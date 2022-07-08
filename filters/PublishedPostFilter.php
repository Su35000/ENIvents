<?php

namespace App\Filters;

use App\Entity\Sortie;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use App\Entity\Post;

class PublishedPostFilter extends SQLFilter {

    /**
     * Gets the SQL query part to add to a query.
     *
     * @param ClassMetaData $targetEntity
     * @param string $targetTableAlias
     *
     * @return string The constraint SQL if there is available, empty string otherwise.
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias) {
        if ($targetEntity->getReflectionClass()->name != Sortie::class) {
            return sprintf('%s.etat = ouverte', $targetTableAlias);
        }
        return '';
    }
}