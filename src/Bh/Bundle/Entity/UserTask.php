<?php

namespace Bh\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * UserTask
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class UserTask implements JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ts", type="datetime")
     */
    private $ts;

    /** @ORM\ManyToOne(targetEntity="User", inversedBy="applied") */
    private $user;

    /** @ORM\ManyToOne(targetEntity="Task", inversedBy="applied") */
    private $task;

    public function jsonSerialize()
    {
        return [
            'user' => $this->getUser(),
        ];
    }

    /*
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ts
     *
     * @param \DateTime $ts
     * @return UserTask
     */
    public function setTs($ts)
    {
        $this->ts = $ts;

        return $this;
    }

    /**
     * Get ts
     *
     * @return \DateTime 
     */
    public function getTs()
    {
        return $this->ts;
    }

    /**
     * Set user
     *
     * @param \Bh\Bundle\Entity\User $user
     * @return UserTask
     */
    public function setUser(\Bh\Bundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Bh\Bundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set task
     *
     * @param \Bh\Bundle\Entity\Task $task
     * @return UserTask
     */
    public function setTask(\Bh\Bundle\Entity\Task $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \Bh\Bundle\Entity\Task 
     */
    public function getTask()
    {
        return $this->task;
    }
}
