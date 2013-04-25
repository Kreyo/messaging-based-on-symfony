<?php

namespace Trivia\MessengerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Users")
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="recipient", type="string", length=255)
     */
    private $recipient;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_read", type="boolean")
     */

    private $is_read;

    /**
     * Get id
     * @Assert\NotBlank()
     * @return integer
     */

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Messages
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set recipient
     *
     * @param string $recipient
     * @return Messages
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    
        return $this;
    }

    /**
     * Get recipient
     *
     * @return string 
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set text
     *
     * @param string $text
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

    public function setRead(){
        $this->is_read = true;
        return $this;
    }

    public function setUnread(){
        $this->is_read = false;
        return $this;
    }

    public function isRead(){
        return $this->is_read;
    }
}