<?php

require_once("../../config.php");
require_once('lib.php');

$cmid = optional_param('id', 0, PARAM_INT);    // Course Module ID

if (!$cm = get_coursemodule_from_id('newsslider', $cmid)) {
    print_error('invalidcoursemodule');
}

if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('coursemisconf');
}

if (!$ns = $DB->get_record('newsslider', array('id' => $cm->instance))) {
    print_error('invalidcoursemodule');
}

$PAGE->set_url('/mod/newsslider/index.php', array('id' => $course->id));

require_login($course, true, $cm);

redirect("$CFG->wwwroot/course/view.php?id=$course->id");