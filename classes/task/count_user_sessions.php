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
 * Implements  task 
 *
 * @package     tool_analys
 * @category    admin
 * @copyright   2021 Shintaro Fujiwara <shintaro dot fujiwara at gmail dot com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_analys\task;

require_once($CFG->dirroot.'/admin/tool/analys/classes/count_sessions.php');

class count_user_sessions extends \core\task\scheduled_task {
    public function get_name(){
        return get_string('taskcountusersessions', 'tool_analys');
    }

    public function execute(){

        \core_php_time_limit::raise(0);//infinite
        \raise_memory_limit(MEMORY_HUGE);
        global $CFG;
        $dbtype = $CFG->dbtype;
        if ( $dbtype === 'pgsql' ) {
            $obj = new \count_sessions();
            $obj->insert_session_count_time_eight_hours_pgsql();
        }
    }
}
