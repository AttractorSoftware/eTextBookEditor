<?php

namespace eTextBook\LoungeBundle\UseCases\Book;


class BookPublisher {

    private $package;

    public function __construct(BookPackage $package) {
        $this->package = $package;
    }

}