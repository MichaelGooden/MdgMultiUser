<?php
/**
 * ZF2 module to allow multiple concurrent instances of ZfcUser, for independant user systems.
 *
 * @link      http://github.com/MichaelGooden/MdgMultiUser for the canonical source repository
 * @copyright Copyright (c) 2013 Michael Gooden (http://michaelgooden.github.io)
 * @license   http://michaelgooden.github.io/license/BSD-3-Clause.txt BSD 3-Clause License
 */
namespace MdgMultiUser\View\Helper;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\View\Helper\AbstractHelper;

class MdgMultiUserLoginWidgetProxy extends AbstractHelper implements ServiceLocatorAwareInterface
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
            $pluginName = 'mdgmultiuser.user_login_widget_helper.' . $alias;
            if (!$this->getServiceLocator()->has($pluginName)) {
                throw new ServiceNotFoundException(sprintf(
                    '%s was unable to fetch or create an instance for %s',
                    get_class($this), $pluginName
                ));
            } else {
                $plugin = $this->getServiceLocator()->get($pluginName);
                return $plugin();
            }
        } else {
            $plugin = $this->getServiceLocator()->get('zfcUserLoginWidget');
            return $plugin();
        }
    }
}
