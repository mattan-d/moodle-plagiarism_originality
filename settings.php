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
 * plagiarism_originality settings.
 *
 * @package    plagiarism_originality
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->libdir . '/plagiarismlib.php');
require_once($CFG->dirroot . '/plagiarism/originality/lib.php');
require_once($CFG->dirroot . '/plagiarism/originality/plagiarism_form.php');
require_once($CFG->dirroot . '/plagiarism/originality/version.php');
require_once($CFG->dirroot . '/user/lib.php');

require_login();
admin_externalpage_setup('manageplagiarismplugins');

$context = context_system::instance();
require_capability('moodle/site:config', $context, $USER->id, true, 'nopermissions');

$form = new plagiarism_setup_form();
$settingspage = new moodle_url('/plagiarism/originality/settings.php');

$config = get_config('plagiarism_originality');

if (($data = $form->get_data()) && confirm_sesskey()) {

    if (!isset($data->enabled)) {
        $data->enabled = 0;
    }

    if (!isset($data->default_use)) {
        $data->default_use = 0;
    }

    if (!isset($data->check_ghostwriter)) {
        $data->check_ghostwriter = 0;
    }

    foreach ($data as $key => $value) {
        set_config($key, $value, 'plagiarism_originality');
    }

    // Create external token.
    $service = $DB->get_record('external_services', ['shortname' => 'plagiarism_originality_service']);

    if ($service) {

        if (!$user = $DB->get_record('user', ['idnumber' => 'originalityuser'])) {
            $user = new stdClass();
            $user->firstname = 'originality';
            $user->lastname = 'user';
            $user->idnumber = 'originalityuser';
            $user->username = 'originalityuser';
            $user->email = 'info@originality.world';
            $user->confirmed = true;
            $user->mnethostid = $CFG->mnet_localhost_id;
            $user->id = user_create_user($user, false, false);
        }

        $role = $DB->get_record('role', ['shortname' => 'originality']);
        if (empty($role)) {
            $roleid = create_role('Originality', 'originality', get_string('pluginname', 'plagiarism_originality'), 'originality');
        } else {
            $roleid = $role->id;
        }

        set_role_contextlevels($roleid, [CONTEXT_SYSTEM]);
        assign_capability('plagiarism/originality:manage', CAP_ALLOW, $roleid, $context->id, true);
        assign_capability('webservice/rest:use', CAP_ALLOW, $roleid, $context->id, true);

        accesslib_clear_role_cache($roleid);

        // Role assign.
        role_assign($roleid, $user->id, $context->id);

        // Check if a token has already been created for this user and this service.
        $conditions = [
                'userid' => $user->id,
                'externalserviceid' => $service->id,
                'tokentype' => EXTERNAL_TOKEN_PERMANENT,
        ];

        // Check existing tokens.
        $tokens = $DB->get_record('external_tokens', $conditions);
        if (!$tokens && has_capability('moodle/webservice:createtoken', context_system::instance())) {
            $token = external_generate_token(EXTERNAL_TOKEN_PERMANENT, $service->id, $user->id, \context_system::instance(), 0);
            set_config('wstoken', $token, 'plagiarism_originality');
        } else {
            set_config('wstoken', $tokens->token, 'plagiarism_originality');
        }
    }

    $originality = new plagiarism_plugin_originality();
    if (!$originality->utils->subscription() || !$originality->utils->webserver_token()) {
        redirect($settingspage, get_string('saved_failed', 'plagiarism_originality'), null,
                \core\output\notification::NOTIFY_ERROR);
    } else {
        redirect($settingspage, get_string('plugin_connected', 'plagiarism_originality'), null,
                \core\output\notification::NOTIFY_SUCCESS);
    }
}

echo $OUTPUT->header();
$form->set_data($config);
$form->display();
echo $OUTPUT->footer();
