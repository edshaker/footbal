<?php
namespace AppBundle\EventListener;


use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class AuthListener
{
    public function onKernelController(FilterControllerEvent $event)
    {

        $controller = $event->getController();
        if(method_exists ($controller[0],'getUser') && method_exists ($controller[0]->getUser(),'getRoles'))
            $roles = $controller[0]->getUser()->getRoles();
        else
            $roles=array();
        $request = $event->getRequest();
        if (in_array('DELETED_USER', $roles) && !strpos($request->getUri(), 'deleted')) {
            header('Location: ' . $controller[0]->generateUrl('deletedpage'));
            exit;
        }
    }
}