<?php
namespace Resource\Bundle\UserBundle\Service\Elastic;
// none yet test class :: to implement and test with correct mapping
use Resource\Bundle\UserBundle\Service\Curl;
class Autocomplete extends Elastic {


    #dead code, not workink yet ...
    public function tagSuggestion($letters='pot') {
       $json = array( 
            'similar' =>array (
                'text'=> $letters,
                'completion'=> array (
                    'field' => 'suggest'
                    )
                )
            );

       return Curl::get(
            $this->getRootUrl().'tag/_suggest?pretty -d ',
           'POST',
           json_encode($json), 50);
    }

    #dead code not working yet ...
    public function associate($tag,$idTag,$associateTag) {
    
           $json =  array("name" => $tag,
                          "suggest"=> array(
                                "input"=> $associateTag,
                                "output"=> $tag,
                                "weight" => 1)
                            );

           
           $method = 'PUT';
           return Curl::get($this->getRootUrl().'tag/similar/'.$idTag.'?refresh=true',$method,json_encode($json));       
    }
    
}



