<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('IndholdskanalenMainBundle:Main:index.html.twig');
    }
}
