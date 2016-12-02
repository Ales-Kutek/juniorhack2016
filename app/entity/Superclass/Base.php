<?php
     /**
      * Base file
      */
     namespace Superclass;
     
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
      * @ORM\MappedSuperclass()
      */
    class Base extends \Kdyby\Doctrine\Entities\BaseEntity {
    /**
     * @var \DateTime $updated
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    protected $created;
    
    public function __construct() {
        $this->created = new \DateTime();
    }
    }