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
 * File containing the general information page.
 *
 * @package     tool_analys
 * @category    admin
 * @copyright   2021 Shintaro Fujiwara <shintaro dot fujiwara at gmail dot com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace  tool_analys;

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->dirroot.'/admin/tool/analys/classes/count_sessions.php');
require_once($CFG->dirroot.'/admin/tool/analys/classes/renderer.php');

if (isguestuser()) {
    throw new \require_login_exception('Guests are not allowed here.');
}

// This is a system level page that operates on other contexts.
require_login();

admin_externalpage_setup('tool_analys');

admin_externalpage_setup('admins');
if (!is_siteadmin()) {
    die;
}

$url = new \moodle_url('/admin/tool/analys/index.php');
$PAGE->set_url($url);
$PAGE->set_title(get_string('analys', 'tool_analys'));
$PAGE->set_heading(get_string('analys', 'tool_analys'));

$returnurl = new \moodle_url('/admin/tool/analys/index.php');

// page parameters
$page    = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);    // how many per page
$sort    = optional_param('sort', 'date', PARAM_ALPHA);
$dir     = optional_param('dir', 'ASC', PARAM_ALPHA); // direction

$obj = new \count_sessions();
$counts = $obj->get_session_count_time_eight_hours();
$sessions_count = $obj->get_session_today_eight_hours_count();
$renderer = $PAGE->get_renderer('tool_analys');
$baseurl = new \moodle_url('index.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));

echo $OUTPUT->header();

echo "<a href=\"download.php\">Download a file of a week.</a>";
echo "<br />";
echo "User sessions in recent 8 hours: $counts";
echo "<br />";
echo "<br />";

echo "Showing ".$sessions_count." session records of today.";
echo $OUTPUT->paging_bar($sessions_count, $page, $perpage, $baseurl);
echo $renderer->show_table($page, $perpage);
echo $OUTPUT->paging_bar($sessions_count, $page, $perpage, $baseurl);

echo $OUTPUT->single_button(new \moodle_url('/admin/tool/analys/assignment_submit_list.php'), get_string('assignmentsubmitlist', 'tool_analys'));

echo $OUTPUT->footer();
