<?php

namespace eTextBook\LoungeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/documentation")
 */
class DocumentationController {
    /**
     * @Route("/", name="documentation")
     * @Template
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Route("/create-book", name="documentation-create-book")
     * @Template
     */
    public function createBookAction() {
        return array();
    }
}