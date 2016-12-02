<?php
/**
 * HumiditySensorLog Entity
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
 * HumiditySensorLog
 *
 * @ORM\Table(name="`humidity_sensor_log`")
 * @ORM\Entity(repositoryClass="Repository\HumiditySensorLog")
 */
class HumiditySensorLog extends \Superclass\Base
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    protected $id;
    
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var integer
     */
    protected $value;
    
    /**
     * @manyToOne(targetEntity="HumiditySensor")
     * @joinColumn(name="humidity_sensor", referencedColumnName="id")
     */
    protected $humidity_sensor;

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