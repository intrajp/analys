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

require_once($CFG->dirroot.'/admin/tool/analys/classes/count_sessions.php');

/**
 * Implements the plugin renderer
 *
 * @copyright   2021 Shintaro Fujiwara <shintaro dot fujiwara at gmail dot com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_analys_renderer extends plugin_renderer_base {

//// tables

    public function show_table($page, $perpage) {

        global $CFG;
        $obj = new \count_sessions();
        $sessions = $obj->get_session_today_eight_hours($page*$perpage, $perpage, 0, 1);

        $data = array();

        $table = new html_table();

        $table->head = [
            get_string('sessioncount', 'tool_analys'),
        ];
        $row_top = new html_table_row(array(
            new html_table_cell("date"),
            new html_table_cell("sessions"),
            new html_table_cell("lapse"),
        ));
        $data[] = $row_top;
        foreach ($sessions as $s) {
            $row = array();
            $row[] = date("Y-m-d H:i:s", $s->time);
            $row[] = $s->sessions;
            $row[] = $s->lapse;
            $data[] = $row;
        }
        $table->data = $data;
        $perpage = 1;
        return html_writer::table($table, array('sort' => 'location', 'dir' => 'ASC',
                                      'perpage' => $perpage));

    return true;

    }

}
