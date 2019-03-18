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

namespace block_course_checker;

defined('MOODLE_INTERNAL') || die();

use block_course_checker\model\check_manager_interface;
use block_course_checker\model\check_plugin_interface;
use block_course_checker\model\check_result_interface;

class plugin_manager implements check_manager_interface {

    const PLUGIN_FILE = 'checker.php';
    const PLUGIN_OUTPUT_FILE = 'renderer.php';
    const PLUGIN_INTERFACE = 'block_course_checker\\model\\check_plugin_interface';
    const PLUGIN_TYPE = "checker";
    const PLUGIN_CLASS = "block_course_checker\checkers\\%s\\checker";
    const PLUGIN_OUTPUT_CLASS = "block_course_checker\\checkers\\%s\\renderer";

    /**
     * A singleton instance of this class.
     *
     * @var \block_course_checker\plugin_manager
     */
    private static $instance;

    /**
     * Force singleton
     */
    protected function __construct() {
    }

    /**
     * Don't allow to clone singleton
     */
    protected function __clone() {
    }

    /**
     * Factory method for this class .
     *
     * @return \block_course_checker\plugin_manager the singleton instance
     */
    public static function instance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Build a list of enabled plugins.
     *
     * @return check_plugin_interface[]
     */
    protected function get_checkers_plugins() {
        static $plugins = [];
        if (!empty($plugins)) {
            return $plugins;
        }
        $pluginroot = $this->get_checkers_folders();

        // Check that directory exists.
        if (!is_dir($pluginroot)) {
            debugging("Unable to open directory " . $pluginroot);
            return [];
        }

        // Iterate over each sub-plugin folder.
        $items = new \DirectoryIterator($pluginroot);
        foreach ($items as $item) {
            if ($item->isDot() or !$item->isDir()) {
                continue;
            }
            $pluginname = $item->getFilename();
            $filelocation = $pluginroot . "/" . $pluginname . "/" . self::PLUGIN_FILE;
            if (false === file_exists($filelocation)) {
                debugging(sprintf("Checker %s has a missing file: %s", $pluginname, $filelocation));
                continue;
            }

            $classname = sprintf(self::PLUGIN_CLASS, $pluginname);
            if (!class_exists($classname, true)) {
                debugging(sprintf("Checker %s has a missing class: %s", $pluginname, $classname));

                continue;
            }
            // Instantiate the plugin.
            $plugins[$pluginname] = new $classname();
        }

        return $plugins;
    }

    /**
     * Get the plugin renderer for a specific check
     *
     * @param string $pluginname plugin name
     * @return abstract_plugin_renderer|null
     */
    public function get_renderer($pluginname) {
        global $PAGE;
        $pluginroot = $this->get_checkers_folders();
        $filelocation = $pluginroot . "/" . $pluginname . "/" . self::PLUGIN_OUTPUT_FILE;
        if (false === file_exists($filelocation)) {
            debugging(sprintf("Checker %s has a missing renderer file: %s", $pluginname, $filelocation));
            return null;
        }

        $classname = sprintf(self::PLUGIN_OUTPUT_CLASS, $pluginname);
        if (!class_exists($classname, true)) {
            debugging(sprintf("Checker %s has a missing class: %s", $pluginname, $classname));
            return null;
        }

        return new $classname($PAGE, RENDERER_TARGET_GENERAL);
    }

    /**
     * @param \stdClass $course
     * @return check_result_interface|array An array of result, indexed with the plugin/check name
     */
    public function run_checks($course) {
        $results = [];
        foreach ($this->get_checkers_plugins() as $pluginname => $plugin) {
            $results[$pluginname] = $plugin->run($course);
        }
        return $results;
    }

    /**
     * Get the folder where checkers must be located.
     *
     * @return string
     */
    private function get_checkers_folders() {
        return __DIR__ . "/checkers";
    }
}