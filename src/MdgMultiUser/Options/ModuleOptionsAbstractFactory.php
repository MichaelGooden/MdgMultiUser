<?php
/**
 * ZF2 module to allow multiple concurrent instances of ZfcUser, for independant user systems.
 *
 * @link      http://github.com/MichaelGooden/MdgMultiUser for the canonical source repository
 * @copyright Copyright (c) 2013 Michael Gooden (http://michaelgooden.github.io)
 * @license   http://michaelgooden.github.io/license/BSD-3-Clause.txt BSD 3-Clause License
 */
namespace MdgMultiUser\Options;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ModuleOptionsAbstractFactory implements AbstractFactoryInterface
{

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (strpos($name, 'mdgmultiuser.moduleoptions.') !== false);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $ourName = substr($name, 27);
        $config = $serviceLocator->get('Config');
        $moduleOptions = new ModuleOptions(isset($config['mdgmultiuser'][$ourName]) ? $config['mdgmultiuser'][$ourName] : array());
        if (! isset($config['mdgmultiuser'][$ourName]['alias'])) {
            $moduleOptions->setAlias($ourName);
        }
        if (! isset($config['mdgmultiuser'][$ourName]['controller_name'])) {
            $moduleOptions->setControllerName('mdgmultiuser.' . $ourName);
        }
        if (! isset($config['mdgmultiuser'][$ourName]['auth_adapters'])) {
            $moduleOptions->setAuthAdapters(array(
                100 => 'mdgmultiuser.auth_adapter_db.' . $ourName
            ));
        }
        return $moduleOptions;
    }
}
