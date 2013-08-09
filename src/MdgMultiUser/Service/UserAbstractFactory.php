<?php
/**
 * ZF2 module to allow multiple concurrent instances of ZfcUser, for independant user systems.
 *
 * @link      http://github.com/MichaelGooden/MdgMultiUser for the canonical source repository
 * @copyright Copyright (c) 2013 Michael Gooden (http://michaelgooden.github.io)
 * @license   http://michaelgooden.github.io/license/BSD-3-Clause.txt BSD 3-Clause License
 */
namespace MdgMultiUser\Service;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Service\User as ZfcUserService;

class UserAbstractFactory implements AbstractFactoryInterface
{

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (strpos($name, 'mdgmultiuser.userservice.') !== false);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $ourName = substr($name, 25);
        $service = new ZfcUserService();
        $service->setAuthService($serviceLocator->get('mdgmultiuser.auth_service.' . $ourName));
        $service->setOptions($serviceLocator->get('mdgmultiuser.module_options.' . $ourName));
        $service->setChangePasswordForm($serviceLocator->get('mdgmultiuser.change_password_form.' . $ourName));
        $service->setRegisterForm($serviceLocator->get('mdgmultiuser.register_form.' . $ourName));
        $service->setUserMapper($serviceLocator->get('mdgmultiuser.user_mapper.' . $ourName));
        return $service;
    }
}
