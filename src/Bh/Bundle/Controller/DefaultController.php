<?php

namespace Bh\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BhBundle:Default:index.html.twig');
    }

    public function taskAction($token)
    {
        $task = $this->getDoctrine()->getRepository('BhBundle:Task')->findOneBy(['token' => $token]);
        if (!$task)
            throw $this->createNotFoundException('No such task');
        return $this->render('BhBundle:Default:task.html.twig', [
            'task' => $task,
        ]);
    }
}
