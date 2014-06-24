<?php

use Behat\Behat\Context\BehatContext;

require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';


/**
 * Features context.
 */
class ImageDescriptionContext extends BehatContext {

    /**
     * @Given /^Кликаем по кнопке добавления картинки$/
     */
    public function selectImageClick() {

        $desktop = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.desktop');
        $driver = eTextBookDriver::getInstance()->getDriver();
        $block = $desktop->find('css', 'block');
        $addImageButton = $block->find('css', '.add-image');
        $addImageButton->click();

    }

    /**
     * @Given /^Выбираем и загружаем картинку в менеджере файлов$/
     */
    public function uploadImage() {
        $fileManager = eTextBookDriver::getInstance()->getCurrentPage()->find('css', '.file-manager');
        $uploadButton = $fileManager->find('css', '#uploadInput');

        $uploadButton->attachFile('C:\\PATH\\TO_FILE.txt');
    }

}
