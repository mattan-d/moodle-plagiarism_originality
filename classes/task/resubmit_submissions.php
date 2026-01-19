<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * plagiarism_originality resubmit documents.
 *
 * @package    plagiarism_originality
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace plagiarism_originality\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/plagiarism/originality/lib.php');
require_once($CFG->dirroot . '/plagiarism/originality/locallib.php');

/**
 * scheduled_task functions
 *
 * @package    plagiarism_originality
 * @category   admin
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class resubmit_submissions extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('stuck_submissions', 'plagiarism_originality');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        $lib = new \plagiarism_plugin_originality();
        $oneweekago = time() - (7 * 24 * 60 * 60); // 7 days ago
        $maxattempts = 3;

        $where = "created >= :createdsince AND docid <= :maxdocid AND attempts <= :maxattempts AND status = :status";
        $params = [
                'createdsince' => $oneweekago,
                'maxdocid' => 0,
                'maxattempts' => $maxattempts,
                'status' => 0
        ];

        $submissions = $DB->get_records_select(
                'plagiarism_originality_sub',
                $where,
                $params,
                'updated DESC',
                '*',
                0,
                10
        );

        $tmp = [];
        if (!$submissions) {
            mtrace('Task: There are no submissions to resubmit.');
        }

        foreach ($submissions as $submission) {

            // Check if maximum attempts (3) has been reached
            if ($submission->attempts >= $maxattempts) {
                mtrace('Task: Submission #' . $submission->id . ' has reached maximum attempts (' . $maxattempts . '). Skipping.');
                continue;
            }

            if (!is_array($tmp[$submission->userid])) {
                $tmp[$submission->userid] = [];
            }

            if (in_array($submission->assignment, $tmp[$submission->userid])) {
                continue;
            }

            array_push($tmp[$submission->userid], $submission->assignment);

            $course = $lib->utils->get_course($submission->assignment);
            if (!$course) {
                mtrace('Task: There is no course associated with this submission #' . $submission->id);
                continue;
            }

            $submissionid = $lib->utils->get_submission_id($submission->assignment, $submission->userid);
            if (!$submissionid) {
                continue;
            }

            $moduleassign = $DB->get_record('modules', ['name' => 'assign']);
            $cm = $DB->get_record('course_modules',
                    ['instance' => $submission->assignment, 'course' => $course->id, 'module' => $moduleassign->id]);

            if (!$cm) {
                continue;
            }

            $eventdata = [];
            $eventdata['eventname'] = '\mod_assign\event\assessable_submitted';
            $eventdata['contextinstanceid'] = $cm->id;
            $eventdata['objectid'] = $submission->objectid;
            $eventdata['courseid'] = $course->id;
            $eventdata['userid'] = $submission->userid;
            $eventdata['assignNum'] = $submission->assignment;

            if ($submission->fileid < 0) {
                $onlinetext = $DB->get_record('assignsubmission_onlinetext',
                        ['assignment' => $submission->assignment, 'submission' => $submissionid[0]]);

                if ($onlinetext) {
                    $eventdata['other']['content'] = $onlinetext->onlinetext;
                }

                $upload = $lib->originality_event_onlinetext_submitted($eventdata, true);
            } else {
                $upload = $lib->originality_event_file_uploaded($eventdata, true);
            }

            // Increment attempts counter
            $submission->attempts++;
            $submission->updated = time();
            if ($upload) {
                $submission->status = 1;
            }

            $lib->utils->update_submission($submission);
            mtrace('Task: Submission #' . $submission->id . ' was resubmitted (Attempt ' . $submission->attempts . '/' . $maxattempts . ').');
        }

        return true;
    }
}
