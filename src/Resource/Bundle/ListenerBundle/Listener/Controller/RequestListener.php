<?php
namespace Resource\Bundle\ListenerBundle\Listener\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

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
            $data = is_array($data)?$data:array();
            //todo might be usefull to set every key as a post data
            foreach($data as $key=>$value){
                $event->getRequest()->attributes->set($key,$value);
            }
        }
    }

    public function onKernelResponse(FilterResponseEvent $event){
            //The api is in JSON format.
            $event->getResponse()->headers->set('Content-Type', 'application/json');
            // Header For Cross Domain.
            $event->getResponse()->headers->set('Access-Control-Allow-Headers','Content-Type');
            $event->getResponse()->headers->set('Access-Control-Allow-Methods','GET,POST,OPTIONS,PUT,DELETE');
            $event->getResponse()->headers->set('Access-Control-Allow-Origin','*');

        
    }
}
