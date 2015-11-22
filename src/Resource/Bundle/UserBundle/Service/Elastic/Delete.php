<?php
namespace Resource\Bundle\UserBundle\Service\Elastic;

use Resource\Bundle\UserBundle\Service\Curl;
class Delete extends Elastic {

   public function delete(){
       
       $url = $this->getRootUrl().'resource';
       $method = 'DELETE';
       Curl::get($url,$method,'');

        $url = $this->getRootUrl().'sim';
       $method = 'DELETE';
       Curl::get($url,$method,'');


   }

 }



