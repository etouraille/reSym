<?php
/**
 * Created by PhpStorm.
 * User: Paulisse
 * Date: 31/07/2015
 * Time: 00:13
 */
namespace Resource\Bundle\UserBundle\Document;

use FOS\OAuthServerBundle\Document\AuthCode as BaseAuthCode;
use FOS\OAuthServerBundle\Model\ClientInterface;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 **/

class AuthCode extends BaseAuthCode
{

    /**
     * @MongoDB\Id
     **/
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Client")
     **/
    protected $client;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User")
     */
    protected $user;

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

}