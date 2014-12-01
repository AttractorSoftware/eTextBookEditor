<?php

namespace eTextBook\LoungeBundle\UseCases\Book;

use eTextBook\LoungeBundle\Entity\Book;
use eTextBook\LoungeBundle\Lib\Transliterate;
use eTextBook\LoungeBundle\Lib\FileManager;
use Gedmo\Sluggable\Util as Sluggable;

class BookPackage {
    private $book;
    private $transliter;
    private $booksFolderPath;
    private $tmpFolderPath;
    private $templateFolderPath;
    private $currentBookFolderPath;
    private $fileManager;
    private $bookImages = array();
    private $bookAudios = array();
    private $bookVideos = array();
    private $bookModules = array();

    public function __construct(Book $book) {
        $this->book = $book;
        $this->transliter = new Transliterate();
        $this->fileManager = new FileManager();
    }

    public function pack() {
        $bookFile = $this->booksFolderPath . $this->book->getSlug() . '.etb';
        if (is_file($bookFile)) { unlink($bookFile); }
        $this->updateBookSummary();
        $this->fileManager->zip($this->currentBookFolderPath, $bookFile);
    }

    public function unpack($zipFilePath = '') {
        $bookFile = $zipFilePath == '' ? $this->booksFolderPath . $this->book->getSlug() . '.etb': $zipFilePath;
        if (is_dir($this->currentBookFolderPath)) { $this->fileManager->removeDir($this->currentBookFolderPath); }
        if(!is_file($bookFile)) {
            die("Book file not found " . $bookFile);
        } else { $this->fileManager->unzip($bookFile, $this->currentBookFolderPath); }
        $this->collectContentFiles();
    }

    public function collectContentFiles() {
        $this->collectImages();
        $this->collectAudios();
        $this->collectVideos();
    }

    public function collectImages() {
        $images = $this->fileManager->fileList($this->currentBookFolderPath . '/content/img');
        if (count($images)) {
            foreach ($images as $img) {
                $img = explode('.', $img);
                $this->bookImages[] = array(
                    'title' => $img[0],
                    'extension' => $img[1]
                );
            }
        }
    }

    public function collectVideos() {
        $videos = $this->fileManager->fileList($this->currentBookFolderPath . '/content/video');
        if (count($videos)) {
            foreach ($videos as $img) {
                $img = explode('.', $img);
                $this->bookVideos[] = array(
                    'title' => $img[0],
                    'extension' => $img[1]
                );
            }
        }
    }

    public function collectAudios() {
        $audios = $this->fileManager->fileList($this->currentBookFolderPath . '/content/audio');
        if (count($audios)) {
            foreach ($audios as $img) {
                $img = explode('.', $img);
                $this->bookAudios[] = array(
                    'title' => $img[0],
                    'extension' => $img[1]
                );
            }
        }
    }

    public function createBootstrapFiles() {
        $this->createBootstrapStructure();
        $this->copyBootstrapFiles();
        $this->updateBookInfoFile();
    }

    public function updateBookInfoSummary($moduleSlug, $taskList) {
        $info = $this->getBookInfo();
        $modules = $info->modules;
        foreach($modules as $module) {
            if($module->slug == $moduleSlug) {
                $module->tasks = $taskList;
            }
        }
        $this->setBookInfo($info);
    }

    public function updateBookSummary() {
        $info = $this->getBookInfo();
        $modules = $info->modules;
        $summaryContent = '';
        foreach($modules as $module) {
            $taskListContent = '';
            if(isset($module->tasks)) {
                foreach($module->tasks as $task) {
                    $taskListContent .= str_replace(
                        array('-- taskId --', '-- taskTitle --'),
                        array($task->id, $task->title),
                        file_get_contents($this->templateFolderPath . 'summaryTaskTemplate.html')
                    );
                }
            }
            $moduleContent = str_replace(
                array('-- moduleSlug --', '-- moduleTitle --', '-- taskList --'),
                array($module->slug, $module->title, $taskListContent),
                file_get_contents($this->templateFolderPath . 'summaryTemplate.html')
            );
            $summaryContent .= $moduleContent;
        }

        $indexFilePath = $this->currentBookFolderPath . 'index.html';
        $indexContent = file_get_contents($indexFilePath);
        $indexContent = str_replace("-- module-list --", $summaryContent, $indexContent);
        file_put_contents($indexFilePath, $indexContent);
    }

    public function addModule($moduleTitle) {
        $moduleSlug = date('d-m-y-H-i-s');
        $bookInfo = $this->getBookInfo();
        $bookInfo->modules[] = array('title' => $moduleTitle, 'slug' => $moduleSlug);
        $this->setBookInfo($bookInfo);
        $this->createModuleFile($moduleTitle, $moduleSlug);
        return $moduleSlug;
    }

    public function updateModuleContent($moduleSlug, $content) {
        $moduleFilePath = $this->currentBookFolderPath . 'modules/' . $moduleSlug . '.html';
        $moduleContent = str_replace("/tmp/". $this->book->getSlug() ."/content/", "content/", $content);
        file_put_contents($moduleFilePath, $moduleContent);
    }

    private function createModuleFile($moduleTitle, $moduleSlug) {
        $moduleFilePath = $this->currentBookFolderPath . '/modules/' . $moduleSlug . '.html';
        $moduleContent = file_get_contents($this->templateFolderPath . 'moduleTemplate.html');
        $moduleContent = str_replace('-- moduleTitle --', $moduleTitle, $moduleContent);
        file_put_contents($moduleFilePath, $moduleContent);
    }

