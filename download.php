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

$dbtype = $CFG->dbtype;
if (($dbtype === 'pgsql') || ($dbtype === 'mariadb') || ($dbtype === 'mysql')) { 
    $obj = new \count_sessions();
    $sessions = $obj->get_session_today_eight_hours(0, 86400, 1);
} else {
    echo "Use PostgreSQL!";
    die;
}

make_temp_directory("tool_analys");

$storage = $CFG->dataroot.'/temp/tool_analys';

$today = date("Y-m-d");
$download_file = "$storage/$today.csv";

$list = array (
    array('date', 'sessions', 'lapse'),
);

foreach ($sessions as $s) {
    $date_day = date("Y-m-d H:i:s", $s->time);
    $list2 = array($date_day,$s->sessions,"8H");
    array_push ($list,$list2);
}

$fp = fopen("$download_file", 'w');

if ($fp) {
    foreach ($list as $fields) {
        fputcsv($fp, $fields);
    }
}

fclose($fp);

//Now let the administrator download a file.
$rs = send_temp_file("$download_file","$today.csv");
