<?php
namespace Resource\Bundle\UserBundle\Service\Elastic;
// none yet test class :: to implement and test with correct mapping
use Resource\Bundle\UserBundle\Service\Curl;
class Autocomplete extends Elastic {

   
    public function tagSuggestion($letters='p') {
       $json = array(
            'similar-suggest' =>array (
                'text'=> $letters,
                'completion'=> array (
                    'field' => 'suggest'
                    )
                )
            );

       return Curl::get(
           $this->getRootUrl().' resource/_mappping?pretty -d ',
           'PUT',
           json_encode($json));
    }

    public function associate($tag,$idTag,$associateTag) {
    
           $json =  array("name" => $tag,
                          "suggest"=> array(
                                "input"=> array( $tag ),
                                "output"=> array($associateTag),
                                "weight" => 1)
                            );

        $method = 'POST';
        return Curl::get($this->getRootUrl().'resource/similar/'.$idTag,$method,json_encode($json));       
    }
    
}



