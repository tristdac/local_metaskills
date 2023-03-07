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

define('NO_OUTPUT_BUFFERING', true);

require('../../config.php');
require('lib.php');
require_login();
global $PAGE, $USER, $CFG;

// Load the context
$context = context_system::instance();

// $cid = optional_param('cid', null, PARAM_INT);


$PAGE->set_context($context);
$PAGE->set_url('/local/metaskills/index.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('metaskills','local_metaskills'));
$PAGE->set_heading(get_string('metaskills','local_metaskills'));
$PAGE->requires->js(new \moodle_url('https://cdn.datatables.net/v/bs4/dt-1.11.5/datatables.min.js'), true); 
$PAGE->requires->css(new \moodle_url('https://cdn.datatables.net/v/bs4/dt-1.11.5/datatables.min.css')); 
// $PAGE->requires->js(new \moodle_url($CFG->wwwroot . '/local/metaskills/js/my_datatables.js'));
$PAGE->requires->js(new \moodle_url('https://code.jquery.com/ui/1.13.0/jquery-ui.min.js'), true);
$PAGE->requires->jquery_plugin('ui');

echo $OUTPUT->header(get_string('metaskills','local_metaskills'));

// $data = get_metaskills_data();
// print_object($data);
$data = get_users_students($USER->id, optional_param('cid', null, PARAM_INT), optional_param('level', null, PARAM_INT));

// echo (json_encode($students));
// $students = json_decode(json_encode($students), true);
// print_object($students);
echo $OUTPUT->render_from_template('local_metaskills/form', $data);

// echo get_metaskills_content();

// echo $OUTPUT->notification(notification(), 'info');
// $PAGE->requires->js_call_amd('local_metaskills/main', 'Init', array('3'));
// $PAGE->requires->js_call_amd('local_metaskills/scoring','init');

echo $OUTPUT->footer();


