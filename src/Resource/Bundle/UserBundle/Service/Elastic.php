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

    public function index($index,$type,$data){
        $json_array = json_decode($data,true);
        return Curl::get($this->getUrl($index,$type,$json_array['id']),'PUT',$data);
    }

     public function update($index,$type,$data){
        $json_array = json_decode($data,true);
        return Curl::get($this->getUrl($index,$type,$json_array['id']),'POST',$data, true);
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

    public function geoSearh($content,$latitude,$longitude, $distance, $userId) {
       $json = $this->geoSearchJson($content,$latitude,$longitude, $distance, $userId ); 
       $method = 'GET';
       $url = 'http://'.$this->host.':'.$this->port.'/resource/hashtag/_search?pretty&size=50'; //find a way to evalulat quantitiy
       return Curl::get($url, $method,$json );
 
    } 

   public function geoSearchJson($content,$latitude,$longitude, $distance, $userId ) {
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
                        "must"=>array(
                            array("or"=>array(
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
                            ),
                            array('bool'=>
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
                            )
                        )
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

    protected function getRootUrl(){
        return 'http://'.$this->host.':'.$this->port.'/';
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
       
       $tab = array('mappings'=>
           array(
               'hashtag'=>
                array('properties'=>
                    array(
                        'content'=>array('type'=>'string'),
                        'geo'=>array('type'=>'geo_point'),
                        'startDate'=>array(
                            'type'=>'date',
                            'format'=>'basicDateTimeNoMillis'
                         ),
                        'endDate'=>array(
                            'type'=>'date',
                            'format'=>'basicDateTimeNoMillis'
                         ),
                    )
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
       $json = json_encode($tab);
       return Curl::get($url,$method,$json);
   }
}

