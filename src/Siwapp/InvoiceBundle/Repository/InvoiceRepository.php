<?php

namespace Siwapp\InvoiceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use Siwapp\CoreBundle\Entity\Serie;
use Siwapp\CoreBundle\Repository\AbstractInvoiceRepository;
use Siwapp\InvoiceBundle\Entity\Invoice;

/**
 * InvoiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoiceRepository extends AbstractInvoiceRepository
{
    protected function addPaginatedSearchSelects(QueryBuilder $qb)
    {
        parent::addPaginatedSearchSelects($qb);
        // For invoices we allow sorting by due.
        $qb->addSelect('i.gross_amount - i.paid_amount AS due_amount');

        return $qb;
    }
}
