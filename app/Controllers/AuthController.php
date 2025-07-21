<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Session;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin(): void
    {
        if (Session::has('user_id')) {
            $this->redirect('/');
        }

        $this->view('auth.login', [
            'title' => 'Login - FotoApp',
        ]);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], $_POST);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old_email', $email);
            $this->back();

            return;
        }

        $user = $this->userModel->authenticate($email, $password);

        if (!$user) {
            Session::flash('error', 'Invalid email or password.');
            Session::flash('old_email', $email);
            $this->back();

            return;
        }

        Session::regenerateId();
        Session::put('user_id', $user['id']);
        Session::put('user_name', $user['name']);
        Session::put('user_email', $user['email']);

        Session::flash('success', 'Welcome back, ' . $user['name'] . '!');
        $this->redirect('/');
    }

    public function showRegister(): void
    {
        if (Session::has('user_id')) {
            $this->redirect('/');
        }

        $this->view('auth.register', [
            'title' => 'Register - FotoApp',
        ]);
    }

    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        $errors = $this->validate([
            'name' => 'required|min:2|max:60',
            'email' => 'required|email|max:255',
            'password' => 'required|min:8|confirmed',
        ], $_POST);

        if ($this->userModel->emailExists($email)) {
            $errors['email'] = 'The email has already been taken.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old_name', $name);
            Session::flash('old_email', $email);
            $this->back();

            return;
        }

        $userId = $this->userModel->create($name, $email, $password);

        Session::regenerateId();
        Session::put('user_id', $userId);
        Session::put('user_name', $name);
        Session::put('user_email', $email);

        Session::flash('success', 'Welcome to FotoApp, ' . $name . '!');
        $this->redirect('/');
    }

    public function logout(): void
    {
        $userName = Session::get('user_name');

        Session::destroy();
        Session::flash('success', 'Goodbye, ' . $userName . '!');

        $this->redirect('/');
    }
}