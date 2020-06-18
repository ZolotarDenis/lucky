<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Table(name="guest")
 * @ORM\Entity(repositoryClass="App\Repository\GuestRepository")
 */
class Guest
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Status
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Status")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(name="client_ip", type="string")
     */
    protected $clientIp;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @param Status $status
     * @param string $clientIp
     */
    public function __construct(Status $status, string $clientIp)
    {
        $this->status = $status;
        $this->clientIp = $clientIp;
        $this->created = new DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @return string
     */
    public function getClientIp(): string
    {
        return $this->clientIp;
    }
}
