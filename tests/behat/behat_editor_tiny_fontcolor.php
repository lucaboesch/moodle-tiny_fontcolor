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
 * TinyMCE custom steps definitions for the fontcolor plugin.
 *
 * It's basically all what the TinyMCE steps provide, but with this extensions:
 * - New step to select the inner text of an element, e.g. the paragraph without
 *   the paragraph tags.
 * - Menu items can have sub items that need to be clicked as well. In this case
 *   the menu to be clicked is Format -> Language -> some language.
 *
 * @package    tiny_fontcolor
 * @category   test
 * @copyright  2023 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../../behat/behat_base.php');
require_once(__DIR__ . '/../../../../tests/behat/editor_tiny_helpers.php');

/**
 * Extends general TinyMCE test to test the tiny_fontcolor plugin.
 */
class behat_editor_tiny_fontcolor extends behat_base {
    use editor_tiny_helpers;

    /**
     * Click on a button for the specified TinyMCE editor.
     *
     * phpcs:disable
     * @When /^I click on the color menu item "(?P<label_string>(?:[^"]|\\")*)" and choose "(?P<color_string>(?:[^"]|\\")*)" for the "(?P<locator_string>(?:[^"]|\\")*)" TinyMCE editor$/
     * phpcs:enable
     *
     * @param string $label The label of the menu item
     * @param string $color The color to choose from the menu item
     * @param string $locator The locator for the editor
     */
    public function i_click_on_colormenuitem_in_menu(string $label, string $color, string $locator): void {
        $this->require_tiny_tags();
        $container = $this->get_editor_container_for_locator($locator);

        $menubar = $container->find('css', '[role="menubar"]');

        $menus = [trim($label), trim($color)];

        // Open the menu bar.
        $mainmenu = 'Format';
        $this->execute('behat_general::i_click_on_in_the', [$mainmenu, 'button', $menubar, 'NodeElement']);

        foreach ($menus as $menuitem) {
            // Find the menu that was opened.
            $openmenu = $this->find('css', '.tox-selected-menu');

            // Move the mouse to the first item in the list.
            // This is required because WebDriver takes the shortest path to the next click location,
            // which will mean crossing across other menu items.
            $firstlink = $openmenu->find('css', "[role^='menuitem'] .tox-collection__item-icon");
            if ($firstlink) {
                $firstlink->mouseover();
            }

            // Now match by title where the role matches any menuitem, or menuitemcheckbox, or menuitem*.
            $link = $openmenu->find('css', "[title='{$menuitem}'][role^='menuitem']");
            $this->execute('behat_general::i_click_on', [$link, 'NodeElement']);
        }
    }
}
