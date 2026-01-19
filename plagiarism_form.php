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
 * plagiarism_originality form
 *
 * @package    plagiarism_originality
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * lib functions
 *
 * @package    plagiarism_originality
 * @category   admin
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plagiarism_setup_form extends moodleform {

    /**
     * Define the form elements for the plugin settings.
     */
    public function definition() {
        global $plugin;

        $mform =& $this->_form;

        $mform->addElement('header', 'plagiarism_originalityconfig', get_string('plugin_settings', 'plagiarism_originality'));

        $radioarray = [];
        $radioarray[] =
                $mform->createElement('radio', 'server', '', get_string('production_endpoint', 'plagiarism_originality'), 'live');
        $radioarray[] = $mform->createElement('radio', 'server', '', get_string('test_endpoint', 'plagiarism_originality'), 'test');

        $mform->addElement('static', 'versionrelease', get_string('version'), $plugin->release . '(' . $plugin->version . ')');

        $mform->setDefault('server', 'live');
        $mform->addGroup($radioarray, 'radioar', get_string('plugin_server_type', 'plagiarism_originality'), ['<br /><br />'],
                false);
        $mform->addRule('radioar', null, 'required');
        $mform->setType('plugin_server_type', PARAM_NOTAGS);

        $mform->addElement('passwordunmask', 'secret', get_string('secret', 'plagiarism_originality'), ['size' => '30']);
        $mform->setType('secret', PARAM_NOTAGS);
        $mform->addHelpButton('secret', 'key', 'plagiarism_originality');
        $mform->addRule('secret', null, 'required', null, 'client');

        $mform->addElement('checkbox', 'enabled', get_string('plugin_enabled', 'plagiarism_originality'), get_string('yes'));
        $mform->setDefault('enabled', true);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('checkbox', 'default_use', get_string('default_settings_assignments', 'plagiarism_originality'),
                get_string('yes'));
        $mform->setType('id', PARAM_INT);
        $mform->hideif('default_use', 'enabled', 'notchecked');

        $mform->addElement('checkbox', 'check_ghostwriter', get_string('ghostwriter_enabled', 'plagiarism_originality'),
                get_string('yes'));
        $mform->setType('id', PARAM_INT);
        $mform->addHelpButton('check_ghostwriter', 'check_ghostwriter', 'plagiarism_originality');
        $mform->hideif('check_ghostwriter', 'enabled', 'notchecked');

        $this->add_action_buttons(false);
    }
}
