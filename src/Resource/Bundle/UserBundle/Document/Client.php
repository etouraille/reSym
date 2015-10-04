<?php

/**
 * Created by PhpStorm.
 * User: Paulisse
 * Date: 31/07/2015
 * Time: 00:06
 */
namespace Resource\Bundle\UserBundle\Document;

use FOS\OAuthServerBundle\Document\Client as BaseClient;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 **/

class Client extends BaseClient
{
    /**
     * @MongoDB\Id
     **/
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }
}