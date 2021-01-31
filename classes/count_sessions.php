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
 * Count user sessions 
 *
 * @copyright   2021 Shintaro Fujiwara <shintaro dot fujiwara at gmail dot com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class count_sessions {

//// methods

    public function get_session_count_time_eight_hours() {

        \core_php_time_limit::raise(0);//infinite
        \raise_memory_limit(MEMORY_HUGE);

        global $CFG;
        global $DB;

        $dbtype = $CFG->dbtype;
        if ($dbtype === 'pgsql') {
            $sessioncounttime = $DB->get_record_sql('SELECT count(userid) as c FROM {sessions}
                                                    WHERE timemodified <= extract(epoch from now())
                                                    - 28800', array());
        } else if (($dbtype === 'mariadb') || ($dbtype === 'mysql')) { 
            $sessioncounttime = $DB->get_record_sql('SELECT count(userid) as c FROM {sessions}
                                                    WHERE timemodified <= now()
                                                    - 28800', array());
        } else { 
            return false;
        }

        return $sessioncounttime->c;

    }

    public function insert_session_count_time_eight_hours() {

        \core_php_time_limit::raise(0);//infinite
        \raise_memory_limit(MEMORY_HUGE);

        global $CFG;
        global $DB;

        $c = $this->get_session_count_time_eight_hours();
        $lapse = '8H';
        $rs = $DB->insert_record('tool_analys_d', ['time' => time(), 'sessions' => $c, 'lapse' => "$lapse"]);

        return true;

    }

    public function get_session_today_eight_hours_count() {

        \core_php_time_limit::raise(0);//infinite
        \raise_memory_limit(MEMORY_HUGE);

        global $DB;

        $begin_of_day = strtotime("today", time());
        $sessions = $DB->get_record_sql("SELECT count(time) as c FROM {tool_analys_d}
                                              WHERE time > $begin_of_day 
                                              AND lapse = '8H'",
                                              array(), $params=null, $limitfrom=0, $limitnum=0);

        return $sessions->c;

    }

    public function get_session_today_eight_hours($offset, $limit, $week_before) {

        \core_php_time_limit::raise(0);//infinite
        \raise_memory_limit(MEMORY_HUGE);

        global $CFG;
        global $DB;

        if ($week_before === 1) {
            $begin_of_day = strtotime("-1 week", time());
            $limit = $limit * 7;
        } else {
            $begin_of_day = strtotime("today", time());
        }

        $dbtype = $CFG->dbtype;
        if ($dbtype === 'pgsql') {
            $sessions = $DB->get_records_sql("SELECT time, sessions, lapse FROM {tool_analys_d}
                                                  WHERE time > $begin_of_day 
                                                  AND lapse = '8H' offset $offset limit $limit",
                                                  array(), $params=null, $limitfrom=0, $limitnum=0);
        } else if (($dbtype === 'mariadb') || ($dbtype === 'mysql')) { 
            $sessions = $DB->get_records_sql("SELECT time, sessions, lapse FROM {tool_analys_d}
                                                  WHERE time > $begin_of_day 
                                                  AND lapse = '8H' ORDER BY time limit $limit offset $offset",
                                                  array(), $params=null, $limitfrom=0, $limitnum=0);
        } 

      return $sessions;

    }

}
