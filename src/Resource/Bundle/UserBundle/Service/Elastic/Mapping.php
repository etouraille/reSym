<?php
namespace Resource\Bundle\UserBundle\Service\Elastic;

use Resource\Bundle\UserBundle\Service\Curl;
class Mapping extends Elastic {

    public function mapping() { 
        $mappings = array('mappings'=>
           array(
               'hashtag'=>
                array('properties'=>
                    array(
                        'reservedBy'=>array('type'=>'string'),
                        'reserved'=>array('type'=>'boolean'),
                        'content'=>array('type'=>'string'),
                        'userid'=>array('type'=>'string'),
                        'message'=>array('type'=>'string'),
                        'geo'=>array('type'=>'geo_point'),
                            'startDate'=>array(
                                'type'=>'date',
                                'format'=>'basicDateTimeNoMillis'
                        ),
                        'endDate'=>array(
                            'type'=>'date',
                            'format'=>'basicDateTimeNoMillis'
                         ),
                     ),
                ),
                'place'=>
                array('properties'=>
                    array(
                        'tag'=>array('type'=>'string'),
                        'address'=>array('type'=>'string'),
                        'geo'=>array('type'=>'geo_point'),
                    )
                )
            )
        );
        

                          
       $url = $this->getRootUrl().'resource';
       $method = 'PUT';
       $json = json_encode($mappings);
       echo Curl::get($url,$method,$json);
    }

    public function autocompleteMapping(){
        $url =  $this->getRootUrl().'tag';
        $method = 'PUT';
        echo Curl::get($url,$method);
        $url = $this->getRootUrl().'tag/similar/_mapping -d';
        $method = 'PUT';
        $json = array('similar' => array(
                    "properties"=> array(
                        "name" => array("type"=>"string"),
                        "suggest"=>array( 
                            "type"=>"completion",
                            "analyzer"=>"simple",
                            "search_analyzer"=>"simple",
                            "payloads"=>"true"
                       )
                    )
                )
            );
    }
}



