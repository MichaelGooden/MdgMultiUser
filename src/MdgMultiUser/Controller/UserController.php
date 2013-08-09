<?php
/**
 * ZF2 module to allow multiple concurrent instances of ZfcUser, for independant user systems.
 *
 * @link      http://github.com/MichaelGooden/MdgMultiUser for the canonical source repository
 * @copyright Copyright (c) 2013 Michael Gooden (http://michaelgooden.github.io)
 * @license   http://michaelgooden.github.io/license/BSD-3-Clause.txt BSD 3-Clause License
 */
namespace MdgMultiUser\Controller;

use MdgMultiUser\Options\UserControllerOptionsInterface;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\ResponseInterface as Response;
use ZfcUser\Service\User as UserService;

class UserController extends AbstractActionController
{

    /**
     *
     * @var UserService
     */
    protected $userService;

    /**
     *
     * @var Form
     */
    protected $loginForm;

    /**
     *
     * @var Form
     */
    protected $registerForm;

    /**
     *
     * @var Form
     */
    protected $changePasswordForm;

    /**
     *
     * @var Form
     */
    protected $changeEmailForm;

    /**
     *
     * @todo Make this dynamic / translation-friendly
     * @var string
     */
    protected $failedLoginMessage = 'Authentication failed. Please try again.';

    /**
     *
     * @var UserControllerOptionsInterface
     */
    protected $options;

