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
    const MESSAGE='message';
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
    public function settingsAction(Request $request)
    {
        if ( $request->getSession()->get(CabinetController::MESSAGE))
        {
            $message= $request->getSession()->get(CabinetController::MESSAGE);
            $request->getSession()->remove(CabinetController::MESSAGE);
        }
        else
            $message = null;
        $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneById($this->getUser()->getId());
        return $this->render('AppBundle:Cabinet:settings.html.twig',array('info'=>$userInfo,'message'=>$message));
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
            $request->getSession()->set(CabinetController::MESSAGE,'Данные профиля сохранены усешно');
        }
        return  new RedirectResponse($this->generateUrl('settings'));
    }
    /**
     * @Route("/cabinet/changepass",name="changepass")
     */
    public function changePassAction(Request $request)
    {
        if ( $request->getSession()->get(CabinetController::MESSAGE))
        {
            $message= $request->getSession()->get(CabinetController::MESSAGE);
            $request->getSession()->remove(CabinetController::MESSAGE);
        }
        else
            $message = null;
        return $this->render('AppBundle:Cabinet:changePass.html.twig',array('message'=>$message));
    }
    /**
     * @Route("/cabinet/savechangepass",name="savechangepass")
     */
    public function saveChangePassAction(Request $request)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneById($this->getUser()->getId());
        if(!$user->isPasswordCorrect($request->request->get('old_pass')))
        {
            $request->getSession()->set(CabinetController::MESSAGE,'Пароль не верен');
            return  new RedirectResponse($this->generateUrl('changepass'));
        }
        if($request->request->get('password')!=$request->request->get('password2'))
        {
            $request->getSession()->set(CabinetController::MESSAGE,'Пароли не совпадают');
            return  new RedirectResponse($this->generateUrl('changepass'));
        }
        $user->setPasswordAndEncrypt($request->request->get('password'));
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($user);
        $em->flush();
        $request->getSession()->set(CabinetController::MESSAGE,'Пароль изменен успешно');
        return  new RedirectResponse($this->generateUrl('settings'));
    }
}
