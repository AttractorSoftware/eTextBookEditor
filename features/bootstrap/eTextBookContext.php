<?php

namespace Features\Bootstrap;

use Behat\Behat\Context\BehatContext;
use Features\Bootstrap\eTextBookDriver;

class eTextBookContext extends BehatContext {

    public function getDriver() {
        return eTextBookDriver::getInstance()->getDriver();
    }

    public function elementSetAttribute($cssStringTarget, $attribute, $value) {
        $driver = $this->getDriver();
        $driver->executeScript('$("'. $cssStringTarget .'").attr({ ' . $attribute . ': "' . $value . '"})');
    }

    public function selectSetValue($cssStringSelect, $value) {
        $driver = $this->getDriver();
        $driver->executeScript('$("'. $cssStringSelect .'").val(' . $value . ')');
    }

    public function setVar($variableName, $variableValue) {
        eTextBookDriver::getInstance()->setVar($variableName, $variableValue);
    }

    public function getVar($variableName) {
        return eTextBookDriver::getInstance()->getVar($variableName);
    }

    public function findCss($cssString) {
        return eTextBookDriver::getInstance()->getCurrentPage()->find('css', $cssString);
    }

    public function findAllCss($cssString) {
        return eTextBookDriver::getInstance()->getCurrentPage()->findAll('css', $cssString);
    }

    public function hasClass($target, $class) {
        return in_array($class, explode(' ', $target->getAttribute('class')));
    }
}