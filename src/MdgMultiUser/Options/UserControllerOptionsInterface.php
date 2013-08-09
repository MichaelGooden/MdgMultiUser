<?php
namespace MdgMultiUser\Options;

interface UserControllerOptionsInterface extends \ZfcUser\Options\UserControllerOptionsInterface
{

    /**
     *
     * @return string the $routeChangePassword
     */
    public function getRouteChangePassword();

    /**
     *
     * @param string $routeChangePassword
     */
    public function setRouteChangePassword($routeChangePassword);

    /**
     *
     * @return string the $routeLogin
     */
    public function getRouteLogin();

    /**
     *
     * @param string $routeLogin
     */
    public function setRouteLogin($routeLogin);

    /**
     *
     * @return string the $routeRegister
     */
    public function getRouteRegister();

    /**
     *
     * @param string $routeRegister
     */
    public function setRouteRegister($routeRegister);

    /**
     *
     * @return string the $routeChangeEmail
     */
    public function getRouteChangeEmail();

    /**
     *
     * @param string $routeChangeEmail
     */
    public function setRouteChangeEmail($routeChangeEmail);

    /**
     *
     * @return string the $routeLogout
     */
    public function getRouteLogout();

    /**
     *
     * @param string $routeLogout
     */
    public function setRouteLogout($routeLogout);

    /**
     *
     * @return string the $controllerName
     */
    public function getControllerName();

    /**
     *
     * @param string $controllerName
     */
    public function setControllerName($controllerName);
}
