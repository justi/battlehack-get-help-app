<?php

namespace Bh\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Task
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Task implements JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(type="text") */
    private $title;

    /** @ORM\Column(type="string", length=63) */
    private $type;

    /** @ORM\Column(type="text") */
    private $details;

    /** @ORM\Column(type="integer") */
    private $points;

    /** @ORM\Column(type="float") */
    private $lat;

    /** @ORM\Column(type="float") */
    private $lng;

    /** @ORM\Column(type="datetime") */
    private $ts;

    /** @ORM\Column(type="datetime") */
    private $deadline;

    /** @ORM\Column(type="string", length=255) */
    private $token;

    /** @ORM\ManyToOne(targetEntity="User", inversedBy="added") */
    private $added;

    /** @ORM\ManyToOne(targetEntity="User", inversedBy="accepted") */
    private $accepted;

    /** @ORM\OneToMany(targetEntity="UserTask", mappedBy="task") */
    private $applied;

    /** @ORM\Column(type="datetime", nullable=true) */
    private $done;

    /** @ORM\Column(type="json_array", nullable=true) */
    private $recording;

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'title' => $this->getTitle(),
            'details' => $this->getDetails(),
            'deadline' => 1000 * $this->getDeadline()->getTimestamp(),
            'lat' => $this->getLat(),
            'lng' => $this->getLng(),
            'ts' => 1000 * $this->getTs()->getTimestamp(),
            'redeem' => $this->getToken(),
            'accepted' => $this->getAccepted(),
            'points' => $this->getPoints(),
            'added' => [
                'email_hash' => $this->getAdded()->getEmailHash(),
            ],
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
     * Set title
     *
     * @param string $title
     * @return Task
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set details
     *
     * @param string $details
     * @return Task
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string 
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return Task
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
     * Set lat
     *
     * @param float $lat
     * @return Task
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param float $lng
     * @return Task
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return float 
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set ts
     *
     * @param \DateTime $ts
     * @return Task
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
     * Set deadline
     *
     * @param \DateTime $deadline
     * @return Task
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime 
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Task
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
     * Set done
     *
     * @param \DateTime $done
     * @return Task
     */
    public function setDone($done)
    {
        $this->done = $done;

        return $this;
    }

    /**
     * Get done
     *
     * @return \DateTime 
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * Set added
     *
     * @param \Bh\Bundle\Entity\User $added
     * @return Task
     */
    public function setAdded(\Bh\Bundle\Entity\User $added = null)
    {
        $this->added = $added;

        return $this;
    }

    /**
     * Get added
     *
     * @return \Bh\Bundle\Entity\User 
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set accepted
     *
     * @param \Bh\Bundle\Entity\User $accepted
     * @return Task
     */
    public function setAccepted(\Bh\Bundle\Entity\User $accepted = null)
    {
        $this->accepted = $accepted;

        return $this;
    }

    /**
     * Get accepted
     *
     * @return \Bh\Bundle\Entity\User 
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Task
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->applied = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add applied
     *
     * @param \Bh\Bundle\Entity\UserTask $applied
     * @return Task
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

    /**
     * Set recording
     *
     * @param array $recording
     * @return Task
     */
    public function setRecording($recording)
    {
        $this->recording = $recording;

        return $this;
    }

    /**
     * Get recording
     *
     * @return array 
     */
    public function getRecording()
    {
        return $this->recording;
    }
}
