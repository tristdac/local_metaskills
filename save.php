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
echo json_encode($_POST);

save_user_scores($_POST);

function save_user_scores($skills) {
    global $DB;

    $user_record = $DB->get_record('user', array('id'=>$_POST->userid));
    $username = $user_record->firstname.' '.$user_record->lastname;
    $table = 'metaskills_scores';

    foreach ($skills as $skill) {
        $record = new stdClass();
        $record->userid  = $skill['userid'];
        $record->skill = $skill['skill'];
        $record->score  = $skill['score'];

        $user_record = $DB->get_record('user', array('id'=>$skill['userid']));
        $username = $user_record->firstname.' '.$user_record->lastname;
        if ($DB->record_exists($table, array('userid'=>$skill['userid'], 'skill'=>$skill['skill']))) { /// user already has a score for this skill
            error_log($username.'('.$skill['userid'].') has a score recorded for '.$skill['skill'].'... lets check if its changed or not...');
            if ($DB->record_exists($table, array('userid'=>$skill['userid'],'skill'=>$skill['skill'],'score'=>$skill['score']))) { /// score is the same - no change - do not update
                error_log($skill['skill'].' score already exists for user '.$username.'('.$skill['userid'].') and hasnt changed... do nothing and skip');
                continue;
            } else {
                error_log($skill['skill'].' score has changed for user '.$username.'('.$skill['userid'].')... lets update the record and move record the old score');
                $existing = $DB->get_record($table, array('userid'=>$skill['userid'],'skill'=>$skill['skill']));
                $record->id = $existing->id;
                $record->oldscore = $existing->score;
                $DB->update_record($table, $record, $bulk=false);
            }
        } else { /// user does not have any score for this skill... let's create a new record
            error_log($username.'('.$skill['userid'].') has no recorded score for '.$skill['skill'].'... we will create new record for this now... unless its a zero value');
            if ($skill['score'] === '0') {
                error_log('It is a zero, so lets do nothing and skip');
                continue;
            } else {
                error_log('This is a real score, so lets create a new record');
                $DB->insert_record($table, $record, $returnid=true, $bulk=false);
            }
        } 
    } 
}   