    public function getBookModuleContent($moduleSlug) {
        if(is_file($this->currentBookFolderPath . 'modules/' . $moduleSlug . '.html')) {
            $moduleContent = file_get_contents($this->currentBookFolderPath . 'modules/' . $moduleSlug . '.html');
            $moduleContent = explode('<e-text-book>', $moduleContent);
            $moduleContent = explode('</e-text-book>', $moduleContent[1]);
            $moduleContent = str_replace("content/", "/tmp/". $this->book->getSlug() ."/content/", $moduleContent[0]);
            $moduleContent = str_replace("../", "", $moduleContent);
            $moduleContent = "<e-text-book>" . $moduleContent . "</e-text-book>";
        } else {
            $moduleContent = '';
        }
        return $moduleContent;
    }

    public function getBookInfo() {
        return json_decode(file_get_contents($this->currentBookFolderPath . 'book.info'));
    }

    public function setBookInfo($info) {
        file_put_contents($this->currentBookFolderPath . 'book.info', json_encode($info));
    }

    public function updateBookInfoFile() {
        if(file_exists($this->currentBookFolderPath . 'book.info')) {
            $info = json_decode(file_get_contents($this->currentBookFolderPath . 'book.info'));
        } else {
            $info = new \stdClass();
            $info->modules = array();
        }

        $info->title = $this->book->getTitle();
        $info->authors = $this->book->getAuthors();
        $info->slug = $this->book->getSlug();
        $info->editor = $this->book->getEditor();
        $info->isbn = $this->book->getIsbn();
        $info->source = $this->book->getFile();
        file_put_contents($this->currentBookFolderPath . 'book.info', json_encode($info));
    }

    public function copyBootstrapFiles() {
        copy($this->templateFolderPath . "/css/main-style.min.css", $this->currentBookFolderPath . 'css/main-style.min.css');
        copy($this->templateFolderPath . "/js/script.min.js", $this->currentBookFolderPath . 'js/script.min.js');
        $this->fileManager->copyFilesFromDirectory($this->templateFolderPath . "/fonts", $this->currentBookFolderPath . 'fonts');
        $this->fileManager->copyFilesFromDirectory($this->templateFolderPath . "/img", $this->currentBookFolderPath . 'img');
        if($this->book->getFile() == '') { //Check source book file (pdf, mobi, fb2 etc.)
            file_put_contents($this->currentBookFolderPath . 'index.html', $this->generateBookIndexFileContent());
        } else {
            $filePath = $this->tmpFolderPath . 'book/' . $this->book->getFile();
            if(file_exists($filePath)) {
                copy($filePath, $this->currentBookFolderPath . '/' . $this->book->getFile());
            }
        }
        if($this->book->getCover() != '') {
            $uploadedCoverLocation = $this->tmpFolderPath . 'cover/' . $this->book->getCover();
            if(file_exists($uploadedCoverLocation)) {
                copy($uploadedCoverLocation, $this->currentBookFolderPath . 'content/cover.png');
            }
        }
    }

    public function generateBookIndexFileContent() {
        $indexFileContent = file_get_contents($this->templateFolderPath . "/index.html");
        $indexFileContent = str_replace("-- book-title --", $this->book->getTitle(), $indexFileContent);
        return $indexFileContent;
    }

    public function createBootstrapStructure() {
        !file_exists($this->currentBookFolderPath) ? mkdir($this->currentBookFolderPath, 0777, true) : false;
        !file_exists($this->currentBookFolderPath . 'css') ? mkdir($this->currentBookFolderPath . 'css', 0777, true) : false;
        !file_exists($this->currentBookFolderPath . 'js') ? mkdir($this->currentBookFolderPath . 'js', 0777, true) : false;
        !file_exists($this->currentBookFolderPath . 'img') ? mkdir($this->currentBookFolderPath . 'img', 0777, true) : false;
        !file_exists($this->currentBookFolderPath . 'fonts') ? mkdir($this->currentBookFolderPath . 'fonts', 0777, true) : false;
        !file_exists($this->currentBookFolderPath . 'modules') ? mkdir($this->currentBookFolderPath . 'modules', 0777, true) : false;
        !file_exists($this->currentBookFolderPath . 'content/video') ? mkdir($this->currentBookFolderPath . 'content/video', 0777, true) : false;
        !file_exists($this->currentBookFolderPath . 'content/audio') ? mkdir($this->currentBookFolderPath . 'content/audio', 0777, true) : false;
        !file_exists($this->currentBookFolderPath . 'content/img') ? mkdir($this->currentBookFolderPath . 'content/img', 0777, true) : false;
    }

    public function updateBookSlug() {
        $slug = Sluggable\Urlizer::urlize(
            $this->transliter->transliterate($this->book->getTitle(), 'ru'), '-'
        );
        $this->book->setSlug($this->book->getId() . "-" . $slug . "-" . $this->book->getVersion());
        $this->currentBookFolderPath = $this->tmpFolderPath . $this->book->getSlug() . "/";
    }

    public function getBook() {
        return $this->book;
    }

    public function setTmpFolderPath($tmpFolderPath) {
        $this->tmpFolderPath = $tmpFolderPath;
    }

    public function setTemplateFolderPath($templateFolderPath) {
        $this->templateFolderPath = $templateFolderPath;
    }

    public function getTemplateFolderPath() {
        return $this->templateFolderPath;
    }

    public function setBooksFolderPath($booksFolderPath) {
        $this->booksFolderPath = $booksFolderPath;
    }

    public function getBooksFolderPath() {
        return $this->booksFolderPath;
    }

    public function getBookImages() {
        return $this->bookImages;
    }

    public function getBookAudios() {
        return $this->bookAudios;
    }

    public function getBookVideos() {
        return $this->bookVideos;
    }

    public function getBookModules() {
        $info = $this->getBookInfo();
        return $info->modules;
    }

}