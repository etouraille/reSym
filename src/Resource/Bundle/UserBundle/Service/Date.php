<?php
namespace Resource\Bundle\UserBundle\Service;

class Date {

    protected $format = 'Ymd\THisP';
    protected $secondsInAMinute = 60;

    public function __construct() {
        //date_default_timezone_set('UTC');
    }

    public function now() {
        return date($this->format);
    }

    public function getFormat() {
        return $this->format;
    }

    public function inMinutes($minutes,$date=null) {
        if(isset($date)) {
            $now = $date;
        } else {
            $now = $this->now();
        }
        return date(
            $this->format, 
            strtotime($now)+$minutes*$this->secondsInAMinute
        );
    }
}
