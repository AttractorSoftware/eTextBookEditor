<?php

use Features\Bootstrap\eTextBookContext;

require_once dirname(__FILE__) . '/../../vendor/phpunit/phpunit/PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';


/**
 * Features context.
 */
class WidgetsContext extends eTextBookContext
{

    /**
     * @Given /^Выбираем виджет со значением "([^"]*)"$/
     */
    public function selectWidget($widgetSlug)
    {
        $desktop = $this->findCss('.desktop');
        $driver = $this->getDriver();
        $block = $desktop->find('css', 'block');
        $editButton = $block->find('css', 'control-panel item.edit');
        $editButton->click();
        $block->find('css', '.widget-type-list .item[value=' . $widgetSlug . ']')->click();
    }

    /**
     * @Then /^Добавляем слово "([^"]*)" с переводом "([^"]*)"$/
     */
    public function addTranslateComparativeItem($word, $translate)
    {
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
        sleep(1);
    }

    /**
     * @Given /^Проверяем результат виджета сравнения перевода со словом "([^"]*)" и переводом "([^"]*)"$/
     */
    public function checkTranslateComparativeResult($word, $translate)
    {
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
    public function selectImageClick()
    {

        $desktop = $this->findCss('.desktop');
        $block = $desktop->find('css', 'block');
        $addImageButton = $block->find('css', '.add-image');
        $addImageButton->click();

    }

    /**
     * @Then /^Добавляем логическое выражение "([^"]*)" с значение "([^"]*)"$/
     */
    public function addLogicStatement($title, $value)
    {

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

        sleep(1);

    }

    /**
     * @Given /^Проверяем результат виджета логическое выражение "([^"]*)" со значением "([^"]*)"$/
     */
    public function checkLogicStatement($title, $value)
    {
        $display = $this->findCss('.display');
        $logicStatement = $display->find('css', 'logic-statement');
        $logicStatementItem = $logicStatement->find('css', 'item');
        $logicStatementTitle = $logicStatement->find('css', 'view-element');
        assertEquals($logicStatementTitle->getHTML(), $title);
    }

    /**
     * @Then /^Добавляем окончание "([^"]*)"$/
     */
    public function addEnding($ending)
    {
        $desktop = $this->findCss('.desktop');
        $checkEndings = $desktop->find('css', '.check-endings');
        $addEnding = $checkEndings->find('css', '.endings .add-ending');
        $addEnding->find('css', 'input')->setValue($ending);
        $addEnding->find('css', '.add')->click();
    }

    /**
     * @Then /^Добавляем окончание будущего времени "([^"]*)"$/
     */
    public function addFutureEnding($ending)
    {
        $desktop = $this->findCss('.desktop');
        $checkEndings = $desktop->find('css', '.check-endings.times');
        $addEnding = $checkEndings->find('css', '.endings.future .add-ending');
        $addEnding->find('css', 'input')->setValue($ending);
        $addEnding->find('css', '.add')->click();
    }

    /**
     * @Then /^Добавляем окончание настоящего времени "([^"]*)"$/
     */
    public function addRealEnding($ending)
    {
        $desktop = $this->findCss('.desktop');
        $checkEndings = $desktop->find('css', '.check-endings.times');
        $addEnding = $checkEndings->find('css', '.endings.real .add-ending');
        $addEnding->find('css', 'input')->setValue($ending);
        $addEnding->find('css', '.add')->click();
    }

    /**
     * @Then /^Добавляем окончание прошлого времени "([^"]*)"$/
     */
    public function addPastEnding($ending)
    {
        $desktop = $this->findCss('.desktop');
        $checkEndings = $desktop->find('css', '.check-endings.times');
        $addEnding = $checkEndings->find('css', '.endings.past .add-ending');
        $addEnding->find('css', 'input')->setValue($ending);
        $addEnding->find('css', '.add')->click();
    }

    /**
     * @Given /^Добавляем окончания "([^"]*)"$/
     */
    public function addEndings($endingsStr)
    {
        $endings = explode(', ', $endingsStr);
        for ($i = 0; $i < count($endings); $i++) {
            $this->addEnding($endings[$i]);
        }
    }

    /**
     * @Given /^Добавляем слово "([^"]*)" с окончанием "([^"]*)"$/
     */
    public function addWord($word, $ending)
    {
        $desktop = $this->findCss('.desktop');
        $checkEndings = $desktop->find('css', '.check-endings');
        $addWord = $checkEndings->find('css', '.words .add-word');
        $addWord->find('css', 'input')->setValue($word);
        $addWord->find('css', '.add')->click();
        $words = $checkEndings->findAll('css', '.words .list .item');
        foreach ($words as $w) {
            if ($w->find('css', 'input')->getValue() == $word) {
                $w->find('css', 'select')->setValue($ending);
            }
        }
    }

    /**
     * @Given /^Добавляем слово "([^"]*)" с окончанием будущего времени "([^"]*)" настоящего времени "([^"]*)" и прошлого времени "([^"]*)"$/
     */
    public function addTimesWord($word, $futureEnding, $realEnding, $pastEnding)
    {
        $desktop = $this->findCss('.desktop');
        $checkEndings = $desktop->find('css', '.check-endings.times');
        $addWord = $checkEndings->find('css', '.words .add-word');
        $addWord->find('css', 'input')->setValue($word);
        $addWord->find('css', '.add')->click();
        $words = $checkEndings->findAll('css', '.words .list .item');
        foreach ($words as $w) {
            if ($w->find('css', 'input')->getValue() == $word) {
                $w->find('css', 'select.futureList')->setValue($futureEnding);
                $w->find('css', 'select.realList')->setValue($realEnding);
                $w->find('css', 'select.pastList')->setValue($pastEnding);
            }
        }
    }

    /**
     * @Given /^Завершаем редактирование блока$/
     */
    public function blockEditOver()
    {
        $this->findCss('.desktop block control-panel .edit')->click();
        sleep(2);
    }

    /**
     * @Given /^Проверяем слово "([^"]*)" с окончанием "([^"]*)"$/
     */
    public function checkWordWithEnding($word, $ending)
    {
        $display = $this->findCss('.display');
        $items = $display->findAll('css', '.check-endings .words .list .item');
        $findItem = '';
        foreach ($items as $item) {
            if ($item->find('css', 'view-element')->getHTML() == $word) {
                $findItem = $item;
                $item->find('css', 'select option[value="' . $ending . '"]')->click();
                break;
            }
        }

        assertEquals($findItem->find('css', 'view-element')->getHTML(), $word);
        assertEquals(true, in_array('success', explode(' ', $findItem->getAttribute('class'))));
    }

    /**
     * @Given /^Проверяем слово "([^"]*)" с окончаниями "([^"]*)", "([^"]*)", "([^"]*)"$/
     */
    public function checkWordWithTimesEnding($word, $futureEnding, $realEnding, $pastEnding)
    {
        $display = $this->findCss('.display');
        $items = $display->findAll('css', '.check-endings.times .words .list .item');
        $findItem = '';
        foreach ($items as $item) {
            if ($item->find('css', 'view-element')->getHTML() == $word) {
                $findItem = $item;
                $futureSelect = $item->find('css', 'select[type="future"]');
                $futureSelect->setValue($futureEnding);
                $realSelect = $item->find('css', 'select[type="real"]');
                $realSelect->setValue($realEnding);
                $pastSelect = $item->find('css', 'select[type="past"]');
                $pastSelect->setValue($pastEnding);
                break;
            }
        }

        assertEquals($findItem->find('css', 'view-element')->getHTML(), $word);
        assertEquals(true, in_array('success', explode(' ', $futureSelect->getAttribute('class'))));
        assertEquals(true, in_array('success', explode(' ', $realSelect->getAttribute('class'))));
        assertEquals(true, in_array('success', explode(' ', $pastSelect->getAttribute('class'))));
    }

    /**
     * @Given /^Кликаем по кнопке добавить видео$/
     */
    public function addVideoButtonClick()
    {
        $desktop = $this->findCss('.desktop');
        $desktop->find('css', 'video-list .add-video')->click();
    }

    /**
     * @Given /^Указываем текст для видео "([^"]*)"$/
     */
    public function setTextForVideo($text)
    {
        $desktop = $this->findCss('.desktop');
        $desktop->find('css', 'video-description textarea')->setValue($text);
    }

    /**
     * @Given /^Кликаем по кнопке добавить аудио$/
     */
    public function addAudioButtonClick()
    {
        $desktop = $this->findCss('.desktop');
        $desktop->find('css', 'audio-list .add-audio')->click();
    }

    /**
     * @Given /^Указываем текст для аудио "([^"]*)"$/
     */
    public function setTextForAudio($text)
    {
        $desktop = $this->findCss('.desktop');
        $desktop->find('css', 'edit-element.description textarea')->setValue($text);
    }

    /**
     * @Then /^Добавляем слово "([^"]*)" в виджет вхождения в множество$/
     */
    public function addWordInCheckBoxList($word)
    {
        $word = explode('=', $word);
        $this->findCss('.desktop .checkbox-list .add-checkbox input')->setValue($word[0]);
        $this->findCss('.desktop .checkbox-list .add-checkbox .add')->click();
        if ($word[1] == '1') {
            $inputs = $this->findAllCss('.checkbox-list .checkboxes .item input');
            end($inputs)->click();
        }
    }

    /**
     * @Given /^Добавляем слова "([^"]*)" в виджет вхождения в множество$/
     */
    public function addWordsInCheckBoxList($words)
    {
        foreach (explode(',', $words) as $word) {
            $this->addWordInCheckBoxList($word);
        }
    }

    /**
     * @Given /^Проверяем слово "([^"]*)" в виджете вхождения в множество$/
     */
    public function checkWordInCheckboxList($word)
    {
        $items = $this->findAllCss('.display .checkbox-list .checkboxes .item');
        $word = explode('=', $word);
        foreach ($items as $item) {
            if ($item->find('css', '.title')->getHTML() == $word[0]) {
                $item->find('css', 'input')->click();
                if ($word[1] == '1') {
                    assertEquals(true, in_array('success', explode(' ', $item->getAttribute('class'))));
                } else {
                    assertEquals(true, in_array('failed', explode(' ', $item->getAttribute('class'))));
                }
            }
        }

    }


    /**
     * @Given /^Добавляем вопрос "([^"]*)"/
     */
    public function addQuestionToTest($question)
    {
        $this->findCss('.desktop .test-widget .question input')->setValue($question);
        $this->findCss('.desktop .test-widget .question .add')->click();
    }

    /**
     * @Then ^Добавляем вариант ответа "([^"]*)"$/
     */
    public function addChoiceIntoTestWidget($choice)
    {
        $this->findCss('.desktop .test-widget .add-choice input')->setValue($choice);
        $this->findCss('.desktop .test-widget .add-choice .add')->click();
    }

    /**
     * @Given /^Добавляем варианты ответов "([^"]*)"$/
     */
    public function addChoicesIntoTestWidget($choices)
    {
        foreach (explode(',', $choices) as $choice) {
            $this->addChoiceIntoTestWidget($choice);
        }
    }

    /**
     * @When /^I check the "([^"]*)" radio button$/
     */
    public function iCheckTheRadioButton($labelText, $selector)
    {
//        $selector = '.desktop .test-widget .choices-list label';
        foreach ($this->findAllCss($selector) as $label) {
            if ($labelText === $label->getText()) {
                $radioButton = $this->findCss('#' . $label->getAttribute('for'));
                $radioButton->click();
                return;
            }
        }
        throw new \Exception('Radio button not found');
    }


    /**
     * @Given /^Выбираем правильный ответ "([^"]*)"$/
     */
    public function chooseRightAnswerInTest($choice)
    {
        $selector = '.desktop .test-widget .choices-list label';
        $this->iCheckTheRadioButton($choice, $selector);
    }

    /**
     * @Given /^Проверяем вариант ответа "([^"]*)"$/
     */
    public function checkRightAnswer($choice)
    {
        $selector = '.display .test-widget .choices-list label';
        $this->iCheckTheRadioButton($choice, $selector);
    }

}
