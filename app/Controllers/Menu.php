<?php

namespace App\Controllers;

/**
 * Halaman "Menu Lainnya" untuk mobile.
 */
class Menu extends BaseController
{
    public function index()
    {
        return $this->renderPage('menu/index', ['title' => 'Menu']);
    }
}
