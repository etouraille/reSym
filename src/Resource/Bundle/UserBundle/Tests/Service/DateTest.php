<?php
namespace Resource\Bundle\UserBundle\Test\Service;

use Resource\Bundle\UserBundle\Service\Date;

class DateTest extends \PHPUnit_Framework_TestCase {
    
    public function testInMinutes(){
        
        $date = new Date();
        $now = '20150212T000000Z';
        $this->assertEquals('20150212T001000Z',$date->inMinutes(10,$now));
    
    }

    public function testInZeroMinutes() {
        $date = new Date();
        $now = '20150212T000000Z';
        $this->assertEquals($now, $date->inMinutes(0,$now));
    }

    public function testFormat(){
        
        $date = new Date();
        $now = $date->now();
        $year = date('Y');
        $h = date('H');
        $this->assertEquals(1,preg_match('#^'.$year.'[0-9]{4}T'.$h.'[0-9]{4}Z$#',$now));
         
    }

    public function testMidnigth() {
        $date = new Date();
        $format = $date->getFormat();
        $date = date($format,mktime(0,0,0,2,12,1977));
        $this->assertEquals('19770212T000000Z',$date);
    
    }

}
