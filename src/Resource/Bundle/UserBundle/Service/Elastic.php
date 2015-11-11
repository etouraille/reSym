<?php
namespace Resource\Bundle\UserBundle\Service;

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

    public function percolator($index,$type, $data, $percolate_id) {
        return Curl::get(
            $this->getRootUrl(). $index .'/.percolator/' . $percolate_id, 
            'PUT' , 
            $data
        );
    }

    public function percolate($index, $type, $document ) {
        return Curl::get(
            $this->getRootUrl(). $index . '/'. $type .'/_percolate',
            'GET',
            $document
        );
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

    public function otherMapping($tag = 'hashtag') {
        $json = array( 'hashtag' => array(
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
        $action = 'PUT';
        return Curl::get($this->getRootUrl().'index/hashtag/_mapping -d',$action,json_encode($json));
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
    
        
    public function geoSearch($content,$latitude,$longitude, $distance, $userId) {
       $json = $this->geoSearchJson($content,$latitude,$longitude, $distance, $userId ); 
       $method = 'GET';
       $url = 'http://'.$this->host.':'.$this->port.'/resource/hashtag/_search?pretty&size=50'; //find a way to evalulat quantitiy
       return Curl::get($url, $method,$json );
 
    } 

   public function geoSearchJson($content,$latitude,$longitude, $distance, $userId, $notByMe = false ) {
       $match = array();
       if(is_array($content) && count($content)>0){
           $matches = array();
           foreach($content as $hashtag) {
                $matches[] = array('match'=>array('content'=>$hashtag));
           }
           $match = array(
               'query'=>
                   array(
                        'bool'=>array(
                            'should'=>$matches
                            )
                        )
                    );
             }
       if($content && !is_array($content)) {
           $match = array(
               'query'=>
                    array(
                        'match'=>array(
                            'content'=>$content
                        )
                    )
                );
       }
       if($notByMe){
           $notByMeFilter = 
               array('not'=>
                    array(
                        'term'=>
                            array(
                                'userid'=>$userId
                            )
                        )
                );
       }    
       
       $dateFilter = array("or"=>array(
                                    array(
                                        "and"=>array(
                                                array("exists"=>array("field"=>"endDate")),
                                                array("range"=>array("startDate"=>array("lte"=>"now"))),
                                                array("range"=>array("endDate"=>array("gte"=>"now")))
                                        )   
                                    )
                                    ,
                                    array(
                                        "and"=>array(
                                                array("missing"=>array("field"=>"endDate")),
                                                array("range"=>array("startDate"=>array("lte"=>"now")))
                                        )
                                    )
                                )
                            );

       $reservedFilter =  array('bool'=>
                                array('should'=>
                                    array(
                                        array(
                                            "and"=>array(
                                                    array("exists"=>array("field"=>"reserved")),
                                                    array("term"=>array("reserved"=>true)),
                                                    array("term"=>array("reservedBy"=>$userId))
                                            )
                                        ),
                                        array(
                                            "and"=>array(
                                                   array("exists"=>array("field"=>"reserved")),
                                                   array("term"=>array("reserved"=>false))
                                                )
                                        ),
                                        array( "missing"=>array("field"=>"reserved"))
                                    )
                                )
                            );
    
       $must = array();
       $must[] = $dateFilter;
       $must[] = $reservedFilter;
       if($notByMe) {
            $must[] = $notByMeFilter;

       }
       
       $filter = array( 
           "filter"=>array(
               array(
                    "geo_distance"=>array(
                        "distance"=>$distance,
                        "geo"=>array(
                            "lat"=>$latitude,
                            "lon"=>$longitude,
                        )
                    )
                ),
                array(
                    "bool"=>array(
                        "must"=> $must
                    )
                )
            )
        );
       $query = array_merge($match,$filter);
       $tab = array(
           'query'=>array(
               'filtered'=>$query
           ),
           'sort'=>array(
                "_geo_distance"=>array(
                    'geo'=>array(
                        'lat'=>$latitude,
                        'lon'=>$longitude,
                    ),
                    'order'=>'asc',
                    'unit'=>'km',
                    'distance_type'=>'plane'
                )
            )   
        );
       $json = json_encode($tab);
       return $json;
       
   }

   public function placeAround($latitude, $longitude, $distance ) {
       $filter = array( 
           "filter"=>array(
               array(
                    "geo_distance"=>array(
                        "distance"=>$distance,
                        "geo"=>array(
                            "lat"=>$latitude,
                            "lon"=>$longitude,
                    )
                    )
                )
            )
        );
        
       $tab = array(
           'query'=>array(
               'filtered'=>$filter
           ),
           'sort'=>array(
                "_geo_distance"=>array(
                    'geo'=>array(
                        'lat'=>$latitude,
                        'lon'=>$longitude,
                    ),
                    'order'=>'asc',
                    'unit'=>'km',
                    'distance_type'=>'plane'
                )
            )   
        );
       $url = 'http://'.$this->host.':'.$this->port.'/resource/place/_search?pretty&size=50'; //find a way to evalulat quantitiy
       $json = json_encode($tab);
       $method = 'GET';
       return Curl::get($url, $method,$json );
   }

    public function delete(){
       $url = $this->getRootUrl().'resource/hashtag/_query';
       $method = 'DELETE';
       $data = json_encode(array('query'=>array('match'=>array('content'=>'cool'))));
       return Curl::get($url,$method,$data);
   }

   public function mapping(){
    
       //delete index
       $url = $this->getRootUrl().'resource';
       $method = 'DELETE';
       Curl::get($url,$method,'');

       //mapping for completion:
       $url = $this->getRootUrl().'resource';
       $method = 'PUT';
       Curl::get($url.json_encode($this->autocompletMapping('hashtag')));


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

       Curl::get($this->getRootUrl().'sim/hastag/_mapping -d', 'PUT', json_encode($mapauto));
       
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
                ),
                  
            )
        );
        

                          
       $url = $this->getRootUrl().'resource';
       $method = 'PUT';
       $json = json_encode( array_merge($settings, $mappings)
       );
       return Curl::get($url,$method,$json);
   }
}



