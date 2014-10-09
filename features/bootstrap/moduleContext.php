<?php

use Features\Bootstrap\eTextBookContext;

/**
 * Features context.
 */
class ModuleContext extends eTextBookContext
{

    /**
     * @When /^Кликаем по ссылке добавить учебник$/
     */
    public function clickAddBookButton()
    {
        $this->findCss('#addBookButton')->click();
        sleep(1);
    }

    /**
     * @Given /^Заполняем форму учебника "([^"]*)", "([^"]*)", "([^"]*)", "([^"]*)"$/
     */
    public function fillBookForm($title, $authors, $editor, $ISBN, $cover)
    {
        $form = $this->findCss('form[name=bookForm]');
        $form->find('css', '#bookTitle')->setValue($title);
        $form->find('css', '#bookAuthors')->setValue($authors);
        $form->find('css', '#bookEditor')->setValue($editor);
        $form->find('css', '#bookISBN')->setValue($ISBN);
        $form->find('css', '#bookCover')->attachFile(dirname(__FILE__) . '/../../fixtures/' . $cover);
        sleep(2);
    }

    /**
     * @Given /^Заполняем форму учебника случайными значениями$/
     */
    public function randomFillBookForm()
    {
        $this->fillBookForm(
            'Title auto-generate by selenium - ' . date('d-m-y-H-i-s'),
            'Author bot, Author bot2',
            'Editor bot',
            '12-3333-221-2221',
            'cover.png'
        );
    }

    /**
     * @Given /^Сохраняем учебник$/
     */
    public function saveETextBook()
    {
        $form = $this->findCss('form[name=bookForm]');
        $book = array(
            'title' => $form->find('css', '#bookTitle')->getValue()
        ,
            'authors' => $form->find('css', '#bookAuthors')->getValue()
        ,
            'editor' => $form->find('css', '#bookEditor')->getValue()
        ,
            'isbn' => $form->find('css', '#bookISBN')->getValue()
        );
        $this->setVar('latestBook', $book);
        $form->find('css', '#bookFormSubmit')->click();
        sleep(3);
    }

    /**
     * @Given /^Проверяем успешное сохранение учебника$/
     */
    public function checkSuccessBookSave()
    {
        $form = $this->findCss('form[name=bookForm]');
        $alertBox = $form->find('css', '#alertBox');
        assertEquals($this->hasClass($alertBox, 'alert-success'), true);
    }

    /**
     * @Then /^Закрываем форму учебника$/
     */
    public function closeBookForm()
    {
        $this->findCss('form[name=bookForm] #bookFormClose')->click();
    }

    /**
     * @Given /^Кликаем по ссылке только что созданного учебника в списке$/
     */
    public function latestBookLinkClick()
    {
        $links = $this->findAllCss('.book-list li');
        $latestBook = $this->getVar('latestBook');
        foreach ($links as $link) {
            $titleLink = $link->find('css', 'a.title');
            if (is_object($titleLink) && trim($titleLink->getHTML()) == $latestBook['title']) {
                $link->find('css', '.edit-link')->click();
                sleep(2);
            }
        }
    }

    /**
     * @Given /^Проверяем на странице редактирования соответствие заголовка последней добавленной книги$/
     */
    public function checkTitleLatestBookOnEditPage()
    {
        $latestBook = $this->getVar('latestBook');
        assertEquals(trim($this->findCss('.page-header h2')->getHTML()), $latestBook['title']);
    }

    /**
     * @Given /^Создаем новый учебник$/
     */
    public function createETextBook()
    {
        $this->clickAddBookButton();
        $this->randomFillBookForm();
        $this->saveETextBook();
        $this->closeBookForm();
        $this->latestBookLinkClick();
    }

    /**
     * @When /^Кликаем по ссылке добавить модуль$/
     */
    public function addModuleLinkClick()
    {
        $this->findCss('#addModuleBtn')->click();
        sleep(1);
    }

    /**
     * @Given /^Заполняем форму модуля "([^"]*)"$/
     */
    public function fillModuleForm($title)
    {
        $form = $this->findCss('form[name=moduleForm]');
        $form->find('css', '#moduleTitle')->setValue($title);
    }

    /**
     * @Given /^Сохраняем модуль$/
     */
    public function saveModule()
    {
        $form = $this->findCss('form[name=moduleForm]');
        $module = array('title' => $form->find('css', '#moduleTitle')->getValue());
        $this->setVar('latestModule', $module);
        $form->find('css', '#moduleFormSubmit')->click();
        sleep(1);
    }

    /**
     * @Given /^Проверяем успешное сохранение модуля$/
     */
    public function checkSuccessSaveModule()
    {
        $form = $this->findCss('form[name=moduleForm]');
        $alertBox = $form->find('css', '#alertBox');
        assertEquals($this->hasClass($alertBox, 'alert-success'), true);
    }

    /**
     * @Then /^Закрываем форму модуля$/
     */
    public function moduleFormClose()
    {
        $this->findCss('form[name=moduleForm] #moduleFormClose')->click();
        sleep(2);
    }

    /**
     * @Given /^Кликаем по ссылке только что созданного модуля в списке$/
     */
    public function latestModuleLinkClick()
    {
        $links = $this->findAllCss('#moduleList li a');
        $latestModule = $this->getVar('latestModule');
        foreach ($links as $key => $link) {
            if (trim($link->getHTML()) == $latestModule['title']) {
                $link->click();
                break;
            }
        }
        sleep(2);
    }

