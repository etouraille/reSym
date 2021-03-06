<?php

namespace Resource\Bundle\LoggedControllerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CreateDataController extends Controller
{
    /*
     * @var Response $response;
     */
    
    public function pingAction()
    {
        $response =  new Response();
        
        // default result for login area ping 
        // in case of wrong authentication, the Exception handler return a success false
        // and an appropriate http status code
        $response->setContent(json_encode( 
                array('success'=>true) 
            ) 
        );
        return $response;
    }
}
