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
 * Launch config test case.
 *
 * @package    report_allylti
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use report_allylti\local\launch_config;
defined('MOODLE_INTERNAL') || die();

/**
 * Launch config test case.
 *
 * @package    report_allylti
 * @copyright  Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_allylti_launch_config_testcase extends basic_testcase {

    public function setUp() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');
    }

    public function test_config_empty_exception() {
        $this->expectException('\moodle_exception');
        new launch_config(null, 'admin', null);
    }

    public function test_config_no_secret() {
        $config = (object) [
            'adminurl' => 'http://localdev.dev',
            'key' => 'key',
        ];
        $cfg = new stdClass();
        $this->expectException('\moodle_exception');
        new launch_config($config, 'admin', $cfg);
    }

    public function test_config_no_key() {
        $config = (object) [
            'adminurl' => 'http://localdev.dev',
            'secret' => 'secret',
        ];
        $cfg = new stdClass();
        $this->expectException('\moodle_exception');
        new launch_config($config, 'admin', $cfg);
    }

    public function test_config_no_url() {
        $config = (object) [
            'key' => 'key',
            'secret' => 'secret',
        ];
        $cfg = new stdClass();
        $this->expectException('\moodle_exception');
        new launch_config($config, 'admin', $cfg);
    }

    public function test_config_container_override() {
        $config = (object) [
            'adminurl' => 'http://localdev.dev',
            'key' => 'key',
            'secret' => 'secret',
        ];
        // First test the default.
        $cfg = new stdClass();
        $launchconfig = new launch_config($config, 'admin', $cfg);
        $this->assertEquals(LTI_LAUNCH_CONTAINER_EMBED, $launchconfig->get_launchcontainer());

        // Test the override.
        $cfg->report_allylti_launch_container = LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW;
        $launchconfig = new launch_config($config, 'admin', $cfg);
        $this->assertEquals(LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW, $launchconfig->get_launchcontainer());
    }

    public function test_config() {
        $config = (object) [
            'adminurl' => 'http://localdev.dev',
            'key' => 'key',
            'secret' => 'secret',
        ];

        $cfg = new stdClass();
        $launchconfig = new launch_config($config, 'admin', $cfg);
        $url = $launchconfig->get_url();
        $this->assertInstanceOf('\moodle_url', $url);
        $this->assertEquals('http://localdev.dev', $url->out());
        $this->assertEquals('key', $launchconfig->get_key());
        $this->assertEquals('secret', $launchconfig->get_secret());
    }
}
