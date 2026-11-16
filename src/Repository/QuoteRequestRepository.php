<?php

namespace Base\Consulting\Repository;

use Base\Consulting\Entity\QuoteRequest;
use Base\Consulting\Enum\QuoteStatus;
use Base\Database\Repository\Finder\Criterion;
use Base\Database\Repository\Finder\Group;
use Base\Database\Repository\Finder\Parser;
use Base\Database\Repository\ServiceEntityRepository;
use Base\Database\Repository\Finders;

/**
 * @method QuoteRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuoteRequest|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method QuoteRequest[]    findAll()
 * @method QuoteRequest[]    findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 */
class QuoteRequestRepository extends ServiceEntityRepository
{
    public function __construct(Finders $finders)
    {
        parent::__construct($finders, QuoteRequest::class);
    }

    /** @return list<QuoteRequest> the new ones, latest first */
    public function findNew(?int $limit = null): array
    {
        return $this->findBy(['status' => QuoteStatus::NEW], ['createdAt' => 'DESC'], $limit);
    }

    public function countNew(): int
    {
        return $this->count(['status' => QuoteStatus::NEW]);
    }

    /**
     * The requests older than the notice says they are kept: removed, whatever their status, through the unit of
     * work (a bulk DELETE before: the same rows, now with their lifecycle - QuoteRequest has no listener).
     */
    public function purgeOlderThan(\DateTimeImmutable $before): int
    {
        $old = $this->findBy([Group::of(Parser::SEPARATOR_AND, Criterion::of('createdAt', $before, Parser::OPTION_LOWER))]);
        foreach ($old as $quote) {
            $this->remove($quote);
        }
        if ($old) {
            $this->flush();
        }

        return \count($old);
    }
}
