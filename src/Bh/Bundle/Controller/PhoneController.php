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
    public function phoneAction(Request $req)
    {

        return $this->success();
    }
}

