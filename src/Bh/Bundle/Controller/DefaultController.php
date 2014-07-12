<?php

namespace Bh\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BhBundle:Default:index.html.twig');
    }
}
