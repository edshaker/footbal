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
class AutorizationController extends Controller
{
    const REGISTRATION_ERROR='errors_reg';
    const NEWPASS_ERROR='errors_newpass';
    /**
     * @Route("/login",name="login")
     */
    public function loginAction(Request $request)
    {
        // получить ошибки логина, если таковые имеются
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error_auth = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error_auth = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
            $request->getSession()->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        if ( $request->getSession()->get(AutorizationController::REGISTRATION_ERROR))
        {
            $error_reg = $request->getSession()->get(AutorizationController::REGISTRATION_ERROR);
            $request->getSession()->remove(AutorizationController::REGISTRATION_ERROR);
        }
        else
            $error_reg = '';
        if ( $request->getSession()->get(AutorizationController::NEWPASS_ERROR))
        {
            $error_forget= $request->getSession()->get(AutorizationController::NEWPASS_ERROR);
            $request->getSession()->remove(AutorizationController::NEWPASS_ERROR);
        }
        else
            $error_forget = '';
        return $this->render('AppBundle:auth:default.html.twig',array(
            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error_auth'    => $error_auth,
            'error_reg'     => $error_reg,
            'error_forget'  => $error_forget
        ));
    }
    /**
     * @Route("/register",name="register")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $reg_form = $this->createFormBuilder($user, array('csrf_protection' => false))
            ->add('username')->add('city')->add('email')->add('password')->add('password2')
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $reg_form->handleRequest($request);
            $errors = $this->get('validator')->validate($reg_form);
            if (count($errors) > 0)
            {
                foreach ($errors as $e) {
                    $errors_messages[] = $e->getMessage();
                }
                $request->getSession()->set(AutorizationController::REGISTRATION_ERROR,$errors_messages);
                return new RedirectResponse($this->generateUrl('login').'#reg');
            }
            else
            {

                $user->setPasswordAndEncrypt($user->getPassword());
                $user->addRole('ROLE_USER');
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();
                $userInfo = new UserInfo();
                $userInfo->setId($user->getId());
                $userInfo->setCity($user->getCity());
                $em->persist($userInfo);
                $em->flush();
                return new RedirectResponse($this->generateUrl('registerok'));
            }
        }
        return  new RedirectResponse($this->generateUrl('login'));
    }
    /**
     * @Route("/getnewpass",name="getnewpass")
     */
    public function getNewPassAction(Request $request)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneByEmail($request->query->get('email'));
        if($user)
        {
            $newPass=substr(md5(time()+rand(0,9999)),rand(0,20),10);
            $user->setPasswordAndEncrypt($newPass);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();
            $mailer=$this->get('mailer');
            $message = $mailer->createMessage()
                ->setSubject('Востановление пароля')
                ->setFrom('send@example.com')
                ->setTo($user->getEmail())
                ->setBody('Ваш новый пароль '.$newPass );
            $mailer->send($message);
            return new RedirectResponse($this->generateUrl('changePassOk'));
        }
        else
        {
            $request->getSession()->set(AutorizationController::NEWPASS_ERROR,'В базе нет такого Email');
            return new RedirectResponse($this->generateUrl('login').'#forget');
        }

    }
    /**
     * @Route("/registerOk",name="registerok")
     */
    public function registerOkAction()
    {
        return $this->render('AppBundle:auth:regOk.html.twig');
    }
    /**
     * @Route("/changePassOk",name="changePassOk")
     */
    public function changePassOkAction()
    {
        return $this->render('AppBundle:auth:changePassOk.html.twig');
    }
    /**
     * @Route("/login_check",name="login_check")
     */
    public function loginCheckAction()
    {
        return new Response('Login Check action');
    }
    /**
     * @Route("/logout",name="logout")
     */
    public function logoutAction()
    {
        return new Response('Logout action');
    }
}
