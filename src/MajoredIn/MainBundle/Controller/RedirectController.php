<?php

namespace MajoredIn\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RedirectController extends Controller
{
    public function indexAction($path)
    {
        $response = $this->redirect("http://www.majoredin.com/{$path}", 301);
        return $response;
    }
}
