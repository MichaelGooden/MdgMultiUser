<?php
/**
 * ZF2 module to allow multiple concurrent instances of ZfcUser, for independant user systems.
 *
 * @link      http://github.com/MichaelGooden/MdgMultiUser for the canonical source repository
 * @copyright Copyright (c) 2013 Michael Gooden (http://michaelgooden.github.io)
 * @license   http://michaelgooden.github.io/license/BSD-3-Clause.txt BSD 3-Clause License
 */
namespace MdgMultiUser\Form;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Form\Register;
use ZfcUser\Form\RegisterFilter;
use ZfcUser\Validator\NoRecordExists;

class RegisterAbstractFactory implements AbstractFactoryInterface
{

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (strpos($name, 'mdgmultiuser.registerform.') !== false);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $ourName = substr($name, 26);
        $options = $serviceLocator->get('mdgmultiuser.module_options.' . $ourName);
        $form = new Register(null, $options);
        /* leaving this fragment as is in the zfcuser implementation, in case
         * someone wants to reimplement captchas
         */
        // $form->setCaptchaElement($sm->get('zfcuser_captcha_element'));
        $userMapper = $serviceLocator->get('mdgmultiuser.user_mapper.' . $ourName);
        $form->setInputFilter(new RegisterFilter(new NoRecordExists(array(
            'mapper' => $userMapper,
            'key' => 'email'
        )), new NoRecordExists(array(
            'mapper' => $userMapper,
            'key' => 'username'
        )), $options));
        return $form;
    }
}
