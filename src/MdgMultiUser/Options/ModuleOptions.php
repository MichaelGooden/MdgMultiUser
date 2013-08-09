<?php
namespace MdgMultiUser\Options;

class ModuleOptions extends \ZfcUser\Options\ModuleOptions implements UserControllerOptionsInterface, ModuleOptionsInterface
{

    /**
     *
     * @var string
     */
    protected $routeChangePassword = 'unset';

    /**
     *
     * @var string
     */
    protected $routeLogin = 'unset';

    /**
     *
     * @var string
     */
    protected $routeRegister = 'unset';

    /**
     *
     * @var string
     */
    protected $routeChangeEmail = 'unset';

    /**
     *
     * @var string
     */
    protected $routeLogout = 'unset';

    /**
     *
     * @var string
     */
    protected $controllerName = 'unset';

    /**
     *
     * @var string
     */
    protected $alias;

    /**
     *
     * @return string the $routeChangePassword
     */
    public function getRouteChangePassword()
    {
        return $this->routeChangePassword;
    }

    /**
     *
     * @param string $routeChangePassword
     */
    public function setRouteChangePassword($routeChangePassword)
    {
        $this->routeChangePassword = $routeChangePassword;
        return $this;
    }

    /**
     *
     * @return string the $routeLogin
     */
    public function getRouteLogin()
    {
        return $this->routeLogin;
    }

    /**
     *
     * @param string $routeLogin
     */
    public function setRouteLogin($routeLogin)
    {
        $this->routeLogin = $routeLogin;
        return $this;
    }

    /**
     *
     * @return string the $routeRegister
     */
    public function getRouteRegister()
    {
        return $this->routeRegister;
    }

    /**
     *
     * @param string $routeRegister
     */
    public function setRouteRegister($routeRegister)
    {
        $this->routeRegister = $routeRegister;
        return $this;
    }

    /**
     *
     * @return string the $routeChangeEmail
     */
    public function getRouteChangeEmail()
    {
        return $this->routeChangeEmail;
    }

    /**
     *
     * @param string $routeChangeEmail
     */
    public function setRouteChangeEmail($routeChangeEmail)
    {
        $this->routeChangeEmail = $routeChangeEmail;
        return $this;
    }

    /**
     *
     * @return string the $controllerName
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     *
     * @param string $controllerName
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
        return $this;
    }

    /**
     *
     * @return string the $alias
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     *
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     *
     * @return string the $routeLogout
     */
    public function getRouteLogout()
    {
        return $this->routeLogout;
    }

    /**
     *
     * @param string $routeLogout
     */
    public function setRouteLogout($routeLogout)
    {
        $this->routeLogout = $routeLogout;
        return $this;
    }
}