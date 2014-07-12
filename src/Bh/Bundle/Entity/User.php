<?php

namespace Bh\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * User
 *
 * @ORM\Table(name="bh_user")
 * @ORM\Entity
 */
class User implements JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(type="string", length=255) */
    private $email;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $phone;

    /** @ORM\Column(type="string", length=255) */
    private $token;

     /** @ORM\Column(type="integer") */
    private $points;

    /** @ORM\OneToOne(targetEntity="Task") */
    private $taskAdded;
    /** @ORM\OneToOne(targetEntity="Task") */
    private $taskAccepted;

    /** @ORM\OneToMany(targetEntity="Task", mappedBy="added") */
    private $added;

    /** @ORM\OneToMany(targetEntity="Task", mappedBy="accepted") */
    private $accepted;

    /** @ORM\OneToMany(targetEntity="UserTask", mappedBy="user") */
    private $applied;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $emailHash;

    public function jsonSerialize()
    {
        return [
            'email_hash' => $this->getEmailHash(),
        ];
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        $this->emailHash = md5(strtolower(trim($email)));

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return User
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->added = new \Doctrine\Common\Collections\ArrayCollection();
        $this->accepted = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set taskAdded
     *
     * @param \Bh\Bundle\Entity\Task $taskAdded
     * @return User
     */
    public function setTaskAdded(\Bh\Bundle\Entity\Task $taskAdded = null)
    {
        $this->taskAdded = $taskAdded;

        return $this;
    }

    /**
     * Get taskAdded
     *
     * @return \Bh\Bundle\Entity\Task 
     */
    public function getTaskAdded()
    {
        return $this->taskAdded;
    }

    /**
     * Set taskAccepted
     *
     * @param \Bh\Bundle\Entity\Task $taskAccepted
     * @return User
     */
    public function setTaskAccepted(\Bh\Bundle\Entity\Task $taskAccepted = null)
    {
        $this->taskAccepted = $taskAccepted;

        return $this;
    }

    /**
     * Get taskAccepted
     *
     * @return \Bh\Bundle\Entity\Task 
     */
    public function getTaskAccepted()
    {
        return $this->taskAccepted;
    }

    /**
     * Add added
     *
     * @param \Bh\Bundle\Entity\Task $added
     * @return User
     */
    public function addAdded(\Bh\Bundle\Entity\Task $added)
    {
        $this->added[] = $added;

        return $this;
    }

    /**
     * Remove added
     *
     * @param \Bh\Bundle\Entity\Task $added
     */
    public function removeAdded(\Bh\Bundle\Entity\Task $added)
    {
        $this->added->removeElement($added);
    }

    /**
     * Get added
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Add accepted
     *
     * @param \Bh\Bundle\Entity\Task $accepted
     * @return User
     */
    public function addAccepted(\Bh\Bundle\Entity\Task $accepted)
    {
        $this->accepted[] = $accepted;

        return $this;
    }

    /**
     * Remove accepted
     *
     * @param \Bh\Bundle\Entity\Task $accepted
     */
    public function removeAccepted(\Bh\Bundle\Entity\Task $accepted)
    {
        $this->accepted->removeElement($accepted);
    }

    /**
     * Get accepted
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * Set emailHash
     *
     * @param string $emailHash
     * @return User
     */
    public function setEmailHash($emailHash)
    {
        $this->emailHash = $emailHash;

        return $this;
    }

    /**
     * Get emailHash
     *
     * @return string 
     */
    public function getEmailHash()
    {
        return $this->emailHash;
    }

    /**
     * Add applied
     *
     * @param \Bh\Bundle\Entity\UserTask $applied
     * @return User
     */
    public function addApplied(\Bh\Bundle\Entity\UserTask $applied)
    {
        $this->applied[] = $applied;

        return $this;
    }

    /**
     * Remove applied
     *
     * @param \Bh\Bundle\Entity\UserTask $applied
     */
    public function removeApplied(\Bh\Bundle\Entity\UserTask $applied)
    {
        $this->applied->removeElement($applied);
    }

    /**
     * Get applied
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getApplied()
    {
        return $this->applied;
    }
}
