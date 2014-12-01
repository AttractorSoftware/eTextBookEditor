<?php

namespace Features\Bootstrap;

use Behat\Mink\Session;


class eTextBookDriver
{
    private static $instance;
    private $driver;
    private $page;
    private $variables = array();

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getDriver()
    {
        if (!$this->driver) {
            $this->driver = new \Behat\Mink\Driver\Selenium2Driver();
        }
        return $this->driver;
    }

    public function openPage($url)
    {
        $session = new Session($this->getDriver());
        $session->start();
        $session->visit($url);
        $this->page = $session->getPage();
    }

    public function getCurrentPage()
    {
        return $this->page;
    }

    public function deleteAutoCreatedBooks()
    {
        $webDirectory = dirname(__FILE__) . "/../../web";
        foreach (glob($webDirectory . "/books/title-auto-generate*.etb") as $filename) {
            unlink($filename);
        }
        foreach (glob($webDirectory . "/tmp/title-auto-generate*") as $filename) {
            $this->rrmdir($filename);
        }

    }

    private function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        $this->rrmdir($dir . "/" . $object);
                    else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }


    public function closeBrowser()
    {
        $this->driver->stop();
    }

    public function getVar($variableName)
    {
        return isset($this->variables[$variableName]) ? $this->variables[$variableName] : 'Variable not defined';
    }

    public function setVar($variableName, $variableValue)
    {
        $this->variables[$variableName] = $variableValue;
    }

    public function getSession(){

    }
}