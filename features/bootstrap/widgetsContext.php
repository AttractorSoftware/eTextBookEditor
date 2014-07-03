<?php

use Behat\Behat\Context\BehatContext;

require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';


/**
 * Features context.
 */
class WidgetsContext extends BehatContext {

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

    /**
     * @Given /^Кликаем по кнопке добавления картинки$/
     */
    public function selectImageClick() {

        $desktop = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.desktop');
        $driver = eTextBookDriver::getInstance()->getDriver();
        $block = $desktop->find('css', 'block');
        $addImageButton = $block->find('css', '.add-image');
        $addImageButton->click();

    }

    /**
     * @Given /^Выбираем и загружаем картинку в менеджере файлов$/
     */
    public function uploadImage() {
        $fileManager = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.file-manager');
        $uploadButton = $fileManager->find('css', '#uploadInput');

        $uploadButton->attachFile('C:\\PATH\\TO_FILE.txt');
    }

    /**
     * @Then /^Добавляем логическое выражение "([^"]*)" с значение "([^"]*)"$/
     */
    public function addLogicStatement($title, $value) {

        $desktop = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.desktop');
        $block = $desktop->find('css', 'block');
        $newStatement = $block->find('css', '.new-statement');
        $newStatementTitle = $newStatement->find('css', 'input');
        $addButton = $newStatement->find('css', 'add');
        $newStatementTitle->setValue($title);

        $driver = eTextBookDriver::getInstance()->getDriver();

        $driver->selectOption("//logic-statement/edit-element[@class='new-statement']/select", $value);

        $addButton->click();

        $editButton = $block->find('css', 'control-panel item.edit');
        $editButton->click();

    }

    /**
     * @Given /^Проверяем результат виджета логическое выражение "([^"]*)" со значением "([^"]*)"$/
     */
    public function checkLogicStatement($title, $value) {
        $display = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.display');
        $logicStatement = $display->find('css', 'logic-statement');
        $logicStatementItem = $logicStatement->find('css', 'item');
        $logicStatementTitle = $logicStatement->find('css', 'view-element');
        assertEquals($logicStatementTitle->getHTML(), $title);
    }

}
