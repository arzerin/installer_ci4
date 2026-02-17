<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if (! is_file(WRITEPATH . 'installer/installed.lock')) {
            return redirect()->to('/install');
        }

        return view('welcome_message');
    }
}
