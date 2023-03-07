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
 * Plugin administration pages are defined here.
 *
 * @package     local_metaskills
 * @category    admin
 * @copyright   2022 Tristan daCosta <tristan.dacosta@edinburghcollege.ac.uk>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function local_metaskills_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    static $report;
    $course = $course;
    $cm = $cm;

    if ($context->contextlevel != CONTEXT_USER) {
        send_file_not_found();
    }

    $itemid = (int)array_shift($args);
    if ($itemid != 0) {
        send_file_not_found();
    }

    $relativepath = implode('/', $args);

    $fullpath = "/{$context->id}/local_metaskills/$filearea/$itemid/$relativepath";

    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        send_file_not_found();
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}


/**
 * Adding learning program link in sidebar
 * @param navigation_node $nav navigation node
 */
function local_metaskills_extend_navigation(navigation_node $nav) {
    global $CFG, $PAGE, $COURSE;

    // Check if users is logged in to extend navigation.
    if (!isloggedin()) {
        return;
    }

    $hasblocks = is_block_present_indashboard();

    // During the installation save the capability.
    // $PAGE->requires->js_call_amd('local_metaskills/install', 'init');

    if ($hasblocks) {
        $icon = new pix_icon('i/calendareventdescription', '');

        $node = $nav->add(
            get_string('metaskills', 'local_metaskills'),
            new moodle_url($CFG->wwwroot . '/local/metaskills/index.php'),
            navigation_node::TYPE_CUSTOM,
            'metaskills',
            'metaskills',
            $icon
        );
        $node->showinflatnavigation = true;
    }

    // If url is not set.
    if (!$PAGE->has_set_url()) {
        return true;
    }

    $iscompletionpage = strpos($PAGE->url, '/local/metaskills/completion.php');
    if ($PAGE->pagelayout !== 'course' && $PAGE->pagelayout !== 'incourse' && !$iscompletionpage) {
        return true;
    }

    if (!has_capability('moodle/course:viewhiddencourses', context_course::instance($COURSE->id))) {
        return;
    }

    $icon = new pix_icon('i/report', '');

    $node = $nav->add(
        get_string('metaskills', 'local_metaskills'),
        new moodle_url($CFG->wwwroot . '/local/metaskills/completion.php', array('courseid' => $COURSE->id)),
        navigation_node::TYPE_CUSTOM,
        'metaskills',
        'metaskills',
        $icon
    );
    $node->showinflatnavigation = true;
}

function get_users_students($userid, $referrer, $level): array {

    //Get courses of current user
    $courses = enrol_get_users_courses($userid, true, 'id, visible, shortname');
    $students = array();
    $studentholder = array();
    $usedids = array();
    $traits = array();
    $usercourses = array();

    foreach ($courses as $courseid => $course){
        $coursecontext = context_course::instance($course->id);
        // Get Enrolled users
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
        $usercourses[] = $course;
        foreach ($enrolledstudents as $user) {
            if (strpos($course->shortname, '-') !== false) { 
                $user->course = (array)$course;
                unset($user->course->category);
            } else {
                continue;
            }
            // $traits = lookup_trait_ratings($user->id);
            // $user->traits = $traits;
            $studentid = $user->id;
            if (!in_array($studentid, $usedids)) {
                array_push($students, $user);
                $usedids[] = $user->id;
            }
        }
    }
    $studentholder = ['students' => (array)$students];
    if ($referrer) {
        $studentholder['referrer'] = (array)get_referrer_course($referrer);
    }
    if ($level) {
        $studentholder['level'] = (array)$level;
    }
    $studentholder['courses'] = $usercourses;
    return $studentholder;
    
}

function get_metaskills_data($userid,$skill) {
    global $DB;
    $score = $DB->get_record('metaskills_scores',array('userid'=>$userid, 'skill'=>$skill));
    return $score;
}

function get_referrer_course($cid) {
    $course = get_course($cid);
    return $course;
}

function get_user_info($userid) {
    global $DB;
    $user = $DB->get_record('user',array('id'=>$userid));
    return $user;
}

function define_skills() {
    $skills = array(
        'Curiosity',
        'Creativity',
        'Collaboration',
        'Critical Thinking',
        'Leading',
        'Resilience',
        'Empathy',
        'Initiative',
        'Integrity',
        'Ethics',
        'Adaptability',
        'Using Data',
        'Sense-making',
        'Feelings/Emotions',
        'Morality',
        'Communication',
        'Global & Cultural Awareness',
        'Self-management',
        'Self-regulation',
        'Self-awareness',
        'Self-efficacy',
        'Innovation');
    return $skills;
}
