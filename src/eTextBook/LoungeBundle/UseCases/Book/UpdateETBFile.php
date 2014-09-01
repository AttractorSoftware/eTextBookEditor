<?php

namespace eTextBook\LoungeBundle\UseCases\Book;

use eTextBook\LoungeBundle\Entity\Book;
use eTextBook\LoungeBundle\Lib\SummaryDom;

class UpdateETBFile
{
    private $book;
    private $tmpDir;
    private $bookTmpDir;

    public function __construct()
    {
        global $kernel;
        $this->tmpDir = $kernel->getContainer()->getParameter('book_tmp_dir');
        $this->templateDir = $kernel->getContainer()->getParameter('book_template_dir');
    }

    public function setBook(Book $book)
    {
        $this->book = $book;
        $this->bookTmpDir = $this->tmpDir . $this->book->getSlug() . '/';
    }

    public function updateModuleContent($bookName, $moduleSlug, $content)
    {
        $moduleFilePath = $this->tmpDir . $bookName . '/modules/' . $moduleSlug . '.html';
        $moduleContent = new SummaryDom();
        $moduleContent->loadWithBreaks($moduleFilePath);
        $content = str_replace('/tmp/' . $bookName . '/', '../', $content);
        $moduleContent->find('.e-text-book-viewer', 0)->innertext = $content;

        $exercisesIDList = $moduleContent->getExercisesList();
        $moduleContent->save($moduleFilePath);
        if (sizeof($exercisesIDList) != 0) {
            $this->addExercisesToSummary($bookName, $moduleSlug, $exercisesIDList);
        }
        $moduleContent->destroy();
    }

    public function addExercisesToSummary($bookName, $moduleSlug, $exercisesIDList)
    {
        $indexFilePath = $this->tmpDir . $bookName . '/index.html';
        $indexContent = new SummaryDom();
        $indexContent->loadWithBreaks($indexFilePath);
        $indexContent->insertExercisesIntoChapter($moduleSlug, $exercisesIDList);
        $indexContent->save($indexFilePath);
        $indexContent->destroy();
    }


    public function addModule($moduleTitle)
    {
        $moduleSlug = date('d-m-y-H-i-s');
        $bookInfo = $this->getBookInfo();
        $bookInfo->modules[] = array('title' => $moduleTitle, 'slug' => $moduleSlug);
        $this->setBookInfo($bookInfo);
        $this->createModuleFile($moduleTitle, $moduleSlug);
        $this->updateBookSummary($moduleTitle, $moduleSlug);
        $this->pack();
        return $moduleSlug;
    }

    private function createModuleFile($moduleTitle, $moduleSlug)
    {
        $moduleFilePath = $this->tmpDir . $this->book->getSlug() . '/modules/' . $moduleSlug . '.html';
        $moduleContent = new SummaryDom();
        $moduleContent->loadWithBreaks($this->templateDir . "/moduleTemplate.html");
        $moduleContent->setTitle($moduleTitle);
        $moduleContent->setModuleTitle($moduleTitle);
        $moduleContent->save($moduleFilePath);
        $moduleContent->destroy();
    }


    public function getBookInfo()
    {
        return json_decode(file_get_contents($this->tmpDir . $this->book->getSlug() . '/book.info'));
    }

    public function setBookInfo($data)
    {
        file_put_contents($this->tmpDir . $this->book->getSlug() . '/book.info', json_encode($data));
    }

    private function updateBookSummary($moduleTitle, $moduleSlug)
    {
        $indexFilePath = $this->tmpDir . $this->book->getSlug() . '/index.html';
        $summaryTemplate = file_get_contents($this->templateDir . "/summaryLinkTemplate.html");

        if (file_exists($indexFilePath)) {
            $summaryContent = $this->createSummary($moduleTitle, $moduleSlug, $summaryTemplate);
            $indexTemplate = file_get_contents($this->tmpDir . $this->book->getSlug() . '/index.html');
        } else {
            $indexTemplate = file_get_contents($this->templateDir . "/index.html");
            $summaryContent = $this->getBookSummaryFromInfo($summaryTemplate);
        }

        $this->fillIndexFile($indexTemplate, $summaryContent, $indexFilePath);
    }

    private function getBookSummaryFromInfo($summaryTemplate)
    {
        $info = json_decode(file_get_contents($this->bookTmpDir . 'book.info'), true);
        $summaryContent = '';
        foreach ($info['modules'] as $module) {
            $summaryContent .= $this->createSummary($module['title'], $module['slug'], $summaryTemplate);
        }
        return $summaryContent;
    }

    private function createSummary($moduleTitle, $moduleSlug, $summaryTemplate)
    {
        $summaryContent = new SummaryDom();
        $summaryContent->loadWithBreaks($summaryTemplate);
        $summaryContent->setChapterAttributes($moduleSlug, $moduleTitle);
        $result = $summaryContent->outertext;
        $summaryContent->destroy();
        return $result;
    }

    private function fillIndexFile($indexTemplate, $summaryContent, $indexPath)
    {
        $indexContent = new SummaryDom();
        $indexContent->loadWithBreaks($indexTemplate);
        $indexContent->setBookAttributes($this->book->getTitle());
        $indexContent->setSummaryList($summaryContent);
        $indexContent->save($indexPath);
        $indexContent->destroy();
    }

    public function execute()
    {
        $this->updateInfoFile();
        $this->pack();
    }

    public function updateInfoFile()
    {
        $info = json_decode(file_get_contents($this->bookTmpDir . 'book.info'));
        $info->title = $this->book->getTitle();
        $info->authors = $this->book->getAuthors();
        $info->editor = $this->book->getEditor();
        $info->isbn = $this->book->getIsbn();
        file_put_contents($this->bookTmpDir . 'book.info', json_encode($info));
    }


    public function copyTemplateFiles()
    {
        copy($this->templateDir . "/css/main-style.min.css", $this->bookTmpDir . 'css/main-style.min.css');
        copy($this->templateDir . "/js/script.min.js", $this->bookTmpDir . 'js/script.min.js');
        $this->fileManager->copyFilesFromDirectory($this->templateDir . "/fonts", $this->bookTmpDir . 'fonts');
        $this->fileManager->copyFilesFromDirectory($this->templateDir . "/img", $this->bookTmpDir . 'img');
    }


    public function pack()
    {
        global $kernel;
        $booksDir = $kernel->getContainer()->getParameter('books_dir');
        $fileManager = $kernel->getContainer()->get('fileManager');
        $bookFile = $booksDir . $this->book->getSlug() . '.etb';
        if (is_file($bookFile)) {
            unlink($bookFile);
        }
        $fileManager->zip($this->bookTmpDir, $bookFile);
    }

}