    /**
     * @Given /^Начинаем редактировать текущий модуль$/
     */
    public function startEditCurrentModule()
    {
        $this->elementSetAttribute('module', 'editable', 1);
        $this->findCss('control-panel.module-panel item.edit')->click();
    }

    /**
     * @Given /^Указываем для модуля заголовок "([^"]*)", ключевые вопросы "([^"]*)" и описание "([^"]*)"$/
     */
    public function setDataForModule($title, $keyQuestions, $description)
    {
        $this->findCss('module-title input[type="text"]')->setValue($title);
        $this->findCss('module-questions textarea')->setValue($keyQuestions);
        $this->findCss('module-description textarea')->setValue($description);
    }

    /**
     * @When /^Заканчиваем редактирование текущего модуля$/
     */
    public function finishEditCurrentModule()
    {
        $this->findCss('control-panel.module-panel item.edit')->click();
        sleep(2);
    }

    /**
     * @Given /^Проверяем поля модуля заголовок "([^"]*)", ключевые вопросы "([^"]*)" и описание "([^"]*)"$/
     */
    public function checkLatestModule($title, $questions, $description)
    {

        $display = $this->findCss('.display');

        assertEquals($display->find('css', 'module-title view-element')->getHTML(), $title);
        assertEquals($display->find('css', 'module-questions view-element')->getHTML(), $questions);
        assertEquals($display->find('css', 'module-description view-element')->getHTML(), $description);
    }

    /**
     * @When /^Создаем новый модуль$/
     */
    public function createModule()
    {
        $this->addModuleLinkClick();
        $this->fillModuleForm('Модуль 1');
        $this->saveModule();
        $this->moduleFormClose();
        $this->latestModuleLinkClick();
    }

    /**
     * @Given /^Создаем блок с заголовком "([^"]*)"$/
     */
    public function createBlock($title)
    {
        $desktop = $this->findCss('.desktop');
        $addBlockButton = $desktop->find('css', 'add-block-button');
        $addBlockButton->click();
        sleep(1);
        $addBlockLink = $addBlockButton->find('css', '.add-block');
        $addBlockLink->click();
        $lastBlock = $desktop->find('css', 'block');
        $blockTitleInput = $lastBlock->find('css', 'block-title edit-element input[type="text"]');
        $blockTitleInput->setValue($title);
        $editButton = $lastBlock->find('css', 'control-panel item.edit');
        $editButton->click();
        $this->setVar('latestBlockID', $lastBlock->getAttribute('id'));
    }

    /**
     * @Then /^Проверяем блок с заголовком "([^"]*)"$/
     */
    public function checkBlock($title)
    {
        $display = $this->findCss('.display');
        $block = $display->find('css', 'block');
        $blockTitle = $block->find('css', 'block-headline block-title view-element');
        assertEquals($blockTitle->getHTML(), $title);
    }

    /**
     * @Then /^Удаляем блок$/
     */
    public function removeBlock()
    {
        $desktop = $this->findCss('.desktop');
        $removeButton = $desktop->find('css', 'block control-panel .remove');
        $this->elementSetAttribute('block control-panel', 'style', 'display: block');
        $removeButton->click();
    }

    /**
     * @Then /^Проверяем удаленный блок$/
     */
    public function checkRemovedBlock()
    {
        $display = $this->findCss('.display');
        $block = $display->find('css', 'block');
        assertEquals(false, is_object($block));
    }

    /**
     * @When /^Создаем правило с текстом "([^"]*)"$/
     */
    public function createRule($text)
    {
        $desktop = $this->findCss('.desktop');

        $addBlockButton = $desktop->find('css', 'add-block-button');
        $addBlockButton->click();
        sleep(1);
        $addRuleLink = $addBlockButton->find('css', '.add-rule');
        $addRuleLink->click();
        $rule = $desktop->find('css', 'rule');
        $ruleTextInput = $rule->find('css', 'rule-title .note-editable');
        $ruleTextInput->setValue($text);
        $editButton = $rule->find('css', 'control-panel item.edit');
        $editButton->click();
        $this->setVar('latestRuleID', $rule->getAttribute('id'));
        sleep(1);
    }

    /**
     * @Then /^Проверяем последнее созданное правило с текстом "([^"]*)"$/
     */
    public function checkRule($text)
    {

        $display = $this->findCss('.display');
        $rule = $display->find('css', 'rule[id=' . $this->getVar('latestRuleID') . ']');

        $ruleText = $rule->find('css', 'rule-title view-element');

        assertEquals($ruleText->getText(), $text);
    }

    /**
     * @Given /^Открываем созданный учебник$/
     */
    public function latestBookLinkViewClick()
    {
        $links = $this->findAllCss('.book-list li');
        $latestBook = $this->getVar('latestBook');
        foreach ($links as $link) {
            $titleLink = $link->find('css', 'a.title');
            if (is_object($titleLink) && trim($titleLink->getHTML()) == $latestBook['title']) {
                $link->find('css', '.view-link')->click();
                sleep(2);
            }
        }
    }

    /**
     * @Then /^Кликаем по кнопке содержания$/
     */
    public function openSummary()
    {
        $this->findCss('#show-book-summary')->click();
    }

    /**
     * @Given /^Проверяем заголовок "([^"]*)", ключевые вопросы "([^"]*)" и описание "([^"]*)"$/
     */
    public function checkModuleInEmulator($title, $questions, $description)
    {

        $display = $this->findCss('.e-text-book-viewer');

        assertEquals($display->find('css', '.module .questions')->getHTML(), $questions);
        assertEquals($display->find('css', '.module .description')->getHTML(), $description);
    }


}
