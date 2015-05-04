<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;

class CabinetController extends Controller
{
    /**
     * @Route("/cabinet",name="cabinet")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        return $this->render('AppBundle:Cabinet:default.html.twig',array('user'=>$user));
    }
}
