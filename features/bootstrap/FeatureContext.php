<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\Mink\Selector\CssSelector;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;


require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';


/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    public $driver;
    public $page;
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters) {
        $this->driver = new \Behat\Mink\Driver\Selenium2Driver(
            array('firefox', 'http://localhost')
        );
    }

    /**
     * @Given /^Открываем страницу "([^"]*)"$/
     */

    public function openPage($url) {
        $session = new Session($this->driver);
        $session->start();
        $session->visit($url);
        $this->page = $session->getPage();
    }

    /**
     * @When /^Создаем новый модуль с заголовком "([^"]*)", ключевыми вопросами "([^"]*)" и описанием "([^"]*)"$/
     */
    public function createModule($title, $questions, $description) {

        $desktop = $this->page->find('css', '.desktop');

        $addModuleButton = $desktop->find('css', 'add-module-button');
        $addModuleButton->click();
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
     * @Given /^Проверяем новый модуль с заголовком "([^"]*)", ключевыми вопросами "([^"]*)" и описанием "([^"]*)"$/
     */
    public function checkModule($title, $questions, $description) {

        $display = $this->page->find('css', '.display');
        $module = $display->find('css', 'module');

        $moduleTitle = $module->find('css', 'module-title view-element');
        $moduleQuestions = $module->find('css', 'module-questions view-element');
        $moduleDescription = $module->find('css', 'module-description view-element');

        assertEquals($moduleTitle->getText(), $title);
        assertEquals($moduleQuestions->getText(), $questions);
        assertEquals($moduleDescription->getText(), $description);
    }

    /**
     * @Then /^Удаляем новый модуль$/
     */
    public function removeModule() {

        $desktop = $this->page->find('css', '.desktop');
        $removeButton = $desktop->find('css', 'module control-panel .remove');
        $removeButton->click();

    }

    /**
     * @Then /^Проверяем удаленный модуль$/
     */
    public function checkRemovedModule() {

        $display = $this->page->find('css', '.display');
        $module = $display->find('css', 'module');
        assertEquals(false, is_object($module));

    }
}
