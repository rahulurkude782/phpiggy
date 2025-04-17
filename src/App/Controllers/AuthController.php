<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\{ValidatorService, UserService};
use Framework\TemplateEngine;

class AuthController
{
    public function __construct(
        private TemplateEngine $view,
        private ValidatorService $validatorService,
        private UserService $userService
    ) {}

    public function registerView()
    {
        echo $this->view->render('register.php');
    }

    public function register()
    {
        $this->validatorService->validateRegister($_POST);
        $this->userService->isEmailExists($_POST['email']);
        $this->userService->create($_POST);

        redirectTo('/');
    }

    public function loginView()
    {
        echo $this->view->render('login.php');
    }

    public function login()
    {
        $data = $_POST;

        $this->validatorService->validateLogin($data);
        $this->userService->isValidCredentials($data);
        redirectTo('/');
    }

    public function logout(): void
    {
        $this->userService->logout();
        redirectTo('/login');
    }
}
