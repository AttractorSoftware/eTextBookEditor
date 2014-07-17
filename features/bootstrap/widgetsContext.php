<?php

use Features\Bootstrap\eTextBookContext;

require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';


/**
 * Features context.
 */
class WidgetsContext extends eTextBookContext {

    /**
     * @Given /^Выбираем виджет со значением "([^"]*)"$/
     */
    public function selectWidget($widgetTitle) {

        $desktop = $this->findCss('.desktop');
        $driver = $this->getDriver();
        $block = $desktop->find('css', 'block');
        $editButton = $block->find('css', 'control-panel item.edit');
        $editButton->click();
        $driver->selectOption("//select[@class='widget-selector']", $widgetTitle);

    }

    /**
     * @Then /^Добавляем слово "([^"]*)" с переводом "([^"]*)"$/
     */
    public function addTranslateComparativeItem($word, $translate) {
        $desktop = $this->findCss('.desktop');
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
        $display = $this->findCss('.display');
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

        $desktop = $this->findCss('.desktop');
        $block = $desktop->find('css', 'block');
        $addImageButton = $block->find('css', '.add-image');
        $addImageButton->click();

    }

    /**
     * @Then /^Добавляем логическое выражение "([^"]*)" с значение "([^"]*)"$/
     */
    public function addLogicStatement($title, $value) {

        $desktop = $this->findCss('.desktop');
        $block = $desktop->find('css', 'block');
        $newStatement = $block->find('css', '.new-statement');
        $newStatementTitle = $newStatement->find('css', 'input');
        $addButton = $newStatement->find('css', 'add');
        $newStatementTitle->setValue($title);

        $driver = $this->getDriver();

        $driver->selectOption("//logic-statement/edit-element[@class='new-statement']/select", $value);

        $addButton->click();

        $editButton = $block->find('css', 'control-panel item.edit');
        $editButton->click();

    }

    /**
     * @Given /^Проверяем результат виджета логическое выражение "([^"]*)" со значением "([^"]*)"$/
     */
    public function checkLogicStatement($title, $value) {
        $display = $this->findCss('.display');
        $logicStatement = $display->find('css', 'logic-statement');
        $logicStatementItem = $logicStatement->find('css', 'item');
        $logicStatementTitle = $logicStatement->find('css', 'view-element');
        assertEquals($logicStatementTitle->getHTML(), $title);
    }

    /**
     * @Then /^Добавляем окончание "([^"]*)"$/
     */
    public function addEnding($ending) {
        $desktop = $this->findCss('.desktop');
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
        $desktop = $this->findCss('.desktop');
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
        $this->findCss('.desktop block control-panel .edit')->click();
        sleep(1);
    }

    /**
     * @Given /^Проверяем слово "([^"]*)" с окончанием "([^"]*)"$/
     */
    public function checkWordWithEnding($word, $ending) {
        $display = $this->findCss('.display');
        $items = $display->findAll('css', '.check-endings .words .list .item');
        $findItem = '';
        foreach($items as $item) {
            if($item->find('css', 'view-element')->getHTML() == $word) {
                $findItem = $item;
                $item->find('css', 'select option[value="' . $ending . '"]')->click();
                break;
            }
        }

        assertEquals($findItem->find('css', 'view-element')->getHTML(), $word);
        assertEquals(true, in_array('success', explode(' ', $findItem->getAttribute('class'))));
    }

    /**
     * @Given /^Кликаем по кнопке добавить видео$/
     */
    public function addVideoButtonClick() {
        $desktop = $this->findCss('.desktop');
        $desktop->find('css', 'video-list .add-video')->click();
    }

    /**
     * @Given /^Указываем текст для видео "([^"]*)"$/
     */
    public function setTextForVideo($text) {
        $desktop = $this->findCss('.desktop');
        $desktop->find('css', 'video-description textarea')->setValue($text);
    }

    /**
     * @Given /^Кликаем по кнопке добавить аудио$/
     */
    public function addAudioButtonClick() {
        $desktop = $this->findCss('.desktop');
        $desktop->find('css', 'audio-list .add-audio')->click();
    }

    /**
     * @Given /^Указываем текст для аудио "([^"]*)"$/
     */
    public function setTextForAudio($text) {
        $desktop = $this->findCss('.desktop');
        $desktop->find('css', 'audio-description textarea')->setValue($text);
    }

}
