<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Halaman login, daftar, dan logout.
 */
class Auth extends BaseController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /** Halaman awal: arahkan ke login atau dashboard. */
    public function check()
    {
        return redirect()->to(site_url(session()->get('logged_in') ? 'dashboard' : 'login'));
    }

    /** Form login. */
    public function showLogin()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(site_url('dashboard'));
        }

        return view('auth/login', ['title' => 'Masuk']);
    }

    /** Proses login. */
    public function login()
    {
        if (! $this->validate([
            'username' => 'required',
            'password' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Username dan password wajib diisi.');
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

        if ($user === null || ! password_verify($password, $user->password)) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        $this->setSession($user);

        return redirect()->to(site_url('dashboard'));
    }

    /** Form daftar akun baru. */
    public function showRegister()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(site_url('dashboard'));
        }

        return view('auth/register', ['title' => 'Daftar Akun']);
    }

    /** Proses pendaftaran. */
    public function register()
    {
        // Aturan validasi pendaftaran
        if (! $this->validate([
            'username' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[users.username]',
            'nama'     => 'required|max_length[100]',
            'email'    => 'valid_email|permit_empty',
            'no_hp'    => 'permit_empty|max_length[20]',
            'password' => 'required|min_length[6]',
            'confirm'  => 'required|matches[password]',
        ], [
            'username' => [
                'is_unique' => 'Username sudah dipakai, pilih yang lain.',
            ],
            'confirm' => [
                'matches' => 'Konfirmasi password tidak sama.',
            ],
        ])) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali data yang diisi.');
        }

        $userId = $this->userModel->baru([
            'username' => trim((string) $this->request->getPost('username')),
            'password' => (string) $this->request->getPost('password'),
            'nama'     => trim((string) $this->request->getPost('nama')),
            'email'    => trim((string) $this->request->getPost('email')) ?: null,
            'no_hp'    => trim((string) $this->request->getPost('no_hp')) ?: null,
        ]);

        $user = $this->userModel->find($userId);
        $this->setSession($user);

        session()->setFlashdata('success', 'Akun berhasil dibuat. Selamat datang, ' . $user->nama . '!');

        return redirect()->to(site_url('dashboard'));
    }

    /** Keluar dari aplikasi. */
    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'));
    }

    private function setSession(object $user): void
    {
        session()->set([
            'logged_in' => true,
            'user_id'   => (int) $user->id,
            'username'  => $user->username,
            'nama'      => $user->nama,
            'email'     => $user->email,
            'role'      => $user->role,
        ]);
    }
}
