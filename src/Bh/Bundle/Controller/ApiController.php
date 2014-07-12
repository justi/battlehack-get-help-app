<?php

namespace Bh\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use DateTime;

use Bh\Bundle\Entity\User;
use Bh\Bundle\Entity\Task;

class ApiController extends Controller
{
    private function json($data)
    {
        $resp = new Response(json_encode($data));
        $resp->headers->set('Content-Type', 'application/json');
        $resp->headers->set('Access-Control-Allow-Origin', '*');
        return $resp;
    }
    private function success()
    {
        return $this->json();
    }
    private function error($msg)
    {
        $resp = $this->json(['success' => false, 'message' => $msg]);
        $resp->setStatusCode(400);
        return $resp;
    }
    private function user($req)
    {
        $token = substr($req->headers->get('Authorization'), strlen('token '));
        return $em->getRepository('BhBundle:User')->findOneBy(['token' => $token]);
    }
    private function ts($ts)
    {
        if ($ts instanceOf DateTime)
            return $ts->getTimestamp() * 1000;
        $dt = new DateTime();
        $dt->setTimestamp($ts / 1000);
        return $dt;
    }

    public function loginAction(Request $req)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('BhBundle:User')->findOneBy(['email' => $req->request->get('email')]);
        if (!$user) {
            $user = new User();
            $user->setEmail($req->request->get('email'));
            $user->setPhone($req->request->get('phone'));
            $user->setPoints(10);
            $em->persist($user);
        }
        $user->setToken(md5(rand()));
        $em->flush();
        return $this->json([
            'login_token' => $user->getToken(),
        ]);
    }

    public function pointsAction(Request $req)
    {
        $user = $this->user($req);
        return $this->json([
            'points' => $user->getPoints(),
        ]);
    }

    public function taskAddAction()
    {
        $user = $this->user($req);
        if ($user->getTaskAdded() or $user->getTaskAccepted())
            return $this->error('Task in progress');
        $points = (int) $req->request->get('points');
        if (!$points)
            return $this->error('Invalid points value');
        if ($points > $user->getPoints())
            return $this->error('Not enough points');
        $em = $this->getDoctrine()->getManager();

        $task = new Task();
        $task->setAdded($user);
        $task->setType($req->request->get('type'));
        $task->setTitle($req->request->get('title'));
        $task->setDetails($req->request->get('details'));
        $task->setPoints($req->request->get('points'));
        $task->setLat($req->request->get('lat'));
        $task->setLng($req->request->get('lng'));
        $task->setDeadline($this->ts($req->request->get('deadline')));
        $em->persist($task);
        $user->setAdded($task);
        $user->setPoints($user->getPoints() - $points);
        $em->flush();
        return $this->json();
    }

    public function taskListAction()
    {
        $em = $this->getDoctrine()->getManager();
        return $this->json($em->getRepository('BhBundle:Task')->findBy(['accepted' => null]));
    }

    public function redeemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('BhBundle:Task')->findOneBy(['token' => $req->request->get('redeem')]);
        if (!$task)
            return $this->error('No such task');
        if ($task->getDone())
            return $this->error('Task already done');
        $user->setPoints($user->getPoints() + $task->getPoints());
        $user->setAccepted(null);
        $task->setDone(new DateTime());
        $em->flush();
        return $this->json(['points' => $task->getPoints()]);
    }

    public function applyAction($id)
    {
        $user = $this->user($req);
        if ($user->getTaskAdded() or $user->getTaskAccepted())
            return $this->error('Task in progress');
        $task = $em->getRepository('BhBundle:Task')->find($id);
        if (!$task)
            return $this->error('No such task');
        if ($task->getAccepted())
            return $this->error('Task already accepted');
        return $this->success();
    }

    public function acceptAction()
    {
        $user = $this->user($req);
        if (!$user->getTaskAdded())
            return $this->error('No task added');
        return $this->json();
    }
}

