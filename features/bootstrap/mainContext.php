<?php

use Behat\Behat\Context\BehatContext;

require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';

require_once "eTextBookContext.php";
require_once "moduleContext.php";
require_once "blockContext.php";
require_once "translateComparativeWidgetContext.php";

/**
 * Features context.
 */
class FeatureContext extends BehatContext {

    public function __construct(array $parameters) {
        $this->useContext('moduleContext', new ModuleContext());
        $this->useContext('blockContext', new BlockContext());
        $this->useContext('translateComparativeContext', new TranslateComparativeContext());
    }

    /**
     * @AfterFeature
     */
    public static function closePage() {
        eTextBookDriver::getInstance()->closeBrowser();
    }

    /**
     * @Given /^Открываем страницу "([^"]*)"$/
     */
    public function openPage($url) {
        eTextBookDriver::getInstance()->openPage($url);
    }

}
