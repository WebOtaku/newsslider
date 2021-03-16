<?php

require_once('../../config.php');
require_once('lib.php');

$courseid = required_param('id', PARAM_INT);

if (!$course = $DB->get_record('course', array('id'=> $courseid))) {
    print_error('Course ID is incorrect');
}

$PAGE->set_url('/mod/newsslider/index.php', array('id' => $courseid));

redirect("$CFG->wwwroot/course/view.php?id=$courseid");