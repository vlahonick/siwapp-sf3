<?php

namespace Siwapp\CustomerBundle\Repository;

use Knp\Component\Pager\PaginatorInterface;
use Siwapp\CoreBundle\Entity\AbstractInvoice;
use Siwapp\CustomerBundle\Entity\Customer;
use Siwapp\EstimateBundle\Entity\Estimate;
use Siwapp\InvoiceBundle\Entity\Invoice;
use Siwapp\RecurringInvoiceBundle\Entity\RecurringInvoice;

/**
 * CustomerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CustomerRepository extends \Doctrine\ORM\EntityRepository
{
    public function findLike($term)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from(Customer::class, 'c')
            ->where('c.name LIKE :name')
            ->orWhere('c.identification LIKE :name')
            ->setParameter('name', '%'. $term .'%')
            ->getQuery()
            ->getResult();
    }

    public function findByInvoice(Invoice $invoice)
    {
        return $this->getByInvoiceQuery($invoice)->getResult();
    }

    public function findByRecurringInvoice(RecurringInvoice $invoice)
    {
        return $this->getByInvoiceQuery($invoice, 'recurring_invoices')->getResult();
    }

    public function findByEstimate(Estimate $invoice)
    {
        return $this->getByInvoiceQuery($invoice, 'estimates')->getResult();
    }

    protected function getByInvoiceQuery(AbstractInvoice $invoice, $propertyName = 'invoices')
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from(Customer::class, 'c')
            ->join('c.' . $propertyName, 'i')
            ->where('i.id = ?1')
            ->setParameter(1, $invoice->getId())
            ->getQuery();
    }

    public function paginatedSearch(array $params, $limit = 50, $page = 1)
    {
        if (!$this->paginator) {
            throw new \RuntimeException('You have to set a paginator first using setPaginator() method');
        }

        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from(Customer::class, 'c');
        foreach ($params as $field => $value) {
            if ($value === null) {
                continue;
            }
            if ($field == 'terms') {
                $terms = $qb->expr()->literal("%$value%");
                $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('c.name', $terms),
                    $qb->expr()->like('c.identification', $terms),
                    $qb->expr()->like('c.contact_person', $terms)
                ));
            }
        }

        return $this->paginator->paginate($qb->getQuery(), $page, $limit);
    }

    /**
     * There is no easy way to inject things into repositories yet.
     */
    public function setPaginator(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }
}