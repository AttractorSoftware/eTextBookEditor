<?php

use Behat\Mink\Session;


class eTextBookDriver {
    private static $instance;
    private $driver;
    private $page;

    public static function getInstance() {
        if ( empty(self::$instance) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getDriver() {
        if(!$this->driver) {
            $this->driver = new \Behat\Mink\Driver\Selenium2Driver();
        } return $this->driver;
    }

    public function openPage($url) {
        $session = new Session($this->getDriver());
        $session->start();
        $session->visit($url);
        $this->page = $session->getPage();
    }

    public function getCurrentPage() {
        return $this->page;
    }

    public function closeBrowser() {
        $this->driver->stop();
    }
}