<?php

namespace eTextBook\LoungeBundle\UseCases\Book;

use eTextBook\LoungeBundle\Entity\Book;

class PrintPublic {

    private $book;
    private $printPath;
    private $bookPath;

    public function generate() {
        $modules = $this->book->getModules();
        $content = '';
        foreach($modules as $module) {
            $content .= file_get_contents(
                $this->bookPath .
                $this->book->getSlug() .
                '/modules/' .
                $module->slug .
                '.html'
            );
        }

        file_put_contents($this->printPath . $this->book->getSlug() . ".html", $content);
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

}