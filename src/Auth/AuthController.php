<?php

declare(strict_types=1);

namespace App\Auth;

use App\Http\RedirectResponse;
use App\Http\Request;
use App\Http\Response;
use App\Tracking\ActionMan;
use App\Tracking\UserActivityTracker;
use App\View\PageRenderer;

final class AuthController
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly RegistrationValidator $validator,
        private readonly PageRenderer $pageRenderer,
        private readonly UserActivityTracker $tracker
    ) {
    }

    public function showLogin(Request $request): Response
    {
        //if ($request->getSession()->userId() !== null) {
        if ($request->getUser() !== null) {
            return new RedirectResponse('/page-a', 302);
        }

        return $this->pageRenderer->page(
            $request,
            'auth/login',
            [
                'email' => '',
                'errors' => [],
                'next' => $this->getSafeNext($request->getQueryParam('next')),
            ],
            'Log in'
        );
    }

    public function login(Request $request): Response
    {
        $email = trim($request->getPostValue('email', '') ?? '');
        $password = $request->getPostValue('password', '') ?? '';
        $next = $this->getSafeNext($request->getPostValue('next'));
        $user = $this->auth->attemptLogin($email, $password);
        if (!$user instanceof User) {
            return $this->pageRenderer->page(
                $request,
                'auth/login',
                [
                    'email' => $email,
                    'errors' => ['Invalid credentials'],
                    'next' => $next,
                ], 
                'Log in',
                401
            );
        }
        $request->getSession()->logIn($user->id);
        $this->tracker->login(ActionMan::fromRequest($request));        

        return new RedirectResponse('/' === $next ? '/page-a' : $next);
    }

    public function showRegister(Request $request): Response
    {
        if ($request->getSession()->userId() !== null) {
            return new RedirectResponse('/page-a', 302);
        }

        return $this->pageRenderer->page(
            $request,
            'auth/register',
            [
                'email' => '',
                'errors' => [],
            ],
            'Register'
        );
    }

    public function register(Request $request): Response
    {
        $email = trim($request->getPostValue('email', '') ?? '');
        $password = $request->getPostValue('password', '') ?? '';
        $confirm = $request->getPostValue('password_confirm', '') ?? '';
        $errors = $this->validator->validate($email, $password, $confirm);
        if ([] === $errors) {
            try {
                $userId = $this->auth->register($email, $password);
                $this->tracker->registration(new ActionMan($userId, $request->getClientIp()));
                $request->getSession()->addFlashMessage('Account created. You can log in now.');

                return new RedirectResponse('/login');
            } catch (EmailAlreadyTakenException) {
                $errors[] = 'Email address is taken.';
            }
        }

        return $this->pageRenderer->page(
            $request,
            'auth/register',
            [
                'email' => $email,
                'errors' => $errors,
            ],
            'Register',
            422
        );
    }

    public function logout(Request $request): Response
    {
        $this->tracker->logout(ActionMan::fromRequest($request));
        $request->getSession()->destroy();

        return new RedirectResponse('/login');
    }

    private function getSafeNext(?string $next): string
    {
        if (null === $next || '' === $next || !str_starts_with($next, '/')) {
            return '/';
        }
        if (str_starts_with($next, '//') || str_starts_with($next, '/\\')) {
            return '/';
        }

        return $next;
    }
}
