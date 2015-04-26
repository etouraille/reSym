<?php
namespace Resource\Bundle\ListenerBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Resource\Bundle\ListenerBundle\Services\ResponseFormaterService;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
class ExceptionListener {

    protected $kernel;
    protected $formater;

    public function __construct($kernel, ResponseFormaterService $formater) {
        
        $this->kernel = $kernel;
        $this->formater = $formater;
    }
    
    public function onKernelException(GetResponseForExceptionEvent $event ) {
        $exception = $event->getException();
        $class = get_class($exception);
        $response = new Response();
        //Manage exception Type, and return appropriate status code
        if($exception instanceof NonceExpiredException){
           $status = 403;
            // Forbiden, and the client cannot solve the problem, hacking attempt
        }elseif($exception instanceof AuthentificationExcepion || $exception instanceof AuthenticationCredentialsNotFoundException ){
           $status = 500;//401;
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
        //manage stack trace.
        $stack = array();
        //if($this->kernel->getEnvironment() == 'dev' && $status >= 500){
    
            $stack = array('stack'=>$exception->getTrace());
        //}
        // Manage returned data.
        $content = array_merge(array(
            'success'=>false,
            'status'=>$status,
            'message'=>$exception->getMessage(),
        ),$stack);

        
        // set response
        $event->setResponse(
            $this->formater->formatResponse(
                $response,
                $event->getRequest(),
                $content
            )
        );

    }

}
