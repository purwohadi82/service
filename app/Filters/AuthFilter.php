<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter keamanan halaman: semua halaman wajib login,
 * kecuali halaman login dan daftar (register).
 */
class AuthFilter implements FilterInterface
{
    /**
     * Halaman yang boleh diakses tanpa login.
     *
     * @var array<int, string>
     */
    private array $public = [
        '',
        'login',
        'register',
    ];

    public function before(RequestInterface $request, $arguments = null): ?ResponseInterface
    {
        // Path aplikasi relatif terhadap baseURL (mis. /service/public/login -> login)
        $uriPath  = $request->getUri()->getPath();
        $basePath = rtrim((string) parse_url((string) config('App')->baseURL, PHP_URL_PATH), '/');
        $path     = trim(substr($uriPath, strlen($basePath)), '/');

        $loggedIn = (bool) session()->get('logged_in');

        if ($loggedIn) {
            // User sudah login tidak perlu mengakses halaman auth lagi
            if (in_array($path, ['login', 'register'], true)) {
                return redirect()->to(site_url('dashboard'));
            }

            return null;
        }

        if (in_array($path, $this->public, true)) {
            return null;
        }

        session()->setFlashdata('error', 'Silakan login terlebih dahulu.');

        return redirect()->to(site_url('login'));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ?ResponseInterface
    {
        return null;
    }
}
