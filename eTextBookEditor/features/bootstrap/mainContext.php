<?php

use Behat\Behat\Context\BehatContext;
use Features\Bootstrap\eTextBookDriver;
use Features\Bootstrap\eTextBookContext;

require_once dirname(__FILE__) . '/../../vendor/phpunit/phpunit/PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';

require_once "eTextBookDriver.php";
require_once "moduleContext.php";
require_once "widgetsContext.php";
require_once "fileManagerContext.php";

/**
 * Features context.
 */
class FeatureContext extends eTextBookContext
{

    public function __construct(array $parameters)
    {
        $this->useContext('moduleContext', new ModuleContext());
        $this->useContext('widgetsContext', new WidgetsContext());
        $this->useContext('fileManagerContext', new FileManagerContext());
    }

    /**
     * @AfterSuite
     */
    public static function deleteBooks()
    {
        eTextBookDriver::getInstance()->deleteAutoCreatedBooks();
    }

    /**
     * @AfterFeature
     */
    public static function closePage()
    {
        eTextBookDriver::getInstance()->closeBrowser();
    }

    /**
     * @Given /^Открываем страницу "([^"]*)"$/
     */
    public function openPage($url)
    {
        eTextBookDriver::getInstance()->openPage($url);
        sleep(1);
        $this->getDriver()->resizeWindow(1200, 756);
    }

    /**
     * @Given /^Открываем страницу авторизации$/
     */
    public function openAuthPage()
    {
        eTextBookDriver::getInstance()->openPage("http://localhost");
        sleep(1);
        $this->findCss('#loginLink')->click();
        $this->getDriver()->resizeWindow(1200, 756);
    }

    /**
     * @Given /^Авторизовываемся как "([^"]*)", "([^"]*)"$/
     */
    public function authenticatedAs($username, $password)
    {
        $this->findCss('input#username')->setValue($username);
        $this->findCss('input#password')->setValue($password);
        $this->findCss('#_submit')->click();
        sleep(2);
    }

    /**
     * @Given /^Авторизовываемся как администратор$/
     */
    public function authenticatedAsAdmin()
    {
        $this->findCss('input#username')->setValue("admin");
        $this->findCss('input#password')->setValue("adminadmin");
        $this->findCss('#_submit')->click();
        sleep(2);
    }

    /**
     * @Given /^Закрываем страницу$/
     */
    public function closeBrowser()
    {
        eTextBookDriver::getInstance()->closeBrowser();
    }

    /**
     * @Given /^Ждем "([^"]*)" секунд$/
     */
    public function wait($seconds)
    {
        sleep($seconds);
    }

    /**
     * @Given /^Перезагружаем страницу$/
     */
    public function reload()
    {
        eTextBookDriver::getInstance()->getDriver()->reload();
        sleep(3);
    }

    /**
     * @Given /^Кликаем по ссылке с классом "([^"]*)"$/
     */
    public function clickToLinkWithClass($class)
    {
        $this->findCss('a.' . $class)->click();
    }

    /**
     * @Given /^Перехожу в айфрэйм "([^"]*)"$/
     */
    public function switchToIFrame($name)
    {
        eTextBookDriver::getInstance()->getDriver()->switchToIframe($name);
    }
}
