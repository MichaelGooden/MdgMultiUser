<?php
/**
 * ZF2 module to allow multiple concurrent instances of ZfcUser, for independant user systems.
 *
 * @link      http://github.com/MichaelGooden/MdgMultiUser for the canonical source repository
 * @copyright Copyright (c) 2013 Michael Gooden (http://michaelgooden.github.io)
 * @license   http://michaelgooden.github.io/license/BSD-3-Clause.txt BSD 3-Clause License
 */
namespace MdgMultiUser\Authentication\Adapter;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Authentication\Adapter\Db as DbAdapter;

class DbAbstractFactory implements AbstractFactoryInterface
{

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (strpos($name, 'mdgmultiuser.authadapterdb.') !== false);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $ourName = substr($name, 27);
        $dbAdapter = new DbAdapter();
        $dbAdapter->setOptions($serviceLocator->get('mdgmultiuser.module_options.' . $ourName));
        $dbAdapter->setMapper($serviceLocator->get('mdgmultiuser.user_mapper.' . $ourName));
        return $dbAdapter;
    }
}
