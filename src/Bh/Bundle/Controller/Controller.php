<?php

namespace Bh\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use DateTime;

use Bh\Bundle\Entity\User;
use Bh\Bundle\Entity\Task;

class Controller extends BaseController
{
    protected function json($data)
    {
        $resp = new Response(json_encode($data));
        $resp->headers->set('Content-Type', 'application/json');
        $resp->headers->set('Access-Control-Allow-Origin', '*');
        $resp->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Token');
        $resp->headers->set('Access-Control-Allow-Method', 'GET, POST, DELETE');
        return $resp;
    }
    protected function data($req)
    {
        return json_decode($req->getContent(), true);
    }
    protected function success()
    {
        return $this->json(true);
    }
    protected function error($msg)
    {
        $resp = $this->json(['success' => false, 'message' => $msg]);
        $resp->setStatusCode(400);
        return $resp;
    }
    protected function user($req)
    {
        $em = $this->getDoctrine()->getManager();
        $token = $req->headers->get('X-Token');
        $user = $em->getRepository('BhBundle:User')->findOneBy(['token' => $token]);
        if (!$user)
            throw new AccessDeniedException();
        return $user;
    }
    protected function ts($ts)
    {
        if ($ts instanceOf DateTime)
            return $ts->getTimestamp() * 1000;
        $dt = new DateTime();
        $dt->setTimestamp($ts / 1000);
        return $dt;
    }
}

