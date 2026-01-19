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
 * plagiarism_originality EN
 *
 * @package    plagiarism_originality
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Originality - plagiarism detection';
$string['originality'] = 'Originality - document plagiarism detection';
$string['originality_help'] =
        'Enable plagiarism detection for text-based assignments. do not use it for assignments in different languages or for engineering assignments, as the mechanism is not designed for that.';
$string['originality_shortname'] = 'Originality';
$string['plugin_server_type'] = 'Originality server';
$string['plugin_settings'] = 'Originality settings';
$string['plugin_enabled'] = 'Enable plugin';
$string['plugin_connected'] = 'Valid API key, connected to the originality system';
$string['student_disclosure'] =
        "You must mark √ in the appropriate place to submit the assignment for originality check. without marking this, the submission of this assignment will not be possible.<br>this submission is original, it belongs to me, was prepared by me, and I take responsibility for the originality of the content written in it.<br><br>except for the places where I have indicated that the work was done by others and there is a relevant link in the bibliography or in the required place.<br><br>I am aware and agree that this assignment will be checked for literary theft detection by the company originality, and I agree to the <a rel='external' href='https://originality.world/termsOfUseEN.html' target='_blank' style='text-decoration:underline'>terms of use</a>.";
$string['secret'] = 'Access secret';
$string['key'] = 'Access key';
$string['key_help'] = 'To USE THIS PLUGIN, YOU NEED AN ACCESS KEY.';
$string['saved_failed'] = 'Invalid access key entered, the plugin is not active.';
$string['checking_inprocessmsg'] = 'In progress';
$string['checking_unprocessable'] = 'Unprocessable';
$string['submitted_before_activation'] = 'Submitted before plugin activation';
$string['service_is_inactive'] = 'Originality plugin is inactive. please contact your moodle administrator.';
$string['warning_message'] = "You must check the consent box ('I am aware and agree') to enable the submit button.";
$string['previous_submissions'] =
        'Existing submissions already made. these students need to resubmit for their work to be checked for originality.';
$string['production_endpoint'] =
        '<b>production server</b>&nbsp;&nbsp;<span style="font-size:14px;">submit assignments to the originality production server.</span>';
$string['test_endpoint'] =
        '<b>test server</b>&nbsp;&nbsp;<span style="font-size:14px;">submit assignments to the originality test server. select this option only after coordinating with originality.</span>';
$string['check_ghostwriter'] = 'Ghostwriter detection for large assignments';
$string['check_ghostwriter_help'] =
        'You can enable this component only after coordinating with originality. without prior coordination, the component will not function.';
$string['check_ghostwriter_label'] = 'Ghostwriter detection';
$string['ghostwriter_enabled'] = 'Enable ghostwriter detection';
$string['ghostwriter_failed_message'] = 'Ghostwriter detection cannot be performed for online text.';
$string['originality_unsupported_file'] = 'Unsupported file';
$string['default_settings_assignments'] = 'Enable detection for new assignments';
$string['pdf:filename'] = 'View originality report';
$string['document_submitted'] = 'Originality API';
$string['document_submitted_desc'] = 'Text or file content sent to originality';
$string['privacy:metadata:originality:data'] = 'Personal data passed through from subsystem.';
$string['privacy:metadata:originality:externalpurpose'] = 'Physical copy of text or file content sent to originality';
$string['privacy:metadata:plagiarism_originality_sub'] = 'Personal data from subs table';
$string['privacy:metadata:plagiarism_originality_sub:userid'] = 'Personal userid data from subs table';
$string['originality:manage'] = 'Manage web service token';
$string['merge_reports'] = 'Merge old reports files into a new structure';
$string['stuck_submissions'] = 'Attempting to resubmit submissions that are stuck';
