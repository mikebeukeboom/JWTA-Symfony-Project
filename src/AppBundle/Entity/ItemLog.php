<?php
/**
 * Created by PhpStorm.
 * User: mbeukeboom
 * Date: 15/08/2017
 * Time: 12:08
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="ItemLogRepository")
 * @ORM\Table(name="ItemLog")
 */
class ItemLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Item
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="logs")
     */
    private $item;

    /**
     * @ORM\Column(type="string", name="user")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", name="date_received")
     */
    private $dateReceived;

    /**
     * @ORM\Column(type="datetime", name="date_returned", nullable=true)
     */
    private $dateReturned;

    /**
     * @ORM\Column(type="string", name="peripherals")
     */
    private $peripherals;

    /**
     * ItemLog constructor.
     */
    public function __construct()
    {
        $this->dateReceived = new DateTime('now');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function setItem($item)
    {
        $this->item = $item;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getDateReceived()
    {
        return $this->dateReceived;
    }

    public function setDateReceived($dateReceived)
    {
        $this->dateReceived = $dateReceived;
    }

    public function getDateReturned()
    {
        return $this->dateReturned;
    }

    public function setDateReturned($dateReturned)
    {
        $this->dateReturned = $dateReturned;
    }

    public function getPeripherals()
    {
        return $this->peripherals;
    }

    public function setPeripherals($peripherals)
    {
        $this->peripherals = $peripherals;
    }
    public function returnItem(){
        $this->dateReturned = new DateTime('now');
        $this->item->setIsAvailable(true);
    }
}