<?php
namespace Resource\Bundle\UserBundle\Service;

class Elastic {

    protected $host;
    protected $port;
    
    public function __construct($host='www.objetspartages.org', $port = '9200'){
        $this->host = $host;
        $this->port = $port;
    }

    public function index($index,$type,$data){
        $json_array = json_decode($data,true);
        return $this->getCurl($this->getUrl($index,$type,$json_array['id']),'PUT',$data);
    }

     public function update($index,$type,$data){
        $json_array = json_decode($data,true);
        return $this->getCurl($this->getUrl($index,$type,$json_array['id']),'POST',$data, true);
    }


    protected function getUrl($index,$type,$indexNumber,$isUpdate = false){
        $urlUpdate = '';
        if($isUpdate){
            $urlUpdate = '/_update';
        }
        return 'http://'.$this->host.':'.$this->port.'/resource/hashtag/'.$indexNumber.$urlUpdate;
   }

   public function geoSearch($content,$latitude,$longitude, $distance, $userId ) {
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
                    "geo_distance"=>array(
                        "distance"=>$distance,
                        "geo"=>array(
                            "lat"=>$latitude,
                            "lon"=>$longitude,
                        )
                    ),
                    "and"=>array(
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
                                            array("missing"=>array("field"=>"enDate")),
                                            array("range"=>array("startDate"=>array("lte"=>"now")))
                                    )
                                )
                            )
                        ),
                        array('or'=>array(
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
                                array( 
                                    'and'=>array(
                                           array( "missing"=>array("field"=>"reserved"))
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
       $url = 'http://'.$this->host.':'.$this->port.'/resource/hashtag/_search?pretty&size=50'; //find a way to evalulat quantitiy
       $json = json_encode($tab);
       $method = 'GET';
       return $this->getCurl($url, $method,$json );
   }

   protected  function getCurl($url,$method, $json){
         $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($json)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
   }

   protected function getRootUrl(){
        return 'http://'.$this->host.':'.$this->port.'/';
   }

   public function delete(){
       $url = $this->getRootUrl().'resource/hashtag/_query';
       $method = 'DELETE';
       $data = json_encode(array('query'=>array('match'=>array('content'=>'cool'))));
       return $this->getCurl($url,$method,$data);
   }

   public function mapping(){
    
       //delete index
       $url = $this->getRootUrl().'resource';
       $method = 'DELETE';
       $this->getCurl($url,$method,'');
       
       $tab = array('mappings'=>
           array('hashtag'=>
                array('properties'=>
                    array(
                        'content'=>array('type'=>'string'),
                        'geo'=>array('type'=>'geo_point')
                    )
                )   
            )
        );

       $url = $this->getRootUrl().'resource';
       $method = 'PUT';
       $json = json_encode($tab);
       return $this->getCurl($url,$method,$json);
   
   }

}

