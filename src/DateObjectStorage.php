<?php

namespace Assertis\Util;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 *
 * implementation of SplObjectList which allow you to store only Date objects
 * as a storage keys.
 */
class DateObjectStorage extends \SplObjectStorage
{

    /**
     * @param object $object
     * @param null $data
     */
    public function offsetSet($object, $data = null)
    {
        if(!$object instanceof Date){
            throw new \InvalidArgumentException("DateObjectStorage can store only date objects");
        }
        parent::offsetSet($object, $data); // TODO: Change the autogenerated stub
    }

    /**
     * @param object $object
     * @param null $data
     */
    public function attach($object, $data = null)
    {
        if(!$object instanceof Date){
            throw new \InvalidArgumentException("DateObjectStorage can store only date objects");
        }
        parent::attach($object, $data); // TODO: Change the autogenerated stub
    }

    /**
     * @param \SplObjectStorage $storage
     */
    public function addAll($storage)
    {
        if(!$storage instanceof \SplObjectStorage){
            throw new \InvalidArgumentException("DateObjectStorage can be merge only with \\SplObjectStorage object.");
        }
        foreach ($storage as $element){
            if(!$element instanceof Date){
                throw new \InvalidArgumentException("DateObjectStorage can store only date objects");
            }
        }
        parent::addAll($storage); // TODO: Change the autogenerated stub
    }
}