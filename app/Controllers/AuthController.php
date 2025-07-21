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

        $userId = $this->userModel->createUser($name, $email, $password);

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

    public function showChangePassword(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'You must be logged in to change your password.');
            $this->redirect('/login');

            return;
        }

        $this->view('auth.change-password', [
            'title' => 'Change Password - FotoApp',
        ]);
    }

    public function changePassword(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'You must be logged in to change your password.');
            $this->redirect('/login');

            return;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['new_password_confirmation'] ?? '';

        $errors = $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], $_POST);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            $this->back();

            return;
        }

        $userId = Session::get('user_id');
        $success = $this->userModel->changePassword($userId, $currentPassword, $newPassword);

        if (!$success) {
            Session::flash('error', 'Current password is incorrect.');
            $this->back();

            return;
        }

        Session::flash('success', 'Password changed successfully!');
        $this->redirect('/');
    }
}