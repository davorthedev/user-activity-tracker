<?php

declare(strict_types=1);

namespace App\Tests\Functional;


final class AuthenticationTest extends HttpAppTestCase
{
    /**
     * @var string
     */
    private const PASSWORD = 'ponchek2024';

    public function testRegisterAndLoginAndPage(): void
    {
        $registered = $this->post('/register', [
            'email' => 'user@example.com',
            'password' => self::PASSWORD,
            'password_confirm' => self::PASSWORD,
        ]);
        self::assertSame(303, $registered->getStatus());
        self::assertSame('/login', $registered->getHeader('location'));
        self::assertSame(1, (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn());
        $loggedIn = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => self::PASSWORD,
        ]);
        self::assertSame(303, $loggedIn->getStatus());
        self::assertNotNull($this->session->userId());
        self::assertSame(200, $this->get('/page-a')->getStatus());
    }

    public function testRedirectionToLogin(): void
    {
        $response = $this->get('/page-a');
        self::assertSame(302, $response->getStatus());
        self::assertSame('/login?next=%2Fpage-a', $response->getHeader('location'));
    }

    public function testPostWithoutCsrf(): void
    {
        self::assertSame(403, $this->post('/login', ['_csrf' => 'wrong'])->getStatus());
    }

    public function testUserToAdminPage(): void
    {
        $this->registerAndLogIn('user@example.com');
        self::assertSame(403, $this->get('/admin/stats')->getStatus());
    }

    public function testDuplicateEmail(): void
    {
        $this->post('/register', [
            'email' => 'george.foreman@example.com',
            'password' => self::PASSWORD,
            'password_confirm' => self::PASSWORD,
        ]);
        $duplicate = $this->post('/register', [
            'email' => 'george.foreman@example.com',
            'password' => self::PASSWORD,
            'password_confirm' => self::PASSWORD,
        ]);
        self::assertSame(422, $duplicate->getStatus());
        self::assertSame(1, (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn());
    }

    private function registerAndLogIn(string $email): void
    {
        $this->post('/register', [
            'email' => $email,
            'password' => self::PASSWORD,
            'password_confirm' => self::PASSWORD,
        ]);
        $this->post('/login', ['email' => $email, 'password' => self::PASSWORD]);
    }
}
