<?php
namespace Resource\Bundle\ListenerBundle\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ResponseFormaterService {

    protected $response = null;
    protected $content = null;
    protected $request = null;

    public function formatResponse(Response $response = null,Request $request = null, array $content = null){

        if(!isset($response)) {
            $response = isset($this->response)?$this->response:new Response();
        }
        //request
        if(!isset($request)){
            $request = isset($this->request)?$this->request:null;
        }

        // JSON FORMAT RESPONSE
        $response->headers->set('Content-Type', 'application/json');
        // Header For Cross Domain.
        $allowHeaders = 'content-type';
        if(preg_match('#logged\-area#',$request->getUri())) {
            $allowHeaders ='authorization,x-wsse';
        }    
        $response->headers->set('Access-Control-Allow-Headers',$allowHeaders);
        $response->headers->set('Access-Control-Allow-Methods','GET,OPTIONS,PUT,DELETE');
        $response->headers->set('Access-Control-Allow-Origin','*');
        $response->headers->set('Access-Control-Allow-Credential',"true");
        //set content
        $content = is_array($this->content)?$this->content:(is_array($content)?$content:null);
        if(isset($content)) {
            $response->setContent(json_encode($content));
        }
        return $response;
    }
    public function setResponse(Response $response){
        $this->response = $response;
    }
    
    public function setContent(array $content){
        $this->content = $content;
    }

    public function setRequest(Request $request){
        $this->request = $request;
    }
}

