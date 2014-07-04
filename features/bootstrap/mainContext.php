<?php

use Behat\Behat\Context\BehatContext;

require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';

require_once "eTextBookContext.php";
require_once "moduleContext.php";
require_once "widgetsContext.php";

/**
 * Features context.
 */
class FeatureContext extends BehatContext {

    public function __construct(array $parameters) {
        $this->useContext('moduleContext', new ModuleContext());
        $this->useContext('widgetsContext', new WidgetsContext());
    }

    /**
     * @AfterFeature
     */
    public static function closePage() {
        //eTextBookDriver::getInstance()->closeBrowser();
    }

    /**
     * @Given /^Открываем страницу "([^"]*)"$/
     */
    public function openPage($url) {
        eTextBookDriver::getInstance()->openPage($url);
    }

    /**
     * @Given /^Закрываем страницу$/
     */
    public function closeBrowser() {
        eTextBookDriver::getInstance()->closeBrowser();
    }
}
