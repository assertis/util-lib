<?php

use Assertis\Util\ObjectList;
use Assertis\Util\TypedMap;

$possibleAutoloadFiles = [
    __DIR__.'/../vendor/autoload.php',
    __DIR__.'/../../../autoload.php',
];

foreach ($possibleAutoloadFiles as $possibleAutoloadFile) {
    if (file_exists($possibleAutoloadFile)) {
        $loader = require $possibleAutoloadFile;
        $loader->addPsr4('Assertis\\Util\\', __DIR__);
    }
}

error_reporting(E_ALL ^ E_USER_DEPRECATED);

class ObjectListAlwaysAccept extends ObjectList
{
    /**
     * @inheritdoc
     */
    public function accepts($value)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function deserializeItem($data)
    {
        return $data;
    }
}

class ObjectListNeverAccept extends ObjectList
{
    /**
     * @inheritdoc
     */
    public function accepts($value)
    {
        return false;
    }
}

class TypedMapAlwaysAccept extends TypedMap
{
    /**
     * @inheritdoc
     */
    public function accepts($value)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function deserializeKey($data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public static function deserializeValue($data)
    {
        return $data;
    }
}

class TypedMapNeverAccept extends TypedMap
{
    /**
     * @inheritdoc
     */
    public function accepts($value)
    {
        return false;
    }
}

class TypedMapNeverAcceptKey extends TypedMap
{
    /**
     * @inheritdoc
     */
    public function accepts($value)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function acceptsKey($value)
    {
        return false;
    }
}
