<?php

namespace App\Controllers;

use App\Config\Paths;
use Framework\TemplateEngine;

class AboutController
{


    public function __construct(private TemplateEngine $view) {}

    public function index(): void
    {
        echo $this->view->render('about.php', [
            'title' => 'About Page',
            'danger' => "<script>alert(123);</script>"
        ]);
    }
}
