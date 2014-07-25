<?php

namespace eTextBook\LoungeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller {
    /**
     * @Route("/", name="home-page")
     * @Template()
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Route("/readers", name="readers")
     * @Template()
     */
    public function readersAction() {
        return array();
    }
}
