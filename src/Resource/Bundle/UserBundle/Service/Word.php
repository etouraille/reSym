<?php

namespace Resource\Bundle\UserBundle\Service;

use Goutte\Client;

class Word {
    
    public static function getRandom(){
        $client = new Client();

        $text = 'anonymous';
        $crawler = $client->request('GET','http://www.dicodesrimes.com/hasard/');
        //$client->getClient()->setDefaultOption('config/curl/'.CURLOPT_TIMEOUT, 60);
        $text = $crawler->filter('html body div.container div.row div.col-md-12 div.panel.panel-info div.panel-body a.resultitem')->text();
        
        return self::accentCharsModifier($text);
    }

    static function  accentCharsModifier($str){
        if(($length=mb_strlen($str,"UTF-8"))<strlen($str)){
            $i=$count=0;
            while($i<$length){
                if(strlen($c=mb_substr($str,$i,1,"UTF-8"))>1){
                    $he=htmlentities($c); 
                    if(($nC=preg_replace("#&([A-Za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#", "\\1", $he))!=$he ||
                        ($nC=preg_replace("#&([A-Za-z]{2})(?:lig);#", "\\1", $he))!=$he ||
                        ($nC=preg_replace("#&[^;]+;#", "", $he))!=$he){
                        $str=str_replace($c,$nC,$str,$count);if($nC==""){$length=$length-$count;$i--;}
                    }
                }
                $i++;
            }
        }
        return $str;
    }

}

