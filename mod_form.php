<?php

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/newsslider/lib.php');

class mod_newsslider_mod_form extends moodleform_mod {

    function definition() {
        global $PAGE;

        $PAGE->force_settings_menu();

        $mform = $this->_form;

        $mform->addElement('header', 'generalhdr', get_string('general'));
        $mform->addElement('text', 'name', get_string('namefield', 'newsslider'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $this->standard_intro_elements(get_string('descriptionfield', 'newsslider'));

        // Label does not add "Show description" checkbox meaning that 'intro' is always shown on the course page.
        $mform->addElement('hidden', 'showdescription', 1);
        $mform->setType('showdescription', PARAM_INT);

        $mform->addElement('text', 'rss_link', get_string('rsslinkfield', 'newsslider'), array('size'=>'1333'));
        $mform->setType('rss_link', PARAM_TEXT);
        $mform->addRule('rss_link', null, 'required');

        $this->standard_coursemodule_elements();

        $this->add_action_buttons(true, false, null);
    }

}

