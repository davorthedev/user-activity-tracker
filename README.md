User Acitivity Tracker

Simple app that records what users do: login, register, view pages (page-a, page-b), click buttons (Buy a cow, Download)
 and shows that back to an admin as a searchable log and a daily report.

PHP 8.1, MySql 8.4
Composer used just for autoloading and dev tools.

How to run app?

Docker. Uses ports 8080 and 3307 (change in docker-compose.yml if needed.)
1. Run:
<pre>
docker compose up -d --build
make install
</pre>
2. Open https://localhost:8080 and log in:

admin-username: admin@mejl.com
admin-password: ponchek2024

user-username: user@mejl.com
user-password: ponchek2024

Password commited deliberately for the sake of simplicity and testing. In a real deployment they should come from the
environment.

Database starts with no data but there is a seeder for million event, 90 days, 50 users.
Run: make seed

-----------------------------------------------------------------------------------------------------------------------
Notes:
"Buy a cow" comes back on reload.
Page view is every render - refresh also.
Dates are UTC for consistency.

All web requests go through public/index.php. Public is document root and everything else is unreachable via URL.
Web request is converted to object of class Request and goes trough middleware for security, sessions, routing, CSRF,
authentication and tracking. After, controller handles the request. Using middleware common checks are in the same place
instead in every controller.

Routing is in middleware chain.

Page tracking is configured on the route ('track' => PerformedOn::PageA):
<pre>
$routes->get('/page-a', [PageController::class, 'pageA'])
        ->withAdditionalAttributes(['role' => Role::User, 'track' => PerformedOn::PageA]);
</pre>

Tracking middleware records a page view only for succesful GET request with status 200.

Button clicks use POST.

Download endpoint use POST so the click is tracked first. PHP send X-Accel-Redirect header and nginx serves file.

All events are stored in events table. The action and performed_on are MySql enums. CHECK constraint prevents
invalid combinations. In PHP these values are PHP enums.

Statistics page uses cursor not offset so there are no page numbers, just older and newer.

DB queries use prepared statements.
Template output is escaped before rendering.
All POST requests use CSRF.
Passwords handled with password_hash and password_verify.
Session id regenerated after login.
Dummy password check for unknown emails so the response time is realistic.

Some functional tests are implemented.
Running tests:
make test


Planned, not done: 
integration and unit tests, PHP Stan, CI
