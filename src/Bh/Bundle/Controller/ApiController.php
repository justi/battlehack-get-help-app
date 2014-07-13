<?php

namespace Bh\Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use DateTime;

use Bh\Bundle\Entity\User;
use Bh\Bundle\Entity\Task;
use Bh\Bundle\Entity\UserTask;

class ApiController extends Controller
{
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
            'email_hash' => $user->getEmailHash(),
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

    public function taskListAction(Request $req)
    {
        $user = $this->user($req);
        $em = $this->getDoctrine()->getManager();
        /*
        $qb = $em->createQueryBuilder();
        $qb->select(['t', 'a']);
        $qb->from('BhBundle:Task', 't');
        $qb->andWhere('t.accepted is null');
        $qb->andWhere('t.added != :user');
        $qb->leftJoin('t.applied', 'a', 'with', 'a.user=:user');
        $qb->setParameter('user', $user);
        */
        $rep = $em->getRepository('BhBundle:Task');
        $repUt = $em->getRepository('BhBundle:UserTask');
        $qb = $rep->createQueryBuilder('t');
        $qb->andWhere('t.accepted is null');
        $qb->andWhere('t.added != :user');
        $qb->setParameter('user', $user);
        $tasks = [];
        $accepted = $user->getTaskAccepted()? $user->getTaskAccepted()->getId() : null;
        foreach ($qb->getQuery()->getResult() as $task) {
            $r = $task->jsonSerialize();
            unset($r['redeem']);
            $r['accepted'] = $accepted == $task->getId();
            $r['applied'] = !!$repUt->findOneBy(['user' => $user, 'task' => $task]);
            $tasks[] = $r;
        }
        return $this->json($tasks);
    }

    public function redeemAction()
    {
        $user = $this->user($req);
        $em = $this->getDoctrine()->getManager();
        if (!($task = $user->getTaskAdded()))
            return $this->error('No such task');
        if ($task->getDone())
            return $this->error('Task already done');
        $redeem = $task->getAccepted();
        $redeem->setPoints($redeem->getPoints() + $task->getPoints());
        $redeem->setTaskAccepted(null);
        $task->setDone(new DateTime());
        $em->flush();
        return $this->success();
    }

    public function applyAction(Request $req, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->user($req);
        if ($user->getTaskAdded() or $user->getTaskAccepted())
            return $this->error('Task in progress');
        $task = $em->getRepository('BhBundle:Task')->find($id);
        if (!$task)
            return $this->error('No such task');
        if ($task->getAccepted())
            return $this->error('Task already accepted');
        $ut = new UserTask();
        $ut->setTs(new DateTime());
        $ut->setUser($user);
        $ut->setTask($task);
        $em->persist($ut);
        $em->flush();
        return $this->success();
    }

    public function acceptAction(Request $req)
    {
        $user = $this->user($req);
        if (!$user->getTaskAdded())
            return $this->error('No task added');
        $data = $this->data($req);

        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('BhBundle:UserTask');
        $qb = $rep->createQueryBuilder('ut');
        $qb->innerJoin('ut.user', 'u');
        $qb->andWhere('u.emailHash=:emailHash');
        $qb->andWhere('ut.task = :task');
        $qb->setParameter('emailHash', $data['user_id']);
        $qb->setParameter('task', $user->getTaskAdded());
        $ut = $qb->getQuery()->getSingleResult();

        if (!$ut)
            return $this->error('Invalid application');

        $ut->getUser()->setTaskAccepted($ut->getTask());
        $ut->getTask()->setAccepted($ut->getUser());

        $em->flush();
        
        $fragment = $this->get('twig')->loadTemplate('BhBundle:email:accept.html.twig');
        $this->get('bh.email')->send($ut->getUser()->getEmail(), 'Your help is needed!', $fragment->render([
            'task' => $ut->getTask(),
        ]));

        return $this->success();
    }

    public function appliedAction(Request $req)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->user($req);
        if (!$user->getTaskAdded())
            return $this->error('No task added');
        $rep = $em->getRepository('BhBundle:User');
        $qb = $rep->createQueryBuilder('u');
        $qb->innerJoin('u.applied', 'a');
        $qb->andWhere('a.task=:task');
        $qb->setParameter('task', $user->getTaskAdded());
        return $this->json($qb->getQuery()->getResult());
    }
}

