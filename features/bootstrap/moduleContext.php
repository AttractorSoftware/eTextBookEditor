<?php

use Behat\Behat\Context\BehatContext;

/**
 * Features context.
 */
class ModuleContext extends BehatContext {

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
}
