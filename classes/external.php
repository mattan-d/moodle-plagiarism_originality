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
 * plagiarism_originality external webservice
 *
 * @package    plagiarism_originality
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/plagiarism/originality/locallib.php');

/**
 * External functions
 *
 * @package    plagiarism_originality
 * @category   external
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class plagiarism_originality_external extends external_api {

    /**
     * Returns the expected parameters for the create_report function.
     * This function defines the expected parameters for the create_report function. It specifies the data types and descriptions
     * of each parameter.
     *
     * @return external_function_parameters The parameters expected by the create_report function.
     */
    public static function create_report_parameters() {
        return new external_function_parameters([
                'docId' => new external_value(PARAM_ALPHANUM, 'A document ID value for ordering the entries.'),
                'content' => new external_value(PARAM_RAW, 'The main content of the entry.'),
                'grade' => new external_value(PARAM_INT, 'The grade or rating associated with the entry.'),
                'type' => new external_value(PARAM_INT, 'The type report associated with the entry.', false, 0),
        ]);
    }

    /**
     * Creates a report for a document.
     * This function creates a report for a document based on the provided parameters. It validates the parameters, checks the
     * user's context and capability, and performs necessary database operations to update the submission and save the file. The
     * resulting report is returned as an output object.
     *
     * @param int $docid The ID of the document for which the report is created.
     * @param string $content The content of the report.
     * @param int $grade The grade assigned to the document.
     * @param int $type The type report associated with the entry.
     * @return stdClass The output object containing the created report information.
     */
    public static function create_report($docid, $content, $grade, $type) {
        global $DB;

        $params = self::validate_parameters(self::create_report_parameters(), [
                'docId' => $docid,
                'content' => $content,
                'grade' => $grade,
                'type' => $type,
        ]);

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('plagiarism/originality:manage', $context);

        $output = new stdClass();
        $output->utils = new plagiarism_plugin_originality_utils;
        $output->error = false;

        $submission = $DB->get_record('plagiarism_originality_sub', [
                'docid' => $params['docId'],
                'ghostwriter' => $params['type'],
        ]);

        if (!$submission) {
            $output->error = true;
        } else {
            unset($submission->filesubmited);
            $submission->status = 2;
            $submission->updated = time();
            $submission->grade = $params['grade'];

            $output->utils->update_submission($submission);

            $file = new stdClass();
            $file->content = base64_decode($params['content']);
            $file->itemid = $submission->id;
            $file->cm = $submission->cm;

            $output->utils->save_file($file);
        }

        return $output;
    }

    /**
     * Returns the expected structure of the output for the create_report function.
     * This function defines the structure of the output that the create_report function will return. It indicates whether an error
     * occurred during the operation.
     *
     * @return external_single_structure The structure of the output, including the error field.
     */
    public static function create_report_returns() {
        return new external_single_structure(
                [
                        'error' => new external_value(PARAM_BOOL, 'Indicates whether an error occurred during the operation.',
                                true),
                ],
        );
    }
}
