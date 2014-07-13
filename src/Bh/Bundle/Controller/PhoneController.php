<?php

namespace Bh\Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use DateTime;

use Bh\Bundle\Entity\User;
use Bh\Bundle\Entity\Task;

class PhoneController extends Controller
{
    private function tel($req)
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('BhBundle:User')->findOneBy(['phone' => trim($req->request->get('Caller'), ' +')]);
    }

    public function voiceAction(Request $req)
    {
        $user = $this->tel($req);
        if (!$user)
            return $this->render('BhBundle:Phone:unknown.xml.twig');
        if ($user->getTaskAdded())
            return $this->render('BhBundle:Phone:record.xml.twig');
        if ($user->getTaskAccepted()) {
            return $this->render('BhBundle:Phone:listen.xml.twig', [
                'url' => $user->getTaskAccepted()->getRecording(),
            ]);
        }
        return $this->render('BhBundle:Phone:say.xml.twig');
    }
    public function voiceDoneAction(Request $req)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->tel($req);
        if ($user and $user->getTaskAdded()) {
            $user->getTaskAdded()->setRecording($req->request->get('RecordingUrl'));
            $em->flush();
        }
        return new Response('');
    }
}

