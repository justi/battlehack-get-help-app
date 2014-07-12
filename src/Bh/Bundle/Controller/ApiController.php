<?php

namespace Bh\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        $resp->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Token');
        $resp->headers->set('Access-Control-Allow-Method', 'GET, POST, DELETE');
        return $resp;
    }
    private function data($req)
    {
        return json_decode($req->getContent(), true);
    }
    private function success()
    {
        return $this->json(true);
    }
    private function error($msg)
    {
        $resp = $this->json(['success' => false, 'message' => $msg]);
        $resp->setStatusCode(400);
        return $resp;
    }
    private function user($req)
    {
        $em = $this->getDoctrine()->getManager();
        $token = $req->headers->get('X-Token');
        $user = $em->getRepository('BhBundle:User')->findOneBy(['token' => $token]);
        if (!$user)
            throw new AccessDeniedException();
        return $user;
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
        $data = $this->data($req);
        $user = $em->getRepository('BhBundle:User')->findOneBy(['email' => $data['email']]);
        if (!$user) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPhone($data['phone']);
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

    public function taskAddAction(Request $req)
    {
        $user = $this->user($req);
        if ($user->getTaskAdded() or $user->getTaskAccepted())
            return $this->error('Task in progress');
        $data = $this->data($req);
        $points = (int) $data['points'];
        if (!$points)
            return $this->error('Invalid points value');
        if ($points > $user->getPoints())
            return $this->error('Not enough points');
        $em = $this->getDoctrine()->getManager();

        $task = new Task();
        $task->setAdded($user);
        $task->setType($data['type']);
        $task->setTitle($data['title']);
        $task->setDetails($data['details']);
        $task->setPoints($data['points']);
        $task->setLat($data['lat']);
        $task->setLng($data['lng']);
        $task->setDeadline($this->ts($data['deadline']));
        $task->setTs(new DateTime());
        $task->setToken(md5(rand()));
        $em->persist($task);
        $user->setTaskAdded($task);
        $user->setPoints($user->getPoints() - $points);
        $em->flush();
        return $this->json([
            'redeem' => $task->getToken(),
        ]);
    }

    public function taskAction(Request $req)
    {
        $user = $this->user($req);
        return $this->json($user->getTaskAdded());
    }

    public function taskDeleteAction(Request $req)
    {
        $user = $this->user($req);
        $em = $this->getDoctrine()->getManager();
        if ($task = $user->getTaskAdded()) {
            $user->setPoints($user->getPoints() + $task->getPoints());
            $user->setTaskAdded(null);
        } else if ($task = $user->getTaskAccepted()) {
            $user->setTaskAccepted(null);
        }
        $em->flush();

        return $this->success();
    }

    public function taskListAction()
    {
        $em = $this->getDoctrine()->getManager();
        return $this->json($em->getRepository('BhBundle:Task')->findBy(['accepted' => null]));
    }

    public function redeemAction()
    {
        $data = $this->data($req);
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('BhBundle:Task')->findOneBy(['token' => $data['redeem']]);
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

