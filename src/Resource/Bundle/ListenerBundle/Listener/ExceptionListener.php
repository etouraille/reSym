<?php
namespace Resource\Bundle\ListenerBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;

class ExceptiontListener
{

    public function onKernelException(GetResponseForExcepion $event)
    {
        $exception = $event->getException();
        $response = new Response();
        exit(1); 
        //Manage exception Type, and return appropriate status code
        if($exception instanceof NonceExpiredException){
           $status = 403;
            // Forbiden, and the client cannot solve the problem, hacking attempt
        }elseif($exception instanceof AuthentificationExcepion){
           $status = 401;
            // Forbiden, but the client can logged with the proper rights
        }else{
           $status = 500;
           // bad geteway, probleme with the api
           // We log the trace in dev env.
        }
        $response->setStatusCode($status);
        
        //manage stack trace.
        $stack = array();
        // todo implement environement test
        if($dev=true && $status = 500){
            $stack = array('stack'=>$exception->getTrace());
        }

        // Manage returned data.
        $ret = array_merge(array(
            'success'=>false,
            'status'=>$status,
            'message'=>$exception->getMessage(),
        ),$stack);

        //Header return Type
        $response->setHeaders(array('Content-Type'=>'application/json');
        
        // set response
        $event->setResponse($response);

    }

}
