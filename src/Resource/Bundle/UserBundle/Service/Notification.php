<?php 

namespace Resource\Bundle\UserBundle\Service;

use Sly\NotificationPusher\PushManager;
use Sly\NotificationPusher\Adapter\Gcm as GcmAdapter;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\Device;
use Sly\NotificationPusher\Model\Message;
use Sly\NotificationPusher\Model\Push;

class Notification {

    public static function send($regId, $message ) {
        
        $pushManager = new PushManager(PushManager::ENVIRONMENT_DEV);

        // Then declare an adapter.
        // Only for android for the test.
        $gcmAdapter = new GcmAdapter(array(
            'apiKey' => 'AIzaSyBtzgwOHGYK5i2w5GAh-pF2bdlA5qPxhhs', // ugly, I Know !
        ));

        // Set the device(s) to push the notification to.
        $devices = new DeviceCollection(array(
            new Device($regId),
        ));

        // Then, create the push skel.
        $message = new Message($message);

        // Finally, create and add the push to the manager, and push it!
        $push = new Push($gcmAdapter, $devices, $message);
        $pushManager->add($push);
        $pushManager->push();
            
    }    


}
