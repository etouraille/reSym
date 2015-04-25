<?php
namespace Resource\Bundle\ListenerBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener {

    protected $kernel;

    public function __construct($kernel) {
        
        $this->kernel = $kernel;
    
    }
    
    public function onKernelException(GetResponseForExceptionEvent $event) {
        $exception = $event->getException();
        $response = new Response();
        $line = array();
        //Manage exception Type, and return appropriate status code
        if($exception instanceof NonceExpiredException){
           $status = 403;
            // Forbiden, and the client cannot solve the problem, hacking attempt
        }elseif($exception instanceof AuthentificationExcepion){
           $status = 401;
            // Forbiden, but the client can logged with the proper rights
        }elseif($exception instanceof HttpExceptionInterface){
            $status = $exception->getStatusCode();
        }
        else{
           $status = 500;
           // bad geteway, probleme with the api
           // We log the trace in dev env.
        
        }
        $response->setStatusCode($status);
       var_dump($exception); 
        //manage stack trace.
        $stack = array();
        if($this->kernel->getEnvironment() == 'dev' && $status >= 500){
            $stack = array('stack'=>$exception->getTrace());
        }
        // Manage returned data.
        $ret = array_merge(array(
            'success'=>false,
            'status'=>$status,
            'message'=>$exception->getMessage(),
        ),$line,$stack);
        $response->setContent(json_encode($ret));

        //Header return Type
        $response->headers->set('Content-Type','application/json');
        
        // set response
        $event->setResponse($response);

    }

}
