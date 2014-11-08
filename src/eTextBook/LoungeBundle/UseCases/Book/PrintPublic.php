<?php

namespace eTextBook\LoungeBundle\UseCases\Book;

use eTextBook\LoungeBundle\Entity\Book;

class PrintPublic {

    private $book;
    private $printPath;
    private $bookPath;
    private $bookPrintDirectory;
    private $ETBBook;

    public function generate() {
        $this->createBookDirectory();
        $modules = $this->book->getModules();
        $content = '<html style="background: none">
            <head>
                <meta charset="utf-8">
                <link rel="stylesheet" type="text/css" href="css/main-style.min.css">
            </head>
            <body style="background: none"><div class="container">';
        foreach($modules as $module) {
            $content .= $this->ETBBook->getModuleContent($module->slug);
        }
        $content .= '
            <script src="js/print.min.js"></script>
            <script>

                templateFormat.setRootTag($("e-text-book"));
                    templateFormat.parseData();
                    templateFormat.reDraw();

            </script>
            </div></body></html>';
        file_put_contents(
            $this->bookPrintDirectory . "print.html",
            str_replace("/tmp/".$this->book->getSlug() . "/", "", $content));
    }

    public function createBookDirectory() {
        $this->bookPrintDirectory = $this->printPath . $this->book->getSlug() . "/";
        if(!file_exists($this->bookPrintDirectory)) {
            mkdir($this->bookPrintDirectory);
        }
    }

    public function setBook(Book $book) {
        $this->book = $book;
    }

    public function setPrintPath($printPath) {
        $this->printPath = $printPath;
    }

    public function setBookPath($bookPath) {
        $this->bookPath = $bookPath;
    }

    public function setETBBook($ETBBook) {
        $this->ETBBook = $ETBBook;
    }

}