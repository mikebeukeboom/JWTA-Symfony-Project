<?php
/**
 * Created by PhpStorm.
 * User: mbeukeboom
 * Date: 10/08/2017
 * Time: 10:51
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ItemRepository")
 * @ORM\Table(name="items")
 */
class Item
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAvailable = true;

    /**
     * @var ItemLog[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ItemLog", mappedBy="item")
     */
    private $logs;

    public function __construct()
    {
        $this->logs= new ArrayCollection();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getIsAvailable()
    {
        return $this->isAvailable;
    }

    public function setIsAvailable($isAvailable)
    {
        $this->isAvailable = $isAvailable;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setLogs($logs)
    {
        $this->logs = $logs;
    }

    public function getLogs()
    {
        return $this->logs;
    }
    

}