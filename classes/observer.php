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
 * plagiarism_originality observer.
 *
 * @package    plagiarism_originality
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/plagiarism/originality/lib.php');

/**
 * Observer functions
 *
 * @package    plagiarism_originality
 * @category   admin
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class plagiarism_originality_observer {

    /**
     * Handles the event triggered when a file is uploaded for assessable submission in the File Submission assignment type.
     * This function is responsible for processing the event data and invoking the appropriate functionality to handle the
     * originality check for the uploaded file.
     *
     * @param \assignsubmission_file\event\assessable_uploaded $event The event object containing the uploaded file data.
     * @return void
     */
    public static function assignsubmission_file_uploaded(
            \assignsubmission_file\event\assessable_uploaded $event) {
        $eventdata = $event->get_data();
        $originality = new plagiarism_plugin_originality();
        $originality->originality_event_file_uploaded($eventdata);
    }

    /**
     * Handles the event triggered when an assessable submission is submitted in the Assignment module.
     * This function is responsible for processing the event data and invoking the appropriate functionality to handle the
     * originality check for the submitted assignment.
     *
     * @param \mod_assign\event\assessable_submitted $event The event object containing the submitted assignment data.
     * @return void
     */
    public static function assignsubmission_submitted(
            \mod_assign\event\assessable_submitted $event) {
        $eventdata = $event->get_data();
        $originality = new plagiarism_plugin_originality();
        $originality->originality_event_submitted($eventdata);
    }

    /**
     * Handles the event triggered when online text is uploaded for assessable submission in the Online Text Submission assignment
     * type. This function is responsible for processing the event data and invoking the appropriate functionality to handle the
     * originality check for the uploaded online text.
     * @param \assignsubmission_onlinetext\event\assessable_uploaded $event The event object containing the uploaded online text
     *         data.
     * @return void
     */
    public static function assignsubmission_onlinetext_uploaded(
            \assignsubmission_onlinetext\event\assessable_uploaded $event) {
        $eventdata = $event->get_data();
        $originality = new plagiarism_plugin_originality();
        $originality->originality_event_onlinetext_submitted($eventdata);
    }
}
