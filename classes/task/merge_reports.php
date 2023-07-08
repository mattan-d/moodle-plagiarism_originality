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
 * plagiarism_originality merge old reports.
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
class merge_reports extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('merge_reports', 'plagiarism_originality');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB, $CFG;

        $lib = new \plagiarism_plugin_originality();
        $submissions = $DB->get_records('plagiarism_originality_sub', array('docid' => 0), 'updated DESC', '*', 0, 100);

        if (!$submissions) {
            mtrace('Task: There are no submissions to merge.');
        }

        foreach ($submissions as $submission) {

            if (strpos($submission->filename, 'FilePDF') !== false) {
                // Version 6.2.0.
                $file = $CFG->dataroot . '/originality/' . $submission->assignment . '/' . $submission->filename;
            } else {
                // Version 5.3.9.
                $file = $CFG->dataroot . '/originality/' . $submission->assignment . '/' . $submission->file;
            }

            if (file_exists($file)) {
                $newfile = new \stdClass();
                $newfile->content = file_get_contents($file);
                $newfile->itemid = $submission->id;
                $lib->utils->save_file($newfile);

                $submission->docid = -1;
                $submission->status = 2;
                $submission->attempts = 1;
                $submission->created = time();
                $submission->updated = time();

                $lib->utils->update_submission($submission);

                mtrace('Task: Generation of report #' . $submission->id . ' was completed successfully.');
            }
        }

        return true;
    }
}
