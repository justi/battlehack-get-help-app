<?php

namespace Bh\Bundle\Services;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;

class RequestListener
{
    public function onException(GetResponseForExceptionEvent $event)
    {
        if ($event->getRequest()->getMethod() == 'OPTIONS') {
            $resp = new Response();
            $resp->headers->set('Access-Control-Allow-Origin', '*');
            $resp->headers->set('Access-Control-Allow-Headers', 'Content-Type');
            $resp->headers->set('Access-Control-Allow-Method', 'GET, POST, DELETE');
            $resp->setStatusCode(Response::HTTP_OK);
            $resp->headers->set('X-Status-Code', 200);
            $event->setResponse($resp);
            return $event;
        }
    }
};

