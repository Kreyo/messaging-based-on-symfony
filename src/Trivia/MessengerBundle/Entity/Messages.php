<?php

namespace Trivia\MessengerBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Messages
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Messages
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
     *
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumn(name="fromUser", referencedColumnName="id", onDelete="CASCADE")
     */
    private $fromUser;

    /**
     *
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumn(name="toUser", referencedColumnName="id", onDelete="CASCADE")
     */
    private $toUser;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;
    /**
     * @var boolean
     * @Assert\NotBlank()
     * @ORM\Column(name="is_read", type="boolean")
     */

    private $is_read;

    /**
     * @var datetime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

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
     * Set fromUser
     *
     * @param  string   $fromUser
     * @return Messages
     */

    public function setFromUser($fromUser)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get fromUser
     *
     * @return string
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set toUser
     *
     * @param  string   $toUser
     * @return Messages
     */

    public function setToUser($toUser)
    {
        $this->toUser = $toUser;

        return $this;
    }

    /**
     * Get toUser
     *
     * @return string
     */
    public function getToUser()
    {
        return $this->toUser;
    }

    /**
     * Set text
     *
     * @param  string   $text
     * @return Messages
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    public function setRead()
    {
        $this->is_read = true;

        return $this;
    }

    public function setUnread()
    {
        $this->is_read = false;

        return $this;
    }

    public function isRead()
    {
        return $this->is_read;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }



    /**
     * Set is_read
     *
     * @param boolean $isRead
     * @return Messages
     */
    public function setIsRead($isRead)
    {
        $this->is_read = $isRead;

        return $this;
    }

    /**
     * Get is_read
     *
     * @return boolean 
     */
    public function getIsRead()
    {
        return $this->is_read;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Messages
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
