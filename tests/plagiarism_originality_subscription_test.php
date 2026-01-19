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
 * plagiarism_originality subscription test
 *
 * @package    plagiarism_originality
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace plagiarism_originality;

use moodle_exception;

/**
 * PHPunit testcase class.
 *
 * @covers \plagiarism_originality_subscription
 */
class plagiarism_originality_subscription_test extends \advanced_testcase {
    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/plagiarism/originality/locallib.php');
    }

    /**
     * Setup before every test.
     */
    public function setUp(): void {
        $this->resetAfterTest();

        // Set fake values so we can test methods in class.
        set_config('enabled', '1', 'plagiarism_originality');
        set_config('server', 'test', 'plagiarism_originality');
        set_config('secret', 'MattanDor20230129', 'plagiarism_originality');

        $this->notfoundmockcurl = new class {
            // @codingStandardsIgnoreStart
            /**
             * Stub for curl setHeader().
             *
             * @param string $unusedparam
             * @return void
             */
            public function setHeader($unusedparam) {
                // @codingStandardsIgnoreEnd
                return;
            }

            /**
             * Stub for curl get_errno().
             *
             * @return boolean
             */
            public function get_errno() {
                return false;
            }

            /**
             * Returns 404 error code.
             *
             * @return array
             */
            public function get_info() {
                return ['http_code' => 404];
            }
        };
    }

    /**
     *
     * Tests the subscription functionality of the Remote_server module in Moodle LMS.
     * Retrieves a subscription using the plagiarism_plugin_originality_utils service and checks its validity.
     */
    public function test_subscription() {
        $service = new \plagiarism_plugin_originality_utils();

        // If not, then it can be used as is.
        $subscription = $service->subscription();
        $this->assertEquals(true, $subscription);
    }

}
