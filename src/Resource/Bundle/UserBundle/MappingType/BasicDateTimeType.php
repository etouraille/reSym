<?php

namespace Resource\Bundle\UserBundle\MappingType;

use Doctrine\ODM\MongoDB\Types\Type;

/**
 * date type usefull for elastic basic date time type
 */
class BasicDateTimeType extends Type
{
    public function convertToPHPValue($value)
    {
        // Note: this function is only called when your custom type is used
        // as an identifier. For other cases, closureToPHP() will be called.
        return $value;
    }

    public function closureToPHP()
    {
        // Return the string body of a PHP closure that will receive $value
        // and store the result of a conversion in a $return variable
        return '$return = $value;';
    }

    public function convertToDatabaseValue($value)
    {
        // This is called to convert a PHP value to its Mongo equivalent
        return new \MongoDate(strtotime($value));
    }
}
