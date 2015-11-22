<?php
namespace Resource\Bundle\UserBundle\Service\Elastic;
// none yet test class :: to implement and test with correct mapping
use Resource\Bundle\UserBundle\Service\Curl;
class Autocomplete extends Elastic {

   
    protected function tagSuggestion($letters) {
       $json = array(
            'hashtag-suggest' =>array (
                'text'=> $letters,
                'completion'=> array (
                    'field' => 'suggest'
                    )
                )
            );

        return Curl::get($this->getRootUrl().' sim/_mappping?pretty -d '.json_encode($json));
    }

    public function associate($tag,$idTag,$associateTag) {
    
           $json =  array("name" => $tag,
                          "suggest"=> array(
                                "input"=> array( $tag ),
                                "output"=> array($associateTag),
                                "weight" => 1)
                            );

                      $method = 'PUT';
        return Curl::get($this->getRootUrl().'sim'.'/'.$tag.'/'.$idTag,$method,json_encode($json));       

    }

}



