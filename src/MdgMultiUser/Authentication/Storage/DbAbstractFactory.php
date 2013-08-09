<?php
/**
 * ZF2 module to allow multiple concurrent instances of ZfcUser, for independant user systems.
 *
 * @link      http://github.com/MichaelGooden/MdgMultiUser for the canonical source repository
 * @copyright Copyright (c) 2013 Michael Gooden (http://michaelgooden.github.io)
 * @license   http://michaelgooden.github.io/license/BSD-3-Clause.txt BSD 3-Clause License
 */
namespace MdgMultiUser\Authentication\Storage;

use Zend\Authentication\Storage\Session;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Authentication\Storage\Db as DbStorage;

class DbAbstractFactory implements AbstractFactoryInterface
{

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (strpos($name, 'mdgmultiuser.authstoragedb.') !== false);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $ourName = substr($name, 27);
        $dbStorage = new DbStorage();
        $dbStorage->setMapper($serviceLocator->get('mdgmultiuser.user_mapper.' . $ourName));
        $dbStorage->setStorage(new Session('MdgMultiUser_Auth_' . $ourName));
        return $dbStorage;
    }
}
