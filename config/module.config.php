<?php
/**
 * ZF2 module to allow multiple concurrent instances of ZfcUser, for independant user systems.
 *
 * @link      http://github.com/MichaelGooden/MdgMultiUser for the canonical source repository
 * @copyright Copyright (c) 2013 Michael Gooden (http://michaelgooden.github.io)
 * @license   http://michaelgooden.github.io/license/BSD-3-Clause.txt BSD 3-Clause License
 */
return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'mdgmultiuser' => __DIR__ . '/../view',
        ),
    ),
    'controllers' => array(
        'abstract_factories' => array(
            'MdgMultiUser\Controller\UserControllerAbstractFactory'
        )
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'mdgMultiUserAuthentication' => 'MdgMultiUser\Controller\Plugin\MdgMultiUserAuthenticationProxy'
        ),
        'abstract_factories' => array(
            'MdgMultiUser\Controller\Plugin\MdgMultiUserAuthenticationAbstractFactory'
        )
    ),
    'view_helpers' => array(
        'invokables' => array(
            'mdgMultiUserDisplayName' => 'MdgMultiUser\View\Helper\MdgMultiUserDisplayNameProxy',
            'mdgMultiUserIdentity' => 'MdgMultiUser\View\Helper\MdgMultiUserIdentityProxy',
            'mdgMultiUserLoginWidget' => 'MdgMultiUser\View\Helper\MdgMultiUserLoginWidgetProxy'
        ),
        'abstract_factories' => array(
            'MdgMultiUser\View\Helper\MdgMultiUserDisplayNameAbstractFactory',
            'MdgMultiUser\View\Helper\MdgMultiUserIdentityAbstractFactory',
            'MdgMultiUser\View\Helper\MdgMultiUserLoginWidgetAbstractFactory'
        )
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'MdgMultiUser\Authentication\Adapter\AdapterChainAbstractFactory',
            'MdgMultiUser\Authentication\Adapter\DbAbstractFactory',
            'MdgMultiUser\Authentication\Storage\DbAbstractFactory',
            'MdgMultiUser\Authentication\AuthenticationServiceAbstractFactory',
            'MdgMultiUser\Form\ChangeEmailAbstractFactory',
            'MdgMultiUser\Form\ChangePasswordAbstractFactory',
            'MdgMultiUser\Form\LoginAbstractFactory',
            'MdgMultiUser\Form\RegisterAbstractFactory',
            'MdgMultiUser\Mapper\UserAbstractFactory',
            'MdgMultiUser\Options\ModuleOptionsAbstractFactory',
            'MdgMultiUser\Service\UserAbstractFactory'
        )
    )
);
