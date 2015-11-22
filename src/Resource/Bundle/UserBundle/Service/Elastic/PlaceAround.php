<?php
namespace Resource\Bundle\UserBundle\Service\Elastic;

use Resource\Bundle\UserBundle\Service\Curl;
class PlaceAround extends Elastic {
    
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

  


