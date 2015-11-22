<?php
namespace Resource\Bundle\UserBundle\Service\Elastic;

use Resource\Bundle\UserBundle\Service\Curl;
class Mapping extends Elastic {

       //mapping for completion:
       $url = $this->getRootUrl().'resource';
       $method = 'PUT';
       //Curl::get($url.json_encode($this->autocompletMapping('hashtag')));

       /*
       $settings = array(
        'settings'=> 
           array('analysis'=>
            array(
                'filter'=>array(
                    'nGram_filter'=>array(
                        'type'=>'nGram',
                        'min_gram'=>2,
                        'max_gram'=>20,
                        'token_chars'=>array(
                            'letter',
                            'digit',
                            'punctuation',
                            'symbol'
                        )
                    )
                ),
                'analyze'=>array(
                    'nGram_analyzer'=>array(
                        'type'=>'custom',
                        'tokenizer'=>'whitespace',
                        'filter'=>array(
                            'lowercase',
                            'asciifolding',
                            'nGram_filter'
                        )
                    ),
                    'whitespace_analyzer'=>array(
                        'type'=>'custom',
                        'tokenizer'=>'whitespace',
                        'filter'=>array(
                            'lowercase',
                            'asciifolding'
                        )
                    )
                )
            )
            
        )
       
    );
    */

       $mapauto = array('mappings'=>
           array('hashtag' => array(
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
        )
    ); 

       //Curl::get($this->getRootUrl().'sim/hahstag/_mapping -d', 'PUT', json_encode($mapauto));
       
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
                                'format'=>'basiDateTimeNoMillis'
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
                ),
                  
            )
        );
        

                          
       $url = $this->getRootUrl().'resource';
       $method = 'PUT';
       $json = json_encode($mappings);
       return Curl::get($url,$method,$json);
   }
}



