<?php
/**
 * Group Entity
 */

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Element
 *
 * @ORM\Table(name="element")
 * @ORM\Entity(repositoryClass="Repository\Element")
 */
class Element extends \Superclass\Base
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    protected $id;

    /**
     * @manyToOne(targetEntity="User")
     * @joinColumn(name="user", referencedColumnName="id")
     */
    protected $user;

    /**
     * @OneToMany(targetEntity="HeatSensor", cascade={"persist"}, mappedBy="element")
     * @JoinColumn(name="heat_sensor", referencedColumnName="element")
     */
    protected $heat_sensor;

    /**
     * @OneToMany(targetEntity="HumiditySensor", cascade={"persist"}, mappedBy="element")
     * @JoinColumn(name="humidty_sensor", referencedColumnName="element")
     */
    protected $humidity_sensor;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="color", type="string", length=255, nullable=false)
     */
    protected $color;

    public function getId()
    {
        return $this->id;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}