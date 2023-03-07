<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     local_metaskills
 * @category    admin
 * @copyright   2022 Tristan daCosta <tristan.dacosta@edinburghcollege.ac.uk>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/metaskills/lib.php');

$userid = required_param('userid', PARAM_INT); // User ID.


$data = array();
$skills = define_skills();

foreach ($skills as $skill) {
    $skill_arr = array();
    $values = get_metaskills_data($userid, $skill);
    $skill_arr['name'] = $skill;
    $skill_arr['robotname'] = preg_replace("/(\W)+/", "", $skill);
    if ($values) {
        $skill_arr['score'] = $values->score;
        if ($values->oldscore) {
            $skill_arr['oldscore'] =  $values->oldscore;
        }
    }
    array_push($data, $skill_arr);
}
echo json_encode($data);