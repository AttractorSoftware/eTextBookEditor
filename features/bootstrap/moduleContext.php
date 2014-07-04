<?php

use Behat\Behat\Context\BehatContext;

/**
 * Features context.
 */
class ModuleContext extends BehatContext {

    /**
     * @When /^Указываем название учебника "([^"]*)"$/
     */
    public function setETextBookTitle($title) {
        $bookTitleInput = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '#book-title');
        $bookTitleInput->setValue($title);
    }

    /**
     * @Given /^Сохраняем учебник$/
     */
    public function saveETextBook() {
        $saveButton = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '#save-book-btn');
        $saveButton->click();
        sleep(2);
    }

    /**
     * @When /^Выбираем из выпадающего списка учебник со слагом "([^"]*)"$/
     */
    public function selectViewETextBook($slug) {
        $driver = eTextBookDriver::getInstance()->getDriver();
        $driver->selectOption("//select[@id='book-list']", $slug);
        sleep(2);
    }

    /**
     * @Then /^Проверяем название редактируемого учебника "([^"]*)"$/
     */
    public function checkETextBookTitle($title) {
        $bookTitleInput = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '#book-title');
        assertEquals($title, $bookTitleInput->getValue());
    }

    /**
     * @When /^Создаем модуль с заголовком "([^"]*)", ключевыми вопросами "([^"]*)" и описанием "([^"]*)"$/
     */
    public function createModule($title, $questions, $description) {

        $desktop = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.desktop');

        $addModuleButton = $desktop->find('css', 'add-module-button');
        $addModuleButton->click();
        sleep(1);
        $addModuleLink = $addModuleButton->find('css', 'a.add-module');
        $addModuleLink->click();
        $module = $desktop->find('css', 'module');

        $moduleTitleInput = $module->find('css', 'module-title input');
        $moduleTitleInput->setValue($title);

        $moduleQuestionsInput = $module->find('css', 'module-questions textarea');
        $moduleQuestionsInput->setValue($questions);

        $moduleQuestionsInput = $module->find('css', 'module-description textarea');
        $moduleQuestionsInput->setValue($description);

        $editButton = $module->find('css', 'control-panel item.edit');

        $editButton->click();

        eTextBookDriver::getInstance()->setVar('latestModuleUID', $module->getAttribute('uid'));

    }

    /**
     * @When /^Создаем "([^"]*)" модулей с заголовком "([^"]*)", ключевыми вопросами "([^"]*)" и описанием "([^"]*)"$/
     */
    public function createManyModules($count, $title, $questions, $description) {

        for($i = 0; $i < $count; $i++) {
            $this->createModule($title, $questions, $description);
        }

    }

    /**
     * @Given /^Проверяем последний добавленный модуль с заголовком "([^"]*)", ключевыми вопросами "([^"]*)" и описанием "([^"]*)"$/
     */
    public function checkLatestModule($title, $questions, $description) {

        $display = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.display');
        $module = $display->find('css', 'module[uid='. eTextBookDriver::getInstance()->getVar('latestModuleUID') .']');

        $moduleTitle = $module->find('css', 'module-title view-element');
        $moduleQuestions = $module->find('css', 'module-questions view-element');
        $moduleDescription = $module->find('css', 'module-description view-element');

        assertEquals($moduleTitle->getHTML(), $title);
        assertEquals($moduleQuestions->getHTML(), $questions);
        assertEquals($moduleDescription->getHTML(), $description);
    }

    /**
     * @Then /^Удаляем последний созданный модуль$/
     */
    public function removeLatestModule() {

        $desktop = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.desktop');
        $removeButton = $desktop->find('css', 'module[uid='. eTextBookDriver::getInstance()->getVar('latestModuleUID') .'] control-panel .remove');
        $removeButton->click();

    }

    /**
     * @Then /^Проверяем последний созданный удаленный модуль$/
     */
    public function checkLatestRemovedModule() {

        $display = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.display');
        $module = $display->find('css', 'module[uid='. eTextBookDriver::getInstance()->getVar('latestModuleUID') .']');
        assertEquals(false, is_object($module));

    }

    /**
     * @When /^Создаем правило с текстом "([^"]*)"$/
     */
    public function createRule($text) {
        $desktop = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.desktop');

        $addBlockButton = $desktop->find('css', 'add-block-button');
        $addBlockButton->click();
        sleep(1);
        $addRuleLink = $addBlockButton->find('css', 'a.add-rule');
        $addRuleLink->click();
        $rule = $desktop->find('css', 'rule');

        $ruleTextInput = $rule->find('css', 'rule-title textarea');
        $ruleTextInput->setValue($text);

        $editButton = $rule->find('css', 'control-panel item.edit');

        $editButton->click();

        eTextBookDriver::getInstance()->setVar('latestRuleUID', $rule->getAttribute('uid'));
    }

    /**
     * @Then /^Проверяем последнее созданное правило с текстом "([^"]*)"$/
     */
    public function checkRule($text) {

        $display = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.display');
        $rule = $display->find('css', 'rule[uid='. eTextBookDriver::getInstance()->getVar('latestRuleUID') .']');

        $ruleText = $rule->find('css', 'rule-title view-element');

        assertEquals($ruleText->getHTML(), $text);

    }

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

        eTextBookDriver::getInstance()->setVar('latestBlockUID', $block->getAttribute('uid'));

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
