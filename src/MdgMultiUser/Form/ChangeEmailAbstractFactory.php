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
use ZfcUser\Form\ChangeEmail;
use ZfcUser\Form\ChangeEmailFilter;
use ZfcUser\Validator\NoRecordExists;

class ChangeEmailAbstractFactory implements AbstractFactoryInterface
{

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (strpos($name, 'mdgmultiuser.changeemailform.') !== false);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $ourName = substr($name, 29);
        $options = $serviceLocator->get('mdgmultiuser.module_options.' . $ourName);
        $form = new ChangeEmail(null, $options);
        $form->setInputFilter(new ChangeEmailFilter($options, new NoRecordExists(array(
            'mapper' => $serviceLocator->get('mdgmultiuser.user_mapper.' . $ourName),
            'key' => 'email'
        ))));
        return $form;
    }
}
