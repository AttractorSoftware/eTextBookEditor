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
     * @Route("/register", name="documentation-register")
     * @Template
     */
    public function registerAction() {
        return array();
    }

    /**
     * @Route("/book-create", name="documentation-book-create")
     * @Template
     */
    public function bookCreateAction() {
        return array();
    }

    /**
     * @Route("/module-create", name="documentation-module-create")
     * @Template
     */
    public function moduleCreateAction() {
        return array();
    }

    /**
     * @Route("/task-create", name="documentation-task-create")
     * @Template
     */
    public function taskCreateAction() {
        return array();
    }

}