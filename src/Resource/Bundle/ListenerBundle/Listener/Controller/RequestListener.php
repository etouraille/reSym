<?php
namespace Resource\Bundle\ListenerBundle\Listener\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestListener
{

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller peut être une classe ou une closure. Ce n'est pas
         * courant dans Symfony2 mais ça peut arriver.
         * Si c'est une classe, elle est au format array
         */
        if (!is_array($controller)) {
            return;
        }

        $request = $event->getRequest();
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $data = is_array($data)?$data:array());
            $event->getResponse()->body->set($data);
            //todo might be usefull to set every key as a post data
            foreach($data as $key=>$value){
                $event->getResponse()->request->set($key,$value);
            }
        }
    }
}
