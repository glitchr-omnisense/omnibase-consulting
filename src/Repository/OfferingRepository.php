<?php

namespace Base\Consulting\Repository;

use Base\Consulting\Entity\Offering;
use Base\Database\Repository\ServiceEntityRepository;
use Base\Database\Repository\Finders;

/**
 * @method Offering|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offering|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Offering[]    findAll()
 * @method Offering[]    findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 */
class OfferingRepository extends ServiceEntityRepository
{
    public function __construct(Finders $finders)
    {
        parent::__construct($finders, Offering::class);
    }

    /** @return list<Offering> the visible ones, in their order */
    public function findVisible(?int $limit = null): array
    {
        return $this->findBy(['visible' => true], ['position' => 'ASC', 'id' => 'ASC'], $limit);
    }

    /** @return list<Offering> the visible ones a quote may be asked for, in their order (the quote form's choices) */
    public function findQuotable(): array
    {
        return $this->findBy(['visible' => true, 'quotable' => true], ['position' => 'ASC']);
    }

    public function findOneVisible(string $slug): ?Offering
    {
        return $this->findOneBy(['slug' => $slug, 'visible' => true]);
    }
}
