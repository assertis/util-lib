<?php

use Assertis\Util\ObjectList;

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

error_reporting(E_ALL);

class TestObjectList extends ObjectList
{
    /**
     * @inheritdoc
     */
    public function accepts($value)
    {
        return true;
    }
}
