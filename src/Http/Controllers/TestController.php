<?php

namespace SHTayeb\Bookworm\Http\Controllers;

use Illuminate\Routing\Controller;

class TestController extends Controller
{

    public function index(): string
    {
        return 'It works!';
    }

}
