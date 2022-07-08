<?php

use App\Entity\Sortie;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class PublishedSortieFilter extends SQLFilter {


    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->reflClass->getAttributes($targetEntity, $targetTableAlias)) {
            if ($targetEntity->getReflectionClass()->name != Sortie::class) {
                return 's.nom = Grosse Murge';


            }
        } return '';
    }
}
