<?php
/**
 * ZF2 module to allow multiple concurrent instances of ZfcUser, for independant user systems.
 *
 * @link      http://github.com/MichaelGooden/MdgMultiUser for the canonical source repository
 * @copyright Copyright (c) 2013 Michael Gooden (http://michaelgooden.github.io)
 * @license   http://michaelgooden.github.io/license/BSD-3-Clause.txt BSD 3-Clause License
 */
namespace MdgMultiUser\Controller;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserControllerAbstractFactory implements AbstractFactoryInterface
{

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (strpos($name, 'mdgmultiuser.') !== false);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $ourName = substr($name, 13);
        $serviceLocator = $serviceLocator->getServiceLocator();
        $controller = new UserController();
        $controller->setOptions($serviceLocator->get('mdgmultiuser.module_options.' . $ourName));
        $controller->setUserService($serviceLocator->get('mdgmultiuser.user_service.' . $ourName));
        $controller->setChangeEmailForm($serviceLocator->get('mdgmultiuser.change_email_form.' . $ourName));
        $controller->setChangePasswordForm($serviceLocator->get('mdgmultiuser.change_password_form.' . $ourName));
        $controller->setLoginForm($serviceLocator->get('mdgmultiuser.login_form.' . $ourName));
        $controller->setRegisterForm($serviceLocator->get('mdgmultiuser.register_form.' . $ourName));
        return $controller;
    }
}
