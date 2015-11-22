<?php
namespace Resource\Bundle\UserBundle\Service\Elastic;

use Resource\Bundle\UserBundle\Service\Curl;
class Delete extends Elastic {

   public function delete(){
       $url = $this->getRootUrl().'resource/hashtag/_query';
       $method = 'DELETE';
       $data = json_encode(array('query'=>array('match'=>array('content'=>'cool'))));
       return Curl::get($url,$method,$data);
   }

 }



