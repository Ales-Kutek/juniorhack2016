<?php
/** softdele */

use Doctrine\ORM\Mapping\ClassMetadata;
use Zenify\DoctrineFilters\Contract\FilterInterface;


final class SoftdeleteableFilter implements FilterInterface
{
    private $session;
    
    public function __construct(\Nette\Http\Session $session) {
        $this->session = $session;
    }

    /**
     * Přidá za každý DQL dotaz podmínku, který zajišťuje, že z databáze se nahrají pouze nesmazané záznamy
     */
    public function addFilterConstraint(ClassMetadata $entity, $alias)
    {
        $fields = $entity->getFieldNames();

        //$section = $this->session->getSection("user");
        
        if (in_array("deleted_at", $fields)) {
            return "$alias.deleted_at IS NULL";
        }

        return "";
    }
}