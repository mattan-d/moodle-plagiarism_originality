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
 * plagiarism_originality backup.
 *
 * @package    plagiarism_originality
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_plagiarism_originality_plugin extends backup_plagiarism_plugin {

    /**
     * Required by Moodle's backup tool to define the plugin structure.
     *
     * @return backup_plugin_element
     * @throws backup_step_exception
     * @throws base_element_struct_exception
     */
    protected function define_module_plugin_structure() {
        $plugin = $this->get_plugin_element();

        $pluginelement = new backup_nested_element($this->get_recommended_name());
        $plugin->add_child($pluginelement);

        // Add module config elements.
        $mods = new backup_nested_element('originality_mods');
        $mod = new backup_nested_element(
                'originality_mod',
                ['id'],
                [
                        'cm', 'ischeck', 'ischeckgw',
                ]
        );

        $subs = new backup_nested_element('originality_subs');
        $sub = new backup_nested_element(
                'originality_sub',
                ['id'],
                [
                        'assignment', 'cm', 'userid', 'docid', 'ghostwriter', 'filesubmited', 'filename', 'fileid',
                        'status', 'grade', 'attempts', 'created', 'updated', 'objectid', 'parent',
                ]
        );

        $pluginelement->add_child($mods);
        $mods->add_child($mod);

        $pluginelement->add_child($subs);
        $subs->add_child($sub);

        $mod->set_source_table('plagiarism_originality_mod', ['cm' => backup::VAR_PARENTID]);
        $sub->set_source_table('plagiarism_originality_sub', ['cm' => backup::VAR_PARENTID]);

        return $plugin;
    }
}
