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
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl($index,$type,$json_array['id']));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

   protected function getUrl($index,$type,$indexNumber){
        return 'http://'.$this->host.':'.$this->port.'/resource/hashtag/'.$indexNumber;
   }

   public function geoSearch($content,$latitude,$longitude, $distance) {
       $match = array();
       if($content) {
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
                    )
                )

        );
       $query = array_merge($match,$filter);
    
       $tab = array(
           'query'=>array(
               'filtered'=>$query
           )    
       );
       $url = 'http://'.$this->host.':'.$this->port.'/resource/hashtag/_search?pretty';
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
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
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

