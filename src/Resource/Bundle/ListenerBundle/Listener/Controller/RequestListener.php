<?php
namespace Resource\Bundle\ListenerBundle\Listener\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Resource\Bundle\ListenerBundle\Services\ResponseFormaterService;

class RequestListener
{

    protected $formater;

    public function __construct(ResponseFormaterService $formater){
        $this->formater = $formater;
    }
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

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
            $event->setResponse(
                $this->formater->formatResponse(
                    $event->getResponse(),
                    $event->getRequest()
                )
            );
    }
}
