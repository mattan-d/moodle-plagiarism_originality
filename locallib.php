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
 * plagiarism_originality locallib
 *
 * @package    plagiarism_originality
 * @category   admin
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plagiarism_plugin_originality_utils {

    /**
     * @var false|mixed|object|string
     */
    public $config;

    /**
     * Constructor function for the class.
     * Initializes the configuration settings for the plagiarism_originality plugin.
     *
     * @return void
     */
    public function __construct() {
        $this->config = get_config('plagiarism_originality');
    }

    /**
     * Save a file in the file storage.
     *
     * @param object $data The data object containing the file information.
     * @return void
     */
    public function save_file($data) {

        $fs = get_file_storage();
        $context = \context_module::instance($data->cm);

        $fileinfo = [
                'contextid' => $context->id,
                'component' => 'plagiarism_originality',
                'filearea' => 'reports',
                'itemid' => $data->itemid,
                'filepath' => '/',
                'filename' => 'report.pdf'];

        // Get file.
        $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
                $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);

        // Delete it if it exists.
        if ($file) {
            $file->delete();
        }
        $fs->create_file_from_string($fileinfo, $data->content);
    }

    /**
     * Retrieves the URL of a file associated with a specific context and item in the plagiarism_originality component.
     *
     * @param object $data The data object containing the course module information.
     *                     Must have the properties 'cm', 'id' representing context module and item id respectively.
     * @return string|null The URL of the file if found, otherwise null.
     */
    public function get_file($data) {

        global $DB;

        $fs = get_file_storage();

        if (!$data->cm && $data->assignment) {
            $assign = $DB->get_record('assign', ['id' => $data->assignment]);

            if ($assign) {
                $cm = $DB->get_record('course_modules',
                        ['instance' => $data->assignment, 'course' => $assign->course]);
                $data->cm = $cm->id;
            }

            // Update the old file with the current cmid.
            $DB->update_record('plagiarism_originality_sub', $data);
        }

        if (!$data->cm) {
            return false;
        }

        $cm = $DB->get_record('course_modules', ['id' => $data->cm]);
        if (!$cm) {
            return false;
        }

        $context = \context_module::instance($data->cm);
        // Prepare file record object.
        $fileinfo = [
                'contextid' => $context->id,
                'component' => 'plagiarism_originality',
                'filearea' => 'reports',
                'itemid' => $data->id,
                'filepath' => '/',
                'filename' => 'report.pdf'];

        // Get file.
        $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
                $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);

        if ($file) {
            $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                    $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
            return $url;
        }
    }

    /**
     * Check the subscription status by sending a ping request to the server.
     *
     * @return bool Returns true if the subscription is active, false otherwise.
     */
    public function subscription() {
        if (!isset($this->config->secret)) {
            return false;
        }

        $url = $this->get_server() . 'customers/ping';
        $options = [
                'RETURNTRANSFER' => true,
                'CURLOPT_MAXREDIRS' => 10,
                'CURLOPT_TIMEOUT' => 30,
        ];

        $header = [
                'authorization: ' . $this->config->secret,
                'cache-control: no-cache',
        ];

        $curl = new curl();
        $curl->setHeader($header);
        $jsonresult = $curl->get($url, [], $options);
        $output = json_decode($jsonresult, true);

        if (!isset($output)) {
            return false;
        }

        if (isset($output['Pong'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Send a request to the server to obtain a web server token.
     *
     * @return bool Returns true if the token retrieval is successful, false otherwise.
     */
    public function webserver_token() {

        global $CFG;

        if (!isset($this->config->secret)) {
            return false;
        }

        $data = [
                'apiKey' => $this->config->secret,
                'MoodleToken' => $this->config->wstoken,
                'MoodleURL' => $CFG->wwwroot,
                'IsUpdate' => 1,
        ];

        $url = $this->get_server() . 'reports';
        $jsondata = json_encode($data);

        $options = [
                'RETURNTRANSFER' => true,
                'CURLOPT_MAXREDIRS' => 10,
                'CURLOPT_TIMEOUT' => 30,
        ];

        $header = [
                'authorization: ' . $this->config->secret,
                'cache-control: no-cache',
                'content-type: application/json',
        ];

        $curl = new curl();
        $curl->setHeader($header);
        $jsonresult = $curl->post($url, $jsondata, $options);
        $output = json_decode($jsonresult, true);

        if (isset($output['success'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieve the course information for a given assignment ID.
     *
     * @param int $assignmentid The ID of the assignment.
     * @return object|null The course object if found, or null if not found.
     */
    public function get_course($assignmentid) {
        global $DB;
        $assignment = $DB->get_record('assign', ['id' => $assignmentid]);

        if ($assignment) {
            $course = $DB->get_record('course', ['id' => $assignment->course]);
        }

        if ($course) {
            return $course;
        }
    }

    /**
     * Get the submission ID for a given assignment ID and user ID.
     *
     * @param int $assignmentid The ID of the assignment.
     * @param int $userid The ID of the user.
     * @return array An array containing the submission ID if found, or an empty array if not found.
     */
    public function get_submission_id($assignmentid, $userid) {
        global $DB;
        $submission = $DB->get_record('assign_submission', ['assignment' => $assignmentid, 'userid' => $userid], 'id');

        if ($submission) {
            return [$submission->id];
        } else {
            return [];
        }
    }

    /**
     * Get the server URL based on the configuration.
     *
     * @return string The server URL.
     */
    public function get_server() {
        $server = 'http://40.115.61.181/rest/v2/api/';

        if ($this->config->server == 'live') {
            $server = 'https://originality-westeurope-ea-prod-iis.azurewebsites.net/api/';
        }

        return $server;
    }

    /**
     * Check if the feature is enabled based on the configuration.
     *
     * @return bool Returns true if the feature is enabled, false otherwise.
     */
    public function is_enabled() {
        $isenabled = false;
        if ($this->config->enabled) {
            $isenabled = true;
        }

        return $isenabled;
    }

    /**
     * Set the submission data in the database.
     *
     * @param object $data The data object containing the submission information.
     * @return bool|int Returns the status of the insertion (true on success, false on failure), or the inserted record ID.
     */
    public function set_submission($data) {
        global $DB;

        $status = $DB->insert_record('plagiarism_originality_sub', $data);

        return $status;
    }

    /**
     * Get submission data from the database based on the provided criteria.
     *
     * @param array $data An array of criteria for filtering the submissions.
     * @param bool $all Flag to retrieve all submissions or only the latest ones.
     * @return array An array of submission records matching the criteria.
     */
    public function get_submission($data, $all = false) {
        global $DB;

        if (!$all) {
            $data['ghostwriter'] = 0;
            $tmp = $DB->get_records('plagiarism_originality_sub', $data, 'id DESC', '*', 0, 1);

            $data['ghostwriter'] = 1;
            $temp = $DB->get_records('plagiarism_originality_sub', $data, 'id DESC', '*', 0, 1);

            $output = array_merge($tmp, $temp);
        } else {
            $output = $DB->get_records('plagiarism_originality_sub', $data, 'created DESC', '*', 0, 1000);
        }

        return $output;
    }

    /**
     * Update the submission data in the database.
     *
     * @param object $data The data object containing the updated submission information.
     * @return bool|int Returns the status of the update (true on success, false on failure), or the number of affected rows.
     */
    public function update_submission($data) {
        global $DB;
        $status = $DB->update_record('plagiarism_originality_sub', $data);

        return $status;
    }
}
