<?php
namespace Resource\Bundle\UserBundle\Service\Elastic;

use Resource\Bundle\UserBundle\Service\Curl;
class Percolator extends Elastic {

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
}



