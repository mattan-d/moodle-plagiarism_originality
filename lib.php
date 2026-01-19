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
 * plagiarism_originality lib
 *
 * @package    plagiarism_originality
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG, $USER;
require_once($CFG->dirroot . '/plagiarism/lib.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/plagiarism/originality/locallib.php');

/**
 * lib functions
 *
 * @package    plagiarism_originality
 * @category   admin
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plagiarism_plugin_originality extends plagiarism_plugin {

    /**
     * @var plagiarism_plugin_originality_utils
     */
    public $utils;

    /**
     * Constructor function for the class.
     * Initializes the plagiarism_plugin_originality_utils object for accessing utility functions.
     *
     * @return void
     */
    public function __construct() {
        $this->utils = new plagiarism_plugin_originality_utils;
    }

    /**
     * Get the links from the given link array.
     *
     * @param array $linkarray An array containing links.
     * @return array An array of links.
     */
    public function get_links($linkarray) {
        global $DB, $USER, $OUTPUT, $CFG, $PAGE;

        $output = new stdClass();
        $output->cmid = $linkarray['cmid'];

        // Set static variables.
        static $cm;
        static $allow;
        if (empty($cm)) {
            $cm = get_coursemodule_from_id('assign', $output->cmid);
            $allow = $DB->get_record('plagiarism_originality_mod', ['cm' => $output->cmid]);
        }

        static $isenabled;
        if (empty($isenabled)) {
            $isenabled = $this->utils->is_enabled();
        }

        $output->userid = $linkarray['userid'];
        $output->file = (isset($linkarray['file']) && is_object($linkarray['file'])) ? $linkarray['file'] : false;
        $output->content = (isset($linkarray['content'])) ? $linkarray['content'] : false;
        $output->cm = $cm;
        $output->allow = $allow;

        // Check if plagiarism detection is enabled, user is a student, course module exists,
        // plagiarism checking is allowed, and user ID is provided.
        if (!has_capability('gradereport/grader:view', $PAGE->context)) {
            return;
        }

        if (!$isenabled || !$output->cm || !$output->allow->ischeck) {
            return;
        }

        if ($output->file) {
            $fileid = $output->file->get_id();
            $filename = $output->file->get_filename();
            $info = pathinfo($filename);

            // Check if the file extension is supported.
            if (!in_array(strtolower($info['extension']), $this->allowed_file_extensions())) {
                $output->html =
                        '<div class="plagiarism-report">' . get_string('originality_unsupported_file', 'plagiarism_originality') .
                        '</div>';
                return $output->html;
            }

            // Submissions with files attached.
            $submissions = $this->utils->get_submission([
                    'cm' => $output->cmid,
                    'assignment' => $output->cm->instance,
                    'userid' => $output->userid,
                    'fileid' => $fileid,
            ]);

        } else if ($output->content) {

            // Submissions with content.
            $token = md5($output->content);

            if ($output->userid == 0 && $token) {
                $groups = $DB->get_records('plagiarism_originality_grp', [
                        'token' => $token,
                        'assignment' => $output->cm->instance,
                ], 'id DESC', 'id, userid', 0, 1);
                if ($groups) {
                    foreach ($groups as $group) {
                        $output->userid = $group->userid;
                    }
                }
            }

            $submissions = $this->utils->get_submission([
                    'cm' => $output->cmid,
                    'assignment' => $output->cm->instance,
                    'userid' => $output->userid,
                    'fileid' => '-1',
            ]);
        }

        if ($submissions) {
            $output->html = '<!-- ' . $output->userid . ' -->';
            foreach ($submissions as $submission) {

                $output->html .= '<div class="plagiarism-report">';

                if ($submission->ghostwriter) {
                    $output->html .= get_string('check_ghostwriter_label', 'plagiarism_originality') . ': ';
                } else {
                    $output->html .= get_string('originality_shortname', 'plagiarism_originality') . ': ';
                }

                if ($submission->status == 2) {
                    $output->pdf = html_writer::link($this->utils->get_file($submission),
                            $OUTPUT->pix_icon('f/pdf', get_string('pdf:filename', 'plagiarism_originality')));

                    if ($submission->grade < 0) {
                        $output->html .= get_string('checking_unprocessable', 'plagiarism_originality');
                    } else if ($submission->grade > 950) {
                        $output->html .= get_string('checking_unprocessable', 'plagiarism_originality') . ' ' . $submission->grade;
                    } else if ($submission->grade == 0) {
                        $output->html .= get_string('checking_unprocessable', 'plagiarism_originality');
                    } else {
                        $output->html .= html_writer::link($this->utils->get_file($submission),
                                round($submission->grade) . '%');
                    }

                    if ($output->html && isset($output->pdf)) {
                        $output->html .= '&nbsp;' . $output->pdf;
                    }
                } else {
                    $output->html .= get_string('checking_inprocessmsg', 'plagiarism_originality') . ' <small>(' .
                            $submission->docid . ')</small>';
                }

                $output->html .= '</div>';
            }
        } else {

            if ($output->userid) {
                $status = $DB->get_record_sql('SELECT s.status, s.assignment
                                FROM {assign_submission} s
                                LEFT JOIN {course_modules} m ON m.instance = s.assignment
                                WHERE m.id = ? AND s.userid = ? AND s.latest = 1', [$output->cmid, $output->userid]);

                if ($status && $status->status == 'draft') {
                    return;
                }

                if ($onlinetext = $DB->get_record('assignsubmission_onlinetext', ['assignment' => $status->assignment])) {
                    if (!$onlinetext->onlinetext) {
                        $status->status = 'empty';
                    }
                }

                if ($status && $status->status == 'submitted') {
                    $output->html = '<div class="plagiarismreport">';
                    $output->html .= get_string('originality_shortname', 'plagiarism_originality') . ': ';
                    $output->html .= get_string('submitted_before_activation', 'plagiarism_originality');
                    $output->html .= '</div>';
                }
            }
        }

        if (isset($output->html)) {
            return $output->html;
        }
    }

    /**
     * Print the disclosure for the given course module ID.
     *
     * @param int $cmid The ID of the course module.
     * @return void
     */
    public function print_disclosure($cmid) {
        global $OUTPUT, $PAGE, $DB;

        $existing = $DB->get_record('plagiarism_originality_mod', ['cm' => $cmid]);
        if ($PAGE->pagetype == 'mod-forum-post' ||
                !$this->utils->is_enabled() || !$existing) {
            return;
        }

        if (isset($existing->ischeck) && !$existing->ischeck) {
            return;
        }

        $output = '';
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;

        $PAGE->requires->js_call_amd('plagiarism_originality/submissions', 'submissions');

        $output .= $OUTPUT->box_start('generalbox boxaligncenter', 'boxoriginalitydisclosure');
        $output .= html_writer::start_div('originality-disclosure');
        $output .= '<span class="disclosure"><div class="checkbox" style="margin-top: 10px; display: flex; align-items: end;">' .
                '<input type="checkbox" id="originality-checkbox" style="margin: 3px;">' .
                '<label for="originality-checkbox" style="margin-bottom: 0; margin: 0 5px;">' .
                get_string('student_disclosure', 'plagiarism_originality') . '</label></div>';
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('core-notification', ['style' => 'display: none;']);
        $output .= '<msg>' . get_string('warning_message', 'plagiarism_originality') . '</msg>';
        $output .= '<btn>' . get_string('continue') . '</btn>';
        $output .= html_writer::end_div();
        $output .= $OUTPUT->box_end();

        return $output;
    }

    /**
     * Get the list of allowed file extensions.
     *
     * @return array An array of allowed file extensions.
     */
    public function allowed_file_extensions() {
        return ['txt', 'rtf', 'doc', 'docx', 'pdf'];
    }

    /**
     * Make a call with the given parameters.
     *
     * @param object $data The data of the call.
     * @return mixed The result of the call.
     */
    public function make_call($data) {
        global $CFG, $DB;

        $user = $DB->get_record('user', ['id' => $data->userid]);
        $data->content = base64_encode($data->content);
        $data->TZhash = md5($user->username);

        $raw = [
                'FileName' => $data->filename,
                'SenderIP' => $CFG->wwwroot,
                'FacultyCode' => $data->facultycode,
                'FacultyName' => $data->facultyname,
                'DeptCode' => $data->deptcode,
                'DeptName' => $data->deptname,
                'CourseCategory' => $data->coursecategory,
                'CourseCode' => $data->courseid,
                'CourseName' => $data->coursename,
                'AssignmentCode' => $data->cmid,
                'MoodleAssignPageNo' => $data->realassignnum,
                'StudentCode' => $data->userid,
                'LecturerCode' => $data->lectid,
                'GroupMembers' => $data->groupmembers,
                'DocSequence' => 1,
                'file' => $data->content,
                'GhostWriterCheck' => $data->ghostwritercheck,
                'LinkMoodleFile' => $data->filepath,
                'TZhash' => $data->TZhash,
        ];

        $url = $this->utils->get_server() . 'documents';
        $jsondata = json_encode($raw);

        $options = [
                'RETURNTRANSFER' => true,
                'CURLOPT_MAXREDIRS' => 10,
                'CURLOPT_TIMEOUT' => 30,

        ];

        $header = [
                'authorization: ' . $this->utils->config->secret,
                'cache-control: no-cache',
                'content-type: application/json',
        ];

        $curl = new curl();
        $curl->setHeader($header);
        $jsonresult = $curl->post($url, $jsondata, $options);
        $output = json_decode($jsonresult);

        if (isset($output->Id)) {
            $context = context_course::instance($data->courseid);
            $event = \plagiarism_originality\event\document_submitted::create([
                    'objectid' => $data->cmid,
                    'context' => $context,
                    'userid' => $data->userid]);
            $event->trigger();

            return $output->Id;
        } else {
            return false;
        }
    }

    /**
     * Handle the group requirement for the given event data.
     *
     * @param mixed $eventdata The data associated with the event.
     * @return void
     */
    public function group_require($eventdata) {
        global $DB;

        $submission = $DB->get_record('assign_submission', ['id' => $eventdata['objectid']]);
        $assign = $DB->get_record('assign', ['id' => $submission->assignment]);

        if ($submission && $assign && $assign->requireallteammemberssubmit) {
            $groupmembers = $DB->get_records('groups_members', ['groupid' => $submission->groupid]);

            $groupmemberids = array_column($groupmembers, 'userid');
            $submittedmemberids =
                    $DB->get_fieldset_sql('SELECT userid FROM {assign_submission} WHERE assignment = ? AND' .
                            'latest = 1 AND' .
                            'status = \'submitted\' AND' .
                            'userid IN (?)',
                            [$submission->assignment, $groupmemberids]);

            if (count($submittedmemberids) != count($groupmembers)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Handle the originality event when a module is created.
     *
     * @param mixed $eventdata The data associated with the event.
     * @return void
     */
    public function originality_event_mod_created($eventdata) {
        $result = true;
        return $result;
    }

    /**
     * Handle the originality event when a module is updated.
     *
     * @param mixed $eventdata The data associated with the event.
     * @return void
     */
    public function originality_event_mod_updated($eventdata) {
        $result = true;
        return $result;
    }

    /**
     * Handle the originality event when a module is deleted.
     *
     * @param mixed $eventdata The data associated with the event.
     * @return void
     */
    public function originality_event_mod_deleted($eventdata) {
        $result = true;
        return $result;
    }

    /**
     * Handle the originality event when a submission is made.
     *
     * @param mixed $eventdata The data associated with the event.
     * @return void
     */
    public function originality_event_submitted($eventdata) {
        if (!$this->group_require($eventdata)) {
            return;
        }

        $this->originality_event_onlinetext_submitted($eventdata);
        $this->originality_event_file_uploaded($eventdata);
    }

    /**
     * Handle the originality event when a file is uploaded.
     *
     * @param mixed $eventdata The data associated with the event.
     * @param bool $resubmission Whether the file upload is a resubmission or not (default: false).
     * @return void
     */
    public function originality_event_file_uploaded($eventdata, $resubmission = false) {
        global $DB, $CFG;
        $result = true;

        $assign = $this->get_assign($eventdata);
        if (!$assign) {
            return $result;
        }

        if (isset($eventdata['eventname']) &&
                $eventdata['eventname'] == '\assignsubmission_file\event\assessable_uploaded') {
            return $result;
        }

        $existing = $DB->get_record('plagiarism_originality_mod', ['cm' => $eventdata['contextinstanceid']]);
        if ($existing && $existing->ischeck) {
            $modulecontext = context_module::instance($eventdata['contextinstanceid']);
            $fs = get_file_storage();

            $params = $this->get_submission_params($eventdata);

            if ($files =
                    $fs->get_area_files($modulecontext->id, 'assignsubmission_file', 'submission_files', $eventdata['objectid'])) {

                foreach ($files as $file) {

                    $filepath = moodle_url::make_file_url($CFG->wwwroot . '/pluginfile.php',
                            '/' . $file->get_contextid() . '/' . $file->get_component() . '/' . $file->get_filearea() . '/' .
                            $file->get_itemid() . '/' . $file->get_filename());
                    $filepath = $filepath->out(true);

                    $fileid = $file->get_id();

                    $filename = trim($file->get_filename());
                    $info = pathinfo($filename);

                    if (!in_array(strtolower($info['extension']), $this->allowed_file_extensions())) {
                        continue;
                    }

                    $filename = $info['filename'];

                    if (strlen($filename) > 40) {
                        $filename = mb_substr($filename, 0, 40);
                    }

                    $filename = $filename . '.' . $info['extension'];
                    $content = $file->get_content();

                    if ($this->utils->is_enabled()) {

                        $params->content = $content;
                        $params->filename = $filename;
                        $params->filepath = $filepath;

                        $types = [];
                        $types[] = 0;

                        if ($params->ghostwritercheck) {
                            $types[] = 1;
                        }

                        $uploadresult = $this->make_call($params);

                        foreach ($types as $type) {
                            if (!$resubmission) {
                                $this->add_document($params->assignnum, $params->userid, $filename,
                                        $fileid, $uploadresult, $type, $eventdata['objectid'], $params->cmid);
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Handle the originality event when an online text submission is made.
     *
     * @param mixed $eventdata The data associated with the event.
     * @param bool $resubmission Whether the online text submission is a resubmission or not (default: false).
     * @return void
     */
    public function originality_event_onlinetext_submitted($eventdata, $resubmission = false) {
        global $DB;
        $result = true;
        $content = '';

        $assign = $this->get_assign($eventdata);
        if (!$assign) {
            return $result;
        }

        if ($eventdata['eventname'] == '\assignsubmission_onlinetext\event\assessable_uploaded') {
            return $result;
        }

        if ($eventdata['eventname'] == '\mod_assign\event\assessable_submitted') {
            $assign = $DB->get_record('assignsubmission_onlinetext', ['submission' => $eventdata['objectid']]);
            if ($assign && $assign->onlinetext) {
                $content = $assign->onlinetext;
                if (!$content) {
                    return;
                }
            }
        } else {
            return $result;
        }

        $existing = $DB->get_record('plagiarism_originality_mod', ['cm' => $eventdata['contextinstanceid']]);
        if ($existing && $existing->ischeck) {

            $params = $this->get_submission_params($eventdata);
            $filename = 'onlinetext-' . $params->userid . '.html';
            if (!$content) {
                return;
            }

            if ($this->utils->is_enabled()) {
                $assignsubmission = $DB->get_record('assign_submission', ['id' => $eventdata['objectid']]);
                if ($assignsubmission) {
                    $data = new stdClass();
                    $data->assignment = $params->assignnum;
                    $data->userid = $params->userid;
                    $data->groupid = $assignsubmission->groupid;
                    $data->token = md5($content);
                    $DB->insert_record('plagiarism_originality_grp', $data);
                }

                $params->content = strip_tags($content);
                $params->filename = $filename;
                $params->filepath = false;

                $uploadresult = $this->make_call($params);
                if (!$resubmission) {
                    $this->add_document($params->assignnum, $params->userid, $filename, -1,
                            $uploadresult, $params->ghostwritercheck, $eventdata['objectid'], $params->cmid);
                }
            }
        }
    }

    /**
     * Handle the originality event when files are processed and done.
     *
     * @param mixed $eventdata The data associated with the event.
     * @return void
     */
    public function originality_event_files_done($eventdata) {
        global $DB;
        $result = true;
    }

    /**
     * Get the parameters for file submission based on the given event data.
     *
     * @param mixed $eventdata The data associated with the event.
     * @return array An array of parameters for file submission.
     */
    private function get_submission_params($eventdata) {
        global $DB, $USER;

        $params = new stdClass();

        $params->cmid = $eventdata['contextinstanceid'];
        $params->courseid = $eventdata['courseid'];
        $params->userid = $USER->id;
        $params->inst = 0;
        $params->facultycode = 100;
        $params->facultyname = 'FacultyName';
        $params->deptcode = 0;
        $params->deptname = 'DepartmentName';
        $params->lectid = false;
        $params->checkfile = '1';
        $params->reserve2 = 'Reserve1';
        $params->groupsize = 1;
        $params->groupmembers = $this->get_group_users($eventdata);

        $course = $DB->get_record('course', ['id' => $params->courseid], '*', MUST_EXIST);
        $tmpcourse = new core_course_list_element($course);
        $coursecontacts = $tmpcourse->get_course_contacts();

        if (!empty($coursecontacts)) {
            $params->lectid = reset($coursecontacts)['user']->id;
        }

        $course = $DB->get_record('course', ['id' => $params->courseid]);
        $coursecategory = $DB->get_record('course_categories', ['id' => $course->category]);

        $params->coursename = $course->fullname;
        $params->coursecategory = $coursecategory->name;

        if (!isset($eventdata['assignNum'])) {
            $cm = $DB->get_record('course_modules', ['id' => $params->cmid, 'course' => $params->courseid]);
            if ($cm) {
                $params->assignnum = $cm->instance;
            }
        } else {
            $params->assignnum = $eventdata['assignNum'];
        }

        $assign = $DB->get_record('assign', ['id' => $params->assignnum], 'id', IGNORE_MISSING);
        $params->realassignnum = ($assign) ? $assign->id : null;

        $existing = $DB->get_record('plagiarism_originality_mod', ['cm' => $params->cmid]);
        $params->ghostwritercheck = ($existing && $existing->ischeckgw) ? 1 : 0;

        return $params;
    }

    /**
     * Get the user ID associated with the submission based on the given event data.
     *
     * @param mixed $eventdata The data associated with the event.
     * @return int The user ID associated with the submission.
     */
    private function get_group_users($eventdata) {
        global $DB;

        if (isset($eventdata['objecttable']) && $eventdata['objecttable'] == 'assign_submission') {
            $userid = $eventdata['userid'];
            [$course, $cm] = get_course_and_cm_from_cmid($eventdata['contextinstanceid'], 'assign');
            $context = context_module::instance($cm->id);
            $assign = new assign($context, $cm, $course);
            $cmod = $assign->get_instance();

            $names = [];
            if ($cmod && $cmod->teamsubmission) {
                $allgroups = $assign->get_all_groups($userid);
                if ($allgroups) {

                    $submissiongroup = $assign->get_submission_group($userid);
                    if ($submissiongroup) {
                        $groupusers = $DB->get_records('groups_members', ['groupid' => $submissiongroup->id]);
                        $userids = array_column($groupusers, 'userid');
                        $users = $DB->get_records_list('user', 'id', $userids);

                        foreach ($users as $user) {
                            array_push($names,
                                    str_replace(' ', '-', $user->firstname) . '~' . str_replace(' ', '-', $user->lastname));
                        }
                    }
                }
            }

            if (!empty($names)) {
                $names = implode(',', $names);
            } else {
                $names = 'None';
            }

            return $names;
        }
    }

    /**
     * Add a request record to the database.
     *
     * @param int $assignnum The assignment number.
     * @param int $userid The user ID of the submitter.
     * @param string $filename The name of the file being submitted.
     * @param int $moodlefileid The Moodle file ID associated with the submitted file.
     * @param int $uploadresult The result of the file upload operation.
     * @param bool $ghostwriter Whether a ghostwriter was involved in the submission.
     * @param int $objectid The ID of the object associated with the submission.
     * @param int $cm The course module ID.
     * @return void
     */
    private function add_document($assignnum, $userid, $filename, $moodlefileid, $uploadresult, $ghostwriter,
            $objectid, $cm) {
        $submission = new stdClass();
        $submission->assignment = $assignnum;
        $submission->userid = $userid;
        $submission->filesubmited = $filename;
        $submission->fileid = $moodlefileid;
        $submission->status = 0;
        $submission->cm = $cm;

        if ($uploadresult) {
            $submission->docid = $uploadresult;
            $submission->status = 1;
        }

        $submission->submit_date = time();
        $submission->created = time();
        $submission->attempts = 1;
        $submission->updated = time();
        $submission->objectid = $objectid;
        $submission->ghostwriter = $ghostwriter;

        $this->utils->set_submission($submission);
    }

    /**
     * Get the assignment associated with the given event data.
     *
     * @param mixed $eventdata The data associated with the event.
     * @return mixed The assignment associated with the event data.
     */
    private function get_assign($eventdata) {
        global $DB, $CFG;
        $assign = false;
        if ($eventdata['contextinstanceid'] && $eventdata['contextinstanceid'] != 1) {
            require_once($CFG->dirroot . '/mod/assign/locallib.php');

            [$course, $cm] = get_course_and_cm_from_cmid($eventdata['contextinstanceid'], 'assign');
            $assign = $DB->get_record('assign', ['id' => $cm->instance]);
            return $assign;
        }
    }

    /**
     * Executes before the standard top of body HTML for the plagiarism originality plugin.
     *
     * @param stdClass $course The course object.
     * @param stdClass $cm The course module object.
     * @return void
     */
    public function plagiarism_originality_before_standard_top_of_body_html($course, $cm) {
    }
}

/**
 * Generate the standard elements for the plagiarism originality course module form.
 *
 * @param stdClass $formwrapper The form wrapper object containing additional form elements.
 * @param mixed $mform The Moodle form object for the plagiarism originality course module.
 * @return void
 */
function plagiarism_originality_coursemodule_standard_elements($formwrapper, $mform) {
    global $DB, $PAGE;

    if ($PAGE->pagetype != 'mod-assign-mod') {
        return;
    }

    $PAGE->requires->js_call_amd('plagiarism_originality/submissions', 'submissions');

    $originality = new plagiarism_plugin_originality();
    $config = $originality->utils->config;

    $add = optional_param('add', '', PARAM_TEXT);
    $cmid = optional_param('update', 0, PARAM_INT);

    if ($config->enabled) {

        if ($cmid) {
            [$course, $cm] = get_course_and_cm_from_cmid($cmid, 'assign');
            $hassubmissions = $DB->get_records('assign_submission', ['assignment' => $cm->instance]);

            if ($hassubmissions) {
                $hassubmissions = get_string('previous_submissions', 'plagiarism_originality');
                $mform->addElement('html',
                        "<div id='assignment_hassubmissions' style='display:block;'>$hassubmissions</div>");
            }
        }

        if (isset($cm)) {
            $existing = $DB->get_record('plagiarism_originality_mod', ['cm' => $cm->id]);
        }

        $mform->addElement('header', 'originalitydesc', get_string('originality', 'plagiarism_originality'));
        $mform->addHelpButton('originalitydesc', 'originality', 'plagiarism_originality');
        $mform->addElement('select', 'originality_use', get_string('plugin_enabled', 'plagiarism_originality'),
                [0 => get_string('no'), 1 => get_string('yes')]);
        $mform->setDefault('originality_use', 0);

        if (isset($existing) && $existing->ischeck) {
            $mform->setDefault('originality_use', 1);
        } else {
            $mform->setDefault('originality_use', 0);
        }

        // When new instance.
        if ($add) {
            if (isset($config->default_use)) {
                $mform->setDefault('originality_use', $config->default_use);
            }
        }

        if (!$originality->utils->subscription()) {
            $mform->addElement('html', '<div style="color: red;" class="originality-message">' .
                    get_string('service_is_inactive', 'plagiarism_originality') . '</div>');
        }

        if ($config->check_ghostwriter) {

            $htmlnotify = '<div style="display: none;" class="core-notification originality-gw-notify">';
            $htmlnotify .= '<msg>' . get_string('ghostwriter_failed_message', 'plagiarism_originality') . '</msg>';
            $htmlnotify .= '<btn>' . get_string('continue') . '</btn>';
            $htmlnotify .= '</div>';
            $mform->addElement('html', $htmlnotify);

            $mform->addElement('header', 'originality_ghostwriter_desc', get_string('check_ghostwriter', 'plagiarism_originality'));
            $mform->addHelpButton('originality_ghostwriter_desc', 'check_ghostwriter', 'plagiarism_originality');
            $mform->addElement('select', 'originality_use_ghostwriter', get_string('ghostwriter_enabled', 'plagiarism_originality'),
                    [0 => get_string('no'), 1 => get_string('yes')]);

            if ($existing && $existing->ischeckgw) {
                $mform->setDefault('originality_use_ghostwriter', 1);
            } else {
                $mform->setDefault('originality_use_ghostwriter', 0);
            }
        }
    }
}

/**
 * Perform post-actions after editing the plagiarism originality course module.
 *
 * @param mixed $data The data submitted for editing the plagiarism originality course module.
 * @param stdClass $course The course object containing information about the course.
 * @return void
 */
function plagiarism_originality_coursemodule_edit_post_actions($data, $course) {
    global $DB;

    $existing = $DB->get_record('plagiarism_originality_mod', ['cm' => $data->coursemodule]);
    if (!$existing) {

        $current = new stdClass();
        $current->cm = $data->coursemodule;
        $current->ischeck = (isset($data->originality_use) ? $data->originality_use : 0);
        $current->ischeckgw = (isset($data->originality_use_ghostwriter) ? $data->originality_use_ghostwriter : 0);

        $DB->insert_record('plagiarism_originality_mod', $current);
    } else {
        $existing->ischeck = (isset($data->originality_use) ? $data->originality_use : 0);
        $existing->ischeckgw = (isset($data->originality_use_ghostwriter) ? $data->originality_use_ghostwriter : 0);
        $DB->update_record('plagiarism_originality_mod', $existing);
    }

    return $data;
}

/**
 * Serve the files from file areas
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function plagiarism_originality_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $USER, $DB;

    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    // Make sure the filearea is one of those
    // used by the plugin.
    if ($filearea == 'reports') {

        // Make sure the user is logged in and has access to
        // the module (plugins that are not course modules
        // should leave out the 'cm' part).
        require_login($course, true, $cm);

        // Check the relevant capabilities - these may vary depending on the filearea being accessed.
        if (!has_capability('gradereport/grader:view', $context)) {
            return false;
        }

        // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
        $itemid = array_shift($args); // The first item in the $args array.

        // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
        // user really does have access to the file in question.

        // Extract the filename / filepath from the $args array.
        $filename = array_pop($args); // The last item in the $args array.
        if (!$args) {
            $filepath = '/';
        } else {
            $filepath = '/' . implode('/', $args) . '/';
        }

        $utils = new plagiarism_plugin_originality_utils;
        if ($itemid) {
            $submissions = $utils->get_submission([
                    'id' => $itemid,
            ]);

            foreach ($submissions as $submission) {

                $stats = new stdClass();

                $stats->userid = $USER->id;
                $stats->timecreated = time();
                $stats->subid = $submission->id;

                $DB->insert_record('plagiarism_originality_stats', $stats);
            }
        }

        // Retrieve the file from the Files API.
        $fs = get_file_storage();
        $file = $fs->get_file($context->id, 'plagiarism_originality', $filearea, $itemid, $filepath, $filename);

        if (!$file) {
            return false; // The file does not exist.
        }

        // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
        send_file($file->get_content(), $file->get_filename(), 0, 0, true, true);
    }
}
