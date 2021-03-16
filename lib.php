<?php

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in Page module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function newsslider_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return false;
        //case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $newsslider
 * @return bool|int
 */
function newsslider_add_instance($newsslider) {
    global $DB, $CFG;

    $newsslider->intro = '
        <div id="newsslider"></div>
        <script src="'.$CFG->wwwroot.'/mod/newsslider/ResizeSensor.js"></script>
        <script>
            $.get("'.$CFG->wwwroot.'/mod/newsslider/newsslider_controller.php/?func=get_newsslider_html&cmid='.$newsslider->coursemodule.'", (response) => {
                $("#newsslider").append(response);
            })
        </script>
    ';
    $newsslider->timemodified = time();

    $id = $DB->insert_record("newsslider", $newsslider);

    $completiontimeexpected = !empty($newsslider->completionexpected) ? $newsslider->completionexpected : null;
    \core_completion\api::update_completion_date_event($newsslider->coursemodule, 'newsslider', $id, $completiontimeexpected);

    return $id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $newsslider
 * @return bool
 */
function newsslider_update_instance($newsslider) {
    global $DB, $CFG;

    $newsslider->intro = '
        <div id="newsslider"></div>
        <script src="'.$CFG->wwwroot.'/mod/newsslider/ResizeSensor.js"></script>
        <script>
            $.get("'.$CFG->wwwroot.'/mod/newsslider/newsslider_controller.php/?func=get_newsslider_html&cmid='.$newsslider->coursemodule.'", (response) => {
                $("#newsslider").append(response);
            })
        </script>
    ';
    $newsslider->timemodified = time();
    $newsslider->id = $newsslider->instance;

    $completiontimeexpected = !empty($newsslider->completionexpected) ? $newsslider->completionexpected : null;
    \core_completion\api::update_completion_date_event($newsslider->coursemodule, 'newsslider', $newsslider->id, $completiontimeexpected);

    return $DB->update_record("newsslider", $newsslider);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id
 * @return bool
 */
function newsslider_delete_instance($id) {
    global $DB;

    if (! $newsslider = $DB->get_record("newsslider", array("id"=>$id))) {
        return false;
    }

    $result = true;

    $cm = get_coursemodule_from_instance('newsslider', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'newsslider', $newsslider->id, null);

    if (! $DB->delete_records("newsslider", array("id"=>$newsslider->id))) {
        $result = false;
    }

    return $result;
}
