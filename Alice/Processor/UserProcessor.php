<?php

namespace FixtureBundle\Alice\Processor;

use Fidry\AliceDataFixtures\ProcessorInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\User;
use Pimcore\Tool;

class UserProcessor implements ProcessorInterface
{
    /**
     * Processes an object before it is persisted to DB
     *
     * @param AbstractObject|Concrete $object instance to process
     */
    public function preProcess(string $id, $object): void
    {
        if ($object instanceof User) {
            $encryptedPass = Tool\Authentication::getPasswordHash($object->getName(), $object->getPassword());
            $object->setPassword($encryptedPass);
        }

    }

    /**
     * Processes an object before it is persisted to DB
     *
     * @param AbstractObject|Concrete $object instance to process
     */
    public function postProcess(string $id, $object): void
    {
    }
}