    /**
     * User page
     */
    public function indexAction()
    {
        if (! $this->mdgMultiUserAuthentication($this->getOptions()
            ->getAlias())
            ->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()
                ->getRouteLogin());
        }
        return array(
            'alias' => $this->getOptions()->getAlias(),
            'routeLogout' => $this->getOptions()->getRouteLogout()
        );
    }

    /**
     * Login form
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $form = $this->getLoginForm();

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        if (! $request->isPost()) {
            return array(
                'loginForm' => $form,
                'redirect' => $redirect,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
                'routeLogin' => $this->getOptions()->getRouteLogin(),
                'routeRegister' => $this->getOptions()->getRouteRegister()
            );
        }

        $form->setData($request->getPost());

        if (! $form->isValid()) {
            $this->flashMessenger()
                ->setNamespace('mdgmultiuser-' . $this->getOptions()
                ->getAlias() . '-login-form')
                ->addMessage($this->failedLoginMessage);
            return $this->redirect()->toUrl($this->url()
                ->fromRoute($this->getOptions()
                ->getRouteLogin()) . ($redirect ? '?redirect=' . $redirect : ''));
        }

        // clear adapters
        $this->mdgMultiUserAuthentication($this->getOptions()
            ->getAlias())
            ->getAuthAdapter()
            ->resetAdapters();
        $this->mdgMultiUserAuthentication($this->getOptions()
            ->getAlias())
            ->getAuthService()
            ->clearIdentity();

        return $this->forward()->dispatch($this->getOptions()
            ->getControllerName(), array(
            'action' => 'authenticate'
        ));
    }

    /**
     * Logout and clear the identity
     */
    public function logoutAction()
    {
        $this->mdgMultiUserAuthentication($this->getOptions()
            ->getAlias())
            ->getAuthAdapter()
            ->resetAdapters();
        $this->mdgMultiUserAuthentication($this->getOptions()
            ->getAlias())
            ->getAuthAdapter()
            ->logoutAdapters();
        $this->mdgMultiUserAuthentication($this->getOptions()
            ->getAlias())
            ->getAuthService()
            ->clearIdentity();

        $redirect = $this->params()->fromPost('redirect', $this->params()
            ->fromQuery('redirect', false));

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return $this->redirect()->toUrl($redirect);
        }

        return $this->redirect()->toRoute($this->getOptions()
            ->getLogoutRedirectRoute());
    }

    /**
     * General-purpose authentication action
     */
    public function authenticateAction()
    {
        if ($this->mdgMultiUserAuthentication($this->getOptions()
            ->getAlias())
            ->getAuthService()
            ->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()
                ->getLoginRedirectRoute());
        }
        $adapter = $this->mdgMultiUserAuthentication($this->getOptions()
            ->getAlias())
            ->getAuthAdapter();
        $redirect = $this->params()->fromPost('redirect', $this->params()
            ->fromQuery('redirect', false));

        $result = $adapter->prepareForAuthentication($this->getRequest());

        // Return early if an adapter returned a response
        if ($result instanceof Response) {
            return $result;
        }

        $auth = $this->mdgMultiUserAuthentication($this->getOptions()
            ->getAlias())
            ->getAuthService()
            ->authenticate($adapter);

        if (! $auth->isValid()) {
            $this->flashMessenger()
                ->setNamespace('mdgmultiuser-' . $this->getOptions()
                ->getAlias() . '-login-form')
                ->addMessage($this->failedLoginMessage);
            $adapter->resetAdapters();
            return $this->redirect()->toUrl($this->url()
                ->fromRoute($this->getOptions()
                ->getRouteLogin()) . ($redirect ? '?redirect=' . $redirect : ''));
        }

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return $this->redirect()->toUrl($redirect);
        }

        return $this->redirect()->toRoute($this->getOptions()
            ->getLoginRedirectRoute());
    }

    /**
     * Register new user
     */
    public function registerAction()
    {
        // if the user is logged in, we don't need to register
        if ($this->mdgMultiUserAuthentication($this->getOptions()
            ->getAlias())
            ->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()
                ->getLoginRedirectRoute());
        }
        // if registration is disabled
        if (! $this->getOptions()->getEnableRegistration()) {
            return array(
                'enableRegistration' => false
            );
        }

        $request = $this->getRequest();
        $service = $this->getUserService();
        $form = $this->getRegisterForm();

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        $redirectUrl = $this->url()->fromRoute($this->getOptions()
            ->getRouteRegister()) . ($redirect ? '?redirect=' . $redirect : '');
        $prg = $this->prg($redirectUrl, true);

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'registerForm' => $form,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
                'redirect' => $redirect,
                'routeRegister' => $this->getOptions()->getRouteRegister()
            );
        }

        $post = $prg;
        $user = $service->register($post);

        $redirect = isset($prg['redirect']) ? $prg['redirect'] : null;

        if (! $user) {
            return array(
                'registerForm' => $form,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
                'redirect' => $redirect,
                'routeRegister' => $this->getOptions()->getRouteRegister()
            );
        }

        if ($service->getOptions()->getLoginAfterRegistration()) {
            $identityFields = $service->getOptions()->getAuthIdentityFields();
            if (in_array('email', $identityFields)) {
                $post['identity'] = $user->getEmail();
            } elseif (in_array('username', $identityFields)) {
                $post['identity'] = $user->getUsername();
            }
            $post['credential'] = $post['password'];
            $request->setPost(new Parameters($post));
            return $this->forward()->dispatch($this->getOptions()
                ->getControllerName(), array(
                'action' => 'authenticate'
            ));
        }

        // TODO: Add the redirect parameter here...
        return $this->redirect()->toUrl($this->url()
            ->fromRoute($this->getOptions()
            ->getRouteLogin()) . ($redirect ? '?redirect=' . $redirect : ''));
    }

    /**
     * Change the users password
     */
    public function changepasswordAction()
    {
        // if the user isn't logged in, we can't change password
        if (! $this->mdgMultiUserAuthentication($this->getOptions()
            ->getAlias())
            ->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()
                ->getLoginRedirectRoute());
        }

        $form = $this->getChangePasswordForm();
        $prg = $this->prg($this->getOptions()
            ->getRouteChangePassword());

        $fm = $this->flashMessenger()
            ->setNamespace('mdgmultiuser-' . $this->getOptions()
            ->getAlias() . '-change-password')
            ->getMessages();
        if (isset($fm[0])) {
            $status = $fm[0];
        } else {
            $status = null;
        }

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'status' => $status,
                'changePasswordForm' => $form,
                'alias' => $this->getOptions()->getAlias(),
                'routeChangePassword' => $this->getOptions()->getRouteChangePassword()
            );
        }

        $form->setData($prg);

        if (! $form->isValid()) {
            return array(
                'status' => false,
                'changePasswordForm' => $form,
                'alias' => $this->getOptions()->getAlias(),
                'routeChangePassword' => $this->getOptions()->getRouteChangePassword()
            );
        }

        if (! $this->getUserService()->changePassword($form->getData())) {
            return array(
                'status' => false,
                'changePasswordForm' => $form,
                'alias' => $this->getOptions()->getAlias(),
                'routeChangePassword' => $this->getOptions()->getRouteChangePassword()
            );
        }

        $this->flashMessenger()
            ->setNamespace('mdgmultiuser-' . $this->getOptions()
            ->getAlias() . '-change-password')
            ->addMessage(true);
        return $this->redirect()->toRoute($this->getOptions()
            ->getRouteChangePassword());
    }

    public function changeEmailAction()
    {
        // if the user isn't logged in, we can't change email
        if (! $this->mdgMultiUserAuthentication($this->getOptions()
            ->getAlias())
            ->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()
                ->getLoginRedirectRoute());
        }

        $form = $this->getChangeEmailForm();
        $request = $this->getRequest();
        $request->getPost()->set('identity', $this->getUserService()
            ->getAuthService()
            ->getIdentity()
            ->getEmail());

        $fm = $this->flashMessenger()
            ->setNamespace('change-email')
            ->getMessages();
        if (isset($fm[0])) {
            $status = $fm[0];
        } else {
            $status = null;
        }

        $prg = $this->prg($this->getOptions()
            ->getRouteChangeEmail());
        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'status' => $status,
                'changeEmailForm' => $form,
                'alias' => $this->getOptions()->getAlias(),
                'routeChangeEmail' => $this->getOptions()->getRouteChangeEmail()
            );
        }

        $form->setData($prg);

        if (! $form->isValid()) {
            return array(
                'status' => false,
                'changeEmailForm' => $form,
                'alias' => $this->getOptions()->getAlias(),
                'routeChangeEmail' => $this->getOptions()->getRouteChangeEmail()
            );
        }

        $change = $this->getUserService()->changeEmail($prg);

        if (! $change) {
            $this->flashMessenger()
                ->setNamespace('mdgmultiuser-' . $this->getOptions()
                ->getAlias() . '-change-email')
                ->addMessage(false);
            return array(
                'status' => false,
                'changeEmailForm' => $form,
                'alias' => $this->getOptions()->getAlias(),
                'routeChangeEmail' => $this->getOptions()->getRouteChangeEmail()
            );
        }

        $this->flashMessenger()
            ->setNamespace('mdgmultiuser-' . $this->getOptions()
            ->getAlias() . '-change-email')
            ->addMessage(true);
        return $this->redirect()->toRoute($this->getOptions()
            ->getRouteChangeEmail());
    }

    /**
     * Getters/setters for DI stuff
     */
    public function getUserService()
    {
        return $this->userService;
    }

    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
        return $this;
    }

    public function getRegisterForm()
    {
        return $this->registerForm;
    }

    public function setRegisterForm(Form $registerForm)
    {
        $this->registerForm = $registerForm;
    }

    public function getLoginForm()
    {
        return $this->loginForm;
    }

    public function setLoginForm(Form $loginForm)
    {
        $this->loginForm = $loginForm;
        $fm = $this->flashMessenger()
            ->setNamespace('mdgmultiuser-' . $this->getOptions()
            ->getAlias() . '-login-form')
            ->getMessages();
        if (isset($fm[0])) {
            $this->loginForm->setMessages(array(
                'identity' => array(
                    $fm[0]
                )
            ));
        }
        return $this;
    }

    public function getChangePasswordForm()
    {
        return $this->changePasswordForm;
    }

    public function setChangePasswordForm(Form $changePasswordForm)
    {
        $this->changePasswordForm = $changePasswordForm;
        return $this;
    }

    /**
     * set options
     *
     * @param UserControllerOptionsInterface $options
     * @return UserController
     */
    public function setOptions(UserControllerOptionsInterface $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * get options
     *
     * @return UserControllerOptionsInterface
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get changeEmailForm.
     *
     * @return changeEmailForm.
     */
    public function getChangeEmailForm()
    {
        return $this->changeEmailForm;
    }

    /**
     * Set changeEmailForm.
     *
     * @param
     *            changeEmailForm the value to set.
     */
    public function setChangeEmailForm($changeEmailForm)
    {
        $this->changeEmailForm = $changeEmailForm;
        return $this;
    }
}
