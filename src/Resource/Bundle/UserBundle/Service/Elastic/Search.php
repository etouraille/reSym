<?php
namespace Resource\Bundle\UserBundle\Service\Elastic;

use Resource\Bundle\UserBundle\Service\Curl;
use Resource\Bundle\UserBundle\Service\Elastic;

class Search extends Elastic{

    public function geoSearch($content,$latitude,$longitude, $distance, $userId) {
    $json = $this->geoSearchJson($content,$latitude,$longitude, $distance, $userId ); 
       $method = 'GET';
       $url = 'http://'.$this->host.':'.$this->port.'/resource/hashtag/_search?pretty&size=50'; //find a way to evalulat quantitiy
       return Curl::get($url, $method,$json );
 
    } 

    protected function geoSearchJson($content,$latitude,$longitude, $distance, $userId, $notByMe = false )
    {
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


}
