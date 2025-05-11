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
 * plagiarism_originality restore.
 *
 * @package    plagiarism_originality
 * @category   backup
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_plagiarism_originality_plugin extends restore_plagiarism_plugin {

    /**
     * Defines the module plugin structure for the restore process.
     * This function specifies the paths to be restored for the module plugin.
     *
     * @return array An array of restore_path_element objects representing the paths to be restored.
     */
    protected function define_module_plugin_structure() {
        $paths = [];
        $paths[] = new restore_path_element('originality_mods', $this->get_pathfor('originality_mods/originality_mod'));
        //$paths[] = new restore_path_element('originality_subs', $this->get_pathfor('originality_subs/originality_sub'));

        return $paths;
    }

    /**
     * Processes the originality_mods data during the restore process.
     * This function inserts the originality_mods data into the 'plagiarism_originality_mod' table.
     *
     * @param array $data The originality_mods data to be processed.
     * @return void
     */
    public function process_originality_mods($data) {
        global $DB;

        $data = (object) $data;
        $data->cm = $this->task->get_moduleid();

        $DB->insert_record('plagiarism_originality_mod', $data);
    }

    /**
     * Processes the submission for the originality check in the plagiarism module.
     * This function takes the submitted data and inserts it into the database table 'plagiarism_originality_sub'.
     *
     * @param object $data The data object containing the submission information.
     * @return void
     * @since Moodle 3.1
     */
    public function process_originality_subs($data) {
        global $DB;

        $data = (object) $data;
        $data->cm = $this->task->get_moduleid();

        $DB->insert_record('plagiarism_originality_sub', $data);
    }

    /**
     * Performs actions after the restoration of the module.
     * This function is called after the module has been successfully restored.
     * It can be used to perform any additional actions or cleanup tasks.
     *
     * @return void
     */
    public function after_restore_module() {
    }
}
