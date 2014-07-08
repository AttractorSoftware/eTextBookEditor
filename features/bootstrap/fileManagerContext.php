<?php

use Behat\Behat\Context\BehatContext;

require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';


/**
 * Features context.
 */
class FileManagerContext extends BehatContext {

    private $manager;

    private function getManager() {
        if(!$this->manager) {
            $this->manager = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.file-manager');
        } return $this->manager;
    }

    /**
     * @Given /^Загружаем картинку "([^"]*)" в менеджер файлов из фикстур$/
     */
    public function uploadImage($imageFileName) {
        $uploadButton = $this->getManager()->find('css', '.upload-container input');
        $uploadButton->attachFile(dirname(__FILE__).'/../../fixtures/img/' . $imageFileName);
    }

    /**
     * @Given /^Загружаем видео "([^"]*)" в менеджер файлов из фикстур$/
     */
    public function uploadVideo($videoFileName) {
        $uploadButton = $this->getManager()->find('css', '.upload-container input');
        $uploadButton->attachFile(dirname(__FILE__).'/../../fixtures/video/' . $videoFileName);
    }

    /**
     * @Given /^Загружаем аудио "([^"]*)" в менеджер файлов из фикстур$/
     */
    public function uploadAudio($audioFileName) {
        $uploadButton = $this->getManager()->find('css', '.upload-container input');
        $uploadButton->attachFile(dirname(__FILE__).'/../../fixtures/audio/' . $audioFileName);
    }

    /**
     * @Given /^Выбираем последнию картинку из списка в менеджере файлов$/
     */
    public function selectFirstImageFromFileManager() {
        $lastImage = end($this->getManager()->findAll('css', '#images .list .item'));
        $lastImage->click();
        $this->getManager()->find('css', '#images .player .buttons .select')->click();
    }
}