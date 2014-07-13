<?php

namespace Bh\Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BhBundle:Default:index.html.twig');
    }

    public function donateSuccessAction(Request $req)
    {
        $points = $req->query->get('points');
        return $this->render('BhBundle:Default:donate-success.html.twig', [
            'points' => $points,
        ]);
    }
    public function donateErrorAction(Request $req)
    {
        $error = $req->query->get('error');
        return $this->render('BhBundle:Default:donate-error.html.twig', [
            'error' => $error,
        ]);
    }

    public function donateAction(Request $req)
    {
        $em = $this->getDoctrine()->getManager();
        if ('POST' == $req->getMethod()) {
            $donate = $this->get('bh.donate');
            $user = $em->getRepository('BhBundle:User')->findOneBy(['email' => $req->request->get('email')]);
            if (!$user)
                throw $this->createNotFoundException('No such user');
            $pay = $donate->pay($req->request->get('payment_method_nonce'), $user, $req->request->get('amount'));
            if (is_int($pay)) {
                $em->flush();
                return $this->redirect($this->get('router')->generate('bh_donate_success', ['points' => $pay]));
            }
            return $this->redirect($this->get('router')->generate('bh_donate_error', ['error' => $pay]));
        }
        if ($req->query->has('email_hash')) {
            $user = $em->getRepository('BhBundle:User')->findOneBy(['emailHash' => $req->query->get('email_hash')]);
        } else
            $user = null;
        return $this->render('BhBundle:Default:donate.html.twig', [
            'token' => $this->get('bh.donate')->getToken(),
            'user' => $user,
        ]);
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
