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
 * This file only schedule a task and redirect back to course.
 *
 * @package    block_course_checker
 * @copyright  2019 Liip SA <elearning@liip.ch>
 */
require_once(__DIR__ . "/../../config.php");

// We must be logged-in, but no permission check is made on this side, as discussed with the client.
require_login();

$PAGE->set_context(context_system::instance());
$courseid = required_param('courseid', PARAM_INT);
$token = required_param('token', PARAM_TEXT);
$checker = optional_param('checker', null, PARAM_TEXT);
if (empty($CFG->disablelogintoken) || false == (bool) $CFG->disablelogintoken) {
    if ($token != \core\session\manager::get_login_token()) {
        print_error("invalidtoken", 'block_course_checker');
    }
}
// Load the course, so we know it exists before scheduling a task.
get_course($courseid);

// The goal is to run the task asynchronously.
$domination = new \block_course_checker\run_checker_task();
// If this is set to 1, no other scheduled task will run at the same time as this task.
$domination->set_blocking(false);

// Pass the course-id to the task.
$data = [
        'course_id' => $courseid
];
// Allow checks to be run only for a specific checker.
if (!empty($checker)) {
    $data["checker"] = $checker;
}

$domination->set_custom_data($data);

// Queue the task.
\core\task\manager::queue_adhoc_task($domination);

// Redirect to referer.
$url = new \moodle_url("/course/view.php", ["id" => $courseid]);
redirect($url);


