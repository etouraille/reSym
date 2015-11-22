<?php
namespace Resource\Bundle\UserBundle\Service\Elastic;
use Resource\Bundle\UserBundle\Service\Curl;
class Elastic {

    protected $host;
    protected $port;
    
    public function __construct($host='www.objetspartages.org', $port = '9200'){
        $this->host = $host;
        $this->port = $port;
    }

    public function index($index,$type,$data,$id){
        return Curl::get($this->getUrl($index,$type,$id),'PUT',$data);
    }

     public function update($index,$type,$data,$id){
        return Curl::get($this->getUrl($index,$type,$id),'POST',$data, true);
    }

    protected function getUrl($index,$type,$indexNumber,$isUpdate = false){
        $urlUpdate = '';
        if($isUpdate){
            $urlUpdate = '/_update';
        }
        return $this->getRootUrl() .$index . '/'.$type.'/'.$indexNumber.$urlUpdate;
    }

    protected function getRootUrl()
    {
         return 'http://'.$this->host.':'.$this->port.'/'; 
    }   
}
