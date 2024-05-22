<?php

namespace MedMessage\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class MessageComment
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_message_comment", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_employe", type="integer")
     */
    private $employeId;

    /**
     * @var int
     *
     * @ORM\Column(name="id_customer", type="integer")
     */
    private $customerId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=64)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=64)
     */
    private $file;

     /**
     * @var int
     *
     * @ORM\Column(name="id_thread", type="integer")
     */
    private $threadId;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="id_state_comment", type="integer")
     */
    private $stateCommentId;

    /**
     * @var int
     *
     * @ORM\Column(name="id_shop", type="integer")
     */
    private $shopId;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getEmployeId()
    {
        return $this->employeId;
    }

    /**
     * @param int $employeId
     *
     * @return MessageComment
     */
    public function setEmployeId($employeId)
    {
        $this->employeId = $employeId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     *
     * @return MessageComment
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return MessageComment
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return MessageComment
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return int
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * @param int $threadId
     *
     * @return MessageComment
     */
    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     *
     * @return MessageComment
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTimeInterface $date
     *
     * @return MessageComment
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getStateCommentId()
    {
        return $this->stateCommentId;
    }

    /**
     * @param int $stateCommentId
     *
     * @return MessageComment
     */
    public function setStateCommentId($stateCommentId)
    {
        $this->stateCommentId = $stateCommentId;

        return $this;
    }

    /**
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
    }
    /**
     * @param int $shopId
     *
     * @return MessageComment
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;

        return $this;
    }
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id_employe' => $this->getEmployeId(),
            'id_message_comment' => $this->getId(),
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'id_customer' => $this->getCustomerId(),
            'id_thread' => $this->getThreadId(),
            'file' => $this->getFile(),
            'id_state_comment' => $this->getStateCommentId(),
            'id_shop' => $this->getShopId(),
        ];
    }
}
