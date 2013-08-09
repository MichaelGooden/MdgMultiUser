<?php
/**
 * ZF2 module to allow multiple concurrent instances of ZfcUser, for independant user systems.
 *
 * @link      http://github.com/MichaelGooden/MdgMultiUser for the canonical source repository
 * @copyright Copyright (c) 2013 Michael Gooden (http://michaelgooden.github.io)
 * @license   http://michaelgooden.github.io/license/BSD-3-Clause.txt BSD 3-Clause License
 */
namespace MdgMultiUser\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class MdgMultiUserAuthenticationProxy extends AbstractPlugin implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function __invoke($alias = null)
    {
        if ($alias) {
            $pluginName = 'mdgmultiuser.authentication_plugin.' . $alias;
            if (!$this->getServiceLocator()->has($pluginName)) {
                throw new ServiceNotFoundException(sprintf(
                    '%s was unable to fetch or create an instance for %s',
                    get_class($this), $pluginName
                ));
            } else {
                return $this->getServiceLocator()->get($pluginName);
            }
        } else {
            return $this->getServiceLocator()->get('zfcUserAuthentication');
        }
    }
}
