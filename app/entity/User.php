<?php
/**
 * User Entity
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
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"}), @ORM\UniqueConstraint(name="username", columns={"username"})})
 * @ORM\Entity(repositoryClass="Repository\User")
 */
class User extends \Superclass\Base implements \Nette\Security\IIdentity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    protected $id;

    /**
     * @manyToOne(targetEntity="Group")
     * @joinColumn(name="group_id", referencedColumnName="id")
     */
    protected $group;
    
    /**
     * @OneToMany(targetEntity="Element", cascade={"persist"}, mappedBy="user")
     * @JoinColumn(name="element", referencedColumnName="user")
     */
    protected $element;
    
    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;
    
    /**
     * @var string
     * @ORM\Column(name="surname", type="string", length=255, nullable=false)
     */
    protected $surname;

    /**
     * @var string
     * @ORM\Column(name="username", type="string", length=255, nullable=false, unique=true)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    protected $phone;

    /**
     * @var string
     * @ORM\Column(name="street", type="string", length=255, nullable=true)
     */
    protected $street;

    /**
     * @var string
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @var string
     * @ORM\Column(name="zip", type="string", length=255, nullable=true)
     */
    protected $zip;

    /**
     * @var boolean
     * @ORM\Column(name="blocked", type="boolean", nullable=false)
     */
    protected $blocked = FALSE;
    
    /**
     * @var string
     * @ORM\Column(name="blocked_reason", type="text", nullable=true)
     */
    protected $blocked_reason;

    public function __construct()
    {
        $this->created = new \DateTime();
    }
    /* implementation of IIdentity */

    public function getId()
    {
        return $this->id;
    }

    /**
     * Povinná funkce kvůli interfacu
     * @return array
     */
    public function getRoles()
    {
        return array();
    }

    /**
     * Zahashuje heslo pomocí BCRYPT
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT,
            array(
            "cost" => 10
        ));
    }

    function setGroup($group)
    {
        $this->group = $group;
    }
}