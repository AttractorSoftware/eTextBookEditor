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

    }

    /**
     * @Given /^Проверяем модуль с заголовком "([^"]*)", ключевыми вопросами "([^"]*)" и описанием "([^"]*)"$/
     */
    public function checkModule($title, $questions, $description) {

        $display = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.display');
        $module = $display->find('css', 'module');

        $moduleTitle = $module->find('css', 'module-title view-element');
        $moduleQuestions = $module->find('css', 'module-questions view-element');
        $moduleDescription = $module->find('css', 'module-description view-element');

        assertEquals($moduleTitle->getHTML(), $title);
        assertEquals($moduleQuestions->getHTML(), $questions);
        assertEquals($moduleDescription->getHTML(), $description);
    }

    /**
     * @Then /^Удаляем модуль$/
     */
    public function removeModule() {

        $desktop = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.desktop');
        $removeButton = $desktop->find('css', 'module control-panel .remove');
        $removeButton->click();

    }

    /**
     * @Then /^Проверяем удаленный модуль$/
     */
    public function checkRemovedModule() {

        $display = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.display');
        $module = $display->find('css', 'module');
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
    }

    /**
     * @Then /^Проверяем правило с текстом "([^"]*)"$/
     */
    public function checkRule($text) {

        $display = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.display');
        $rule = $display->find('css', 'rule');

        $ruleText = $rule->find('css', 'rule-title view-element');

        assertEquals($ruleText->getHTML(), $text);

    }
}
