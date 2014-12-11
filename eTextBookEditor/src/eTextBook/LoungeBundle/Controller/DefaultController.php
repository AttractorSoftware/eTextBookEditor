<?php

namespace eTextBook\LoungeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {
    /**
     * @Route("/", name="home-page")
     * @Template()
     */
    public function indexAction(Request $request) {
        return array();
    }

    /**
     * @Route("/readers", name="readers")
     * @Template()
     */
    public function readersAction() {
        return array();
    }

    /**
     * @Route("/enter", name="enter")
     * @Template()
     */
    public function enterAction() {
        return array();
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request) {
        if($request->headers->get('referer')) {
            $redirectUrl = $request->headers->get('referer');
        } else {
            $redirectUrl = '/';
        }
        return $this->redirect($redirectUrl, 301);
    }
}
