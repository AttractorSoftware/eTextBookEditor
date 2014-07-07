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

        $uploadButton->attachFile(dirname(__FILE__).'/../../fixtures/img/shoes.png');

        $fileManager->find('css', '#images .list .item')->click();
        $fileManager->find('css', '#images .player .buttons .select')->click();
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

    /**
     * @Then /^Добавляем окончание "([^"]*)"$/
     */
    public function addEnding($ending) {
        $desktop = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.desktop');
        $checkEndings = $desktop->find('css', '.check-endings');
        $addEnding = $checkEndings->find('css', '.endings .add-ending');
        $addEnding->find('css', 'input')->setValue($ending);
        $addEnding->find('css', '.add')->click();
    }

    /**
     * @Given /^Добавляем окончания "([^"]*)"$/
     */
    public function addEndings($endingsStr){
        $endings = explode(', ', $endingsStr);
        for($i = 0; $i < count($endings); $i++ ) {
            $this->addEnding($endings[$i]);
        }
    }

    /**
     * @Given /^Добавляем слово "([^"]*)" с окончанием "([^"]*)"$/
     */
    public function addWord($word, $ending) {
        $desktop = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.desktop');
        $checkEndings = $desktop->find('css', '.check-endings');
        $addWord = $checkEndings->find('css', '.words .add-word');
        $addWord->find('css', 'input')->setValue($word);
        $addWord->find('css', '.add')->click();
        $words = $checkEndings->findAll('css', '.words .list .item');
        foreach($words as $w) {
            if($w->find('css', 'input')->getValue() == $word) {
                $w->find('css', 'select')->setValue($ending);
            }
        }
    }

    /**
     * @Given /^Завершаем редактирование блока$/
     */
    public function blockEditOver() {
        eTextBookDriver::getInstance()
            ->getCurrentPage()
            ->find('css', '.desktop block control-panel .edit')->click();
    }

    /**
     * @Given /^Проверяем слово "([^"]*)" с окончанием "([^"]*)"$/
     */
    public function checkWordWithEnding($word, $ending) {
        $display = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.display');
        $items = $display->findAll('css', '.check-endings .words .list .item');
        $findItem = '';
        foreach($items as $item) {
            if($item->find('css', 'view-element')->getHTML() == $word) {
                $findItem = $item;
                $item->find('css', 'select option[value="' . $ending . '"]')->click();
            }
        }

        assertEquals($findItem->find('css', 'view-element')->getHTML(), $word);
        assertEquals(true, in_array('success', explode(' ', $findItem->getAttribute('class'))));
    }

}