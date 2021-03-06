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
 * Settings for checking links inside the course
 *
 * @package    block_course_checker
 * @copyright  2019 Liip SA <elearning@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use block_course_checker\admin\admin_setting_restrictedint;
use block_course_checker\admin\admin_setting_domainwhitelist;
use block_course_checker\checkers\checker_links\config;

/** @var admin_settingpage $setting */
$setting;

// CURL Timeout setting.
$visiblename = get_string('checker_links_setting_timeout', 'block_course_checker');
$timeout = new admin_setting_restrictedint(config::CONNECT_TIMEOUT_SETTING, $visiblename, null,
        config::CONNECT_TIMEOUT_DEFAULT);
$timeout->set_maximum(300)->set_minimum(0);
$setting->add($timeout);

// CURL Connect timeout setting.
$visiblename = get_string('checker_links_setting_connect_timeout', 'block_course_checker');
$timeout = new admin_setting_restrictedint(config::TIMEOUT_SETTING,
        $visiblename, null, config::TIMEOUT_DEFAULT);
$timeout->set_maximum(300)->set_minimum(0);
$setting->add($timeout);

// Link Checker Useragent setting.
$visiblename = get_string('checker_links_setting_useragent', 'block_course_checker');
$description = new lang_string('checker_links_setting_useragent_help', 'block_course_checker');
$useragent = new admin_setting_configtext(config::USERAGENT_SETTING,
        $visiblename, $description, config::USERAGENT_DEFAULT, PARAM_TEXT);
$setting->add($useragent);

// Link Checker Whitelist setting.
$visiblename = get_string('checker_links_setting_whitelist', 'block_course_checker');
$description = get_string('checker_links_setting_whitelist_desc', 'block_course_checker') . ' ' .
        get_string('checker_links_setting_whitelist_help', 'block_course_checker');
$domainwhitelist = new admin_setting_domainwhitelist(config::WHITELIST_SETTING,
        $visiblename, $description, config::WHITELIST_DEFAULT, PARAM_RAW, 600);
$setting->add($domainwhitelist);
