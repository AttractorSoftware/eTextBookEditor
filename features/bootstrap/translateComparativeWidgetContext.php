<?php

use Behat\Behat\Context\BehatContext;

require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';


/**
 * Features context.
 */
class TranslateComparativeContext extends BehatContext {

    /**
     * @Given /^Выбираем виджет со значением "([^"]*)"$/
     */
    public function selectWidget($widgetTitle) {

        $desktop = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.desktop');
        $driver = eTextBookDriver::getInstance()->getDriver();
        $block = $desktop->find('css', 'block');
        $editButton = $block->find('css', 'control-panel item.edit');
        $editButton->click();
        $driver->selectOption("//select[@class='widget-selector']", $widgetTitle);

    }

    /**
     * @Then /^Добавляем слово "([^"]*)" с переводом "([^"]*)"$/
     */
    public function addTranslateComparativeItem($word, $translate) {
        $desktop = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.desktop');
        $block = $desktop->find('css', 'block');
        $wordInput = $block->find('css', 'edit-element.new-item .word');
        $translateInput = $block->find('css', 'edit-element.new-item .translate');

        $wordInput->setValue($word);
        $translateInput->setValue($translate);

        $addButton = $block->find('css', 'add');

        $addButton->click();

        $editButton = $block->find('css', 'control-panel item.edit');
        $editButton->click();
    }

    /**
     * @Given /^Проверяем результат виджета сравнения перевода со словом "([^"]*)" и переводом "([^"]*)"$/
     */
    public function checkTranslateComparativeResult($word, $translate) {
        $display = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.display');
        $block = $display->find('css', 'block');

        $wordItem = $block->find('css', 'translate-comparative list item');
        $answerItem = $block->find('css', 'translate-comparative answers item');

        assertEquals($wordItem->getHTML(), $word);
        assertEquals($answerItem->getHTML(), $translate);
    }

}
