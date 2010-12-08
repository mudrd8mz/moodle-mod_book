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
 * Define all the restore tasks
 *
 * @package    mod
 * @subpackage book
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/book/backup/moodle2/restore_book_stepslib.php'); // Because it exists (must)

/**
 * book restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_book_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     *
     * @return void
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     *
     * @return void
     */
    protected function define_my_steps() {
        // Choice only has one structure step
        $this->add_step(new restore_book_activity_structure_step('book_structure', 'book.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     *
     * @return array
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('book', array('intro'), 'book');
        $contents[] = new restore_decode_content('book_chapters', array('content'), 'book_chapter');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     *
     * @return array
     */
    static public function define_decode_rules() {
        $rules = array();

        // List of books in course
        $rules[] = new restore_decode_rule('bookINDEX', '/mod/book/index.php?id=$1', 'course');

        // book by cm->id
        $rules[] = new restore_decode_rule('bookVIEWBYID', '/mod/book/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('bookVIEWBYIDCH', '/mod/book/view.php?id=$1&chapterid=$2', array('course_module', 'book_chapter'));

        // book by book->id
        $rules[] = new restore_decode_rule('bookVIEWBYB', '/mod/book/view.php?b=$1', 'book');
        $rules[] = new restore_decode_rule('bookVIEWBYBCH', '/mod/book/view.php?b=$1&chapterid=$2', array('book', 'book_chapter'));

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * book logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * @return array
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('book', 'update', 'view.php?id={course_module}', '{book}');
        $rules[] = new restore_log_rule('book', 'view', 'view.php?id={course_module}', '{book}');
        $rules[] = new restore_log_rule('book', 'view all', 'view.php?id={course_module}', '{book}');
        $rules[] = new restore_log_rule('book', 'print', 'view.php?id={course_module}', '{book}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     *
     * @return array
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        return $rules;
    }
}
