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
 * @package    block_course_checker
 * @copyright  2019 Liip SA <elearning@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_course_checker\model;

defined('MOODLE_INTERNAL') || die();

/**
 * Interface that every check must implement.
 */
interface check_result_interface extends \renderable, \templatable {

    /**
     * Tels if the check pass successfully or not
     *
     * @return bool
     */
    public function is_successful(): bool;

    /**
     * Return the details of a check
     * This is an array of \stdClass containing:
     * - success: bool Is the check successful
     * - message: string a message description
     * - link: string|null The link to fix this issue or a null string.
     *
     * @return array
     */
    public function get_details(): array;

    /**
     * The link to solve this problem. Or a null string.
     *
     * @return string|null
     */
    public function get_link();

    /**
     * Set the details !
     *
     * @param array $details
     * @return mixed
     */
    public function set_details(array $details = []);
}