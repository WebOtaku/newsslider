<?php
function xmldb_newsslider_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    $result = TRUE;

    if ($oldversion < 2011070121) {
        // Define table newsslider to be created.
        $table = new xmldb_table('newsslider');

        // Adding fields to table newsslider.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', null, null, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('rss_link', XMLDB_TYPE_CHAR, '1333', null, null, null, null);

        // Adding keys to table newsslider.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table newsslider.
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);

        // Conditionally launch create table for newsslider.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Newsslider savepoint reached.
        upgrade_mod_savepoint(true, 2011070121, 'newsslider');
    }

    return $result;
}
?>