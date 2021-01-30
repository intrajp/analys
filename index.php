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
$dbtype = $CFG->dbtype;
if ($dbtype === 'pgsql') {
    $obj = new \count_sessions();
    $counts = $obj->get_session_count_time_eight_hours_pgsql();
} else if (($dbtype === 'mariadb') || ($dbtype === 'mysql')) { 
    $obj = new \count_sessions();
    $counts = $obj->get_session_count_time_eight_hours_mysql();
} else { 
    echo "Use PostgreSQL!";
    die;
}

$sessions = $obj->get_session_today_eight_hours();

echo $OUTPUT->header();
echo "<a href=\"download.php\">Download a file.</a>";
echo "<br />";
echo "User sessions in recent 8 hours: $counts";
echo "<br />";
echo "<br />";
echo "<table border=1>";
echo "<tr>";
echo "<th>time</th>";
echo "<th>sessions</th>";
echo "</tr>";
foreach ($sessions as $s) {
    echo "<tr>";
    echo "<td>";
    echo date("Y-m-d H:i:s", $s->time);
    echo "</td>";
    echo "<td align=\"center\">";
    echo "$s->sessions";
    echo "</td>";
    echo "</tr>";
}
echo "</table>";
echo $OUTPUT->footer();
