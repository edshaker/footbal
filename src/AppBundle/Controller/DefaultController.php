<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        echo sha1('user');
        $user = $this->getUser();
        return $this->render('AppBundle:Default:default.html.twig',array('user'=>$user));
    }
}
