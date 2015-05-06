<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use AppBundle\Entity\UserInfo;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
class CabinetController extends Controller
{
    /**
     * @Route("/cabinet",name="cabinet")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Cabinet:default.html.twig');
    }
    /**
     * @Route("/cabinet/settings",name="settings")
     */
    public function settingsAction()
    {
        $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneById($this->getUser()->getId());
        return $this->render('AppBundle:Cabinet:settings.html.twig',array('info'=>$userInfo));
    }
    /**
     * @Route("/cabinet/savesettings",name="savesettings")
     */
    public function saveSettingsAction(Request $request)
    {
        $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneById($this->getUser()->getId());
        $form = $this->createFormBuilder($userInfo, array('csrf_protection' => false))
            ->add('name')->add('city')->add('phone')->add('sex')->add('about')->add('skype')
            ->getForm();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($userInfo);
            $em->flush();
        }
        return  new RedirectResponse($this->generateUrl('settings'));
    }
}
