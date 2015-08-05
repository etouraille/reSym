<?php
/**
 * Created by PhpStorm.
 * User: Paulisse
 * Date: 31/07/2015
 * Time: 00:13
 */

namespace Resource\Bundle\UserBundle\Document;

use FOS\OAuthServerBundle\Document\AccessToken as BaseAccessToken;
use FOS\OAuthServerBundle\Model\ClientInterface;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */

class AccessToken extends BaseAccessToken
{

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\EmbedOne(targetDocument="Client")
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