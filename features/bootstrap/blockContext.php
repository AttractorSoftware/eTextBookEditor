<?php

use Behat\Behat\Context\BehatContext,
    Behat\Mink\Session;

require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';


/**
 * Features context.
 */
class BlockContext extends BehatContext {

    /**
     * @Given /^Создаем блок с заголовком "([^"]*)"$/
     */
    public function createBlock($title) {

        $desktop = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.desktop');

        $addBlockButton = $desktop->find('css', 'add-block-button');
        $addBlockButton->click();
        sleep(1);
        $addBlockLink = $addBlockButton->find('css', 'a.add-block');
        $addBlockLink->click();
        $block = $desktop->find('css', 'block');

        $blockTitleInput = $block->find('css', 'block-title input');
        $blockTitleInput->setValue($title);

        $editButton = $block->find('css', 'control-panel item.edit');

        $editButton->click();

    }

    /**
     * @Then /^Проверяем блок с заголовком "([^"]*)"$/
     */
    public function checkBlock($title) {

        $display = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.display');
        $block = $display->find('css', 'block');

        $blockTitle = $block->find('css', 'block-headline block-title view-element');

        assertEquals($blockTitle->getHTML(), $title);

    }

    /**
     * @Then /^Удаляем блок$/
     */
    public function removeBlock() {

        $desktop = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.desktop');
        $removeButton = $desktop->find('css', 'block control-panel .remove');
        $removeButton->click();

    }

    /**
     * @Then /^Проверяем удаленный блок$/
     */
    public function checkRemovedBlock() {

        $display = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.display');
        $block = $display->find('css', 'block');
        assertEquals(false, is_object($block));

    }

}
