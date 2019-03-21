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
namespace block_course_checker;

defined('MOODLE_INTERNAL') || die();

/**
 * Class run_checker_task
 * See https://docs.moodle.org/dev/Task_API
 *
 * @package block_course_checker
 */
class run_checker_task extends \core\task\adhoc_task {

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        $data = $this->get_custom_data();

        if (!isset($data->course_id)) {
            throw new \RuntimeException("The task should contains custom_data with the course_id");
        }

        // Use the get_course function instead of using get_record('course', ...).
        // See https://docs.moodle.org/dev/Data_manipulation_API#get_course.
        $course = get_course($data->course_id);

        $checksresults = plugin_manager::instance()->run_checks($course);
        result_persister::instance()->save_checks($course->id, $checksresults);
    }
}