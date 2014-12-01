<?php

namespace eTextBook\LoungeBundle\Tests\Book;

use eTextBook\LoungeBundle\Entity\Book;
use eTextBook\LoungeBundle\Tests\eTextBookTestCase;
use eTextBook\LoungeBundle\UseCases\Book\PrintPublic;
use eTextBook\LoungeBundle\UseCases\Book\Loader;

class PrintPublicTest extends eTextBookTestCase {


    public function testGenerate() {
        $loader = new Loader();
        $generator = new PrintPublic();
        $generator->setBook($loader->load('final'));
        $generator->generate();
    }
}