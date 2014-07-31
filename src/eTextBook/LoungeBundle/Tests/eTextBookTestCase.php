<?php

namespace eTextBook\LoungeBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

require_once(__DIR__ . "/../../../../app/AppKernel.php");

class eTextBookTestCase extends WebTestCase {
    public function setUp() {
        global $kernel;
        $kernel = new \AppKernel("test", true);
        $kernel->boot();
    }
}