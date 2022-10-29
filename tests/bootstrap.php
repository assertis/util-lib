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
    public function accepts($value): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function deserializeItem($data): mixed
    {
        return $data;
    }
}

class ObjectListNeverAccept extends ObjectList
{
    /**
     * @inheritdoc
     */
    public function accepts($value): bool
    {
        return false;
    }
}

class TypedMapAlwaysAccept extends TypedMap
{
    /**
     * @inheritdoc
     */
    public function accepts($value): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function deserializeKey($data): mixed
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public static function deserializeValue($data): mixed
    {
        return $data;
    }
}

class TypedMapNeverAccept extends TypedMap
{
    /**
     * @inheritdoc
     */
    public function accepts($value): bool
    {
        return false;
    }
}

class TypedMapNeverAcceptKey extends TypedMap
{
    /**
     * @inheritdoc
     */
    public function accepts($value): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function acceptsKey($value): bool
    {
        return false;
    }
}
