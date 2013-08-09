<?php
/**
 * ZF2 module to allow multiple concurrent instances of ZfcUser, for independant user systems.
 *
 * @link      http://github.com/MichaelGooden/MdgMultiUser for the canonical source repository
 * @copyright Copyright (c) 2013 Michael Gooden (http://michaelgooden.github.io)
 * @license   http://michaelgooden.github.io/license/BSD-3-Clause.txt BSD 3-Clause License
 */
namespace MdgMultiUser\Mapper;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Mapper\User as ZfcUserMapper;
use ZfcUser\Mapper\UserHydrator;

class UserAbstractFactory implements AbstractFactoryInterface
{

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (strpos($name, 'mdgmultiuser.usermapper.') !== false);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $ourName = substr($name, 24);
        $options = $serviceLocator->get('mdgmultiuser.module_options.' . $ourName);
        $mapper = new ZfcUserMapper();
        if ($serviceLocator->has('mdgmultiuser.zend_db_adapter.' . $ourName)) {
            $dbAdapter = $serviceLocator->get('mdgmultiuser.zend_db_adapter.' . $ourName);
        } else {
            $dbAdapter = $serviceLocator->get('zfcuser_zend_db_adapter');
        }
        $mapper->setDbAdapter($dbAdapter);
        $entityClass = $options->getUserEntityClass();
        $mapper->setEntityPrototype(new $entityClass());
        $mapper->setHydrator(new UserHydrator());
        $mapper->setTableName($options->getTableName());
        return $mapper;
    }
}
