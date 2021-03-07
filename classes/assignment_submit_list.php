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
 * Implements the plugin rendering page 
 *
 * @package     tool_analys
 * @category    admin
 * @copyright   2021 Shintaro Fujiwara <shintaro dot fujiwara at gmail dot com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get assignment submit list 
 *
 * @copyright   2021 Shintaro Fujiwara <shintaro dot fujiwara at gmail dot com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_assignment_submit_list {

//// methods

    public function get_assignment_submit_count() {

        \core_php_time_limit::raise(0);//infinite
        \raise_memory_limit(MEMORY_HUGE);

        global $DB;

        $assignments = $DB->get_record_sql("SELECT count(u.username) as counts
                                                FROM {assign} a, {assign_submission} s, {user} u, {course} c
                                                WHERE s.userid = u.id AND s.assignment = a.id AND a.course = c.id
                                           ",
                                              array(), $params=null, $limitfrom=0, $limitnum=0);

        return $assignments->counts;

    }

    public function get_assignment_submit_arr($offset, $limit, $order) {

        \core_php_time_limit::raise(0);//infinite
        \raise_memory_limit(MEMORY_HUGE);

        global $DB;

        if ($order === 0) {
            $order = "ASC";
        } else {
            $order = "DESC";
        }

        $dbtype = $CFG->dbtype;
        if ($dbtype === 'pgsql') {
            $assignments = $DB->get_records_sql("SELECT s.timemodified as timemodifird, u.username as username, u.lastname as lastname,
                                                     c.shortname as shortname, a.name as assignname 
                                                     FROM {assign} a, {assign_submission} s, {user} u, {course} c
                                                     WHERE s.userid = u.id AND s.assignment = a.id AND a.course = c.id
                                                     ORDER BY timemodified $order offset $offset limit $limit 
                                                ",
                                                array(), $params=null, $limitfrom=0, $limitnum=0);
        } else if (($dbtype === 'mariadb') || ($dbtype === 'mysql')) { 
            $assignments = $DB->get_records_sql("SELECT s.timemodified as timemodifird, u.username as username, u.lastname as lastname,
                                                     c.shortname as shortname, a.name as assignname 
                                                     FROM {assign} a, {assign_submission} s, {user} u, {course} c
                                                     WHERE s.userid = u.id AND s.assignment = a.id AND a.course = c.id
                                                     ORDER BY timemodified $order limit $limit offset $offset
                                                ",
                                                array(), $params=null, $limitfrom=0, $limitnum=0);
        } else {
            return $false;
        } 

        return $assignments;

    }

}
