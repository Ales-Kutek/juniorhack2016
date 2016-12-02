<?php
/**
 * HeatSensor Entity
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
 * HeatSensor
 *
 * @ORM\Table(name="`heat_sensor`")
 * @ORM\Entity(repositoryClass="Repository\HeatSensor")
 */
class HeatSensor extends \Superclass\Base
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=255, unique=false)
     * @var string
     */
    protected $name; 
    
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @var string
     */
    protected $code;
        
    /**
     * @manyToOne(targetEntity="Element")
     * @joinColumn(name="element", referencedColumnName="id")
     */
    protected $element;
    
    /**
     * @OneToMany(targetEntity="HeatSensorLog", cascade={"persist"}, mappedBy="heat_sensor")
     * @JoinColumn(name="heat_sensor", referencedColumnName="heat_sensor")
     */
    protected $heat_sensor_log;

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