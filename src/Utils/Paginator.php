<?php

namespace App\Utils;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use Exception;


class Paginator
{
    private const PAGE_SIZE = 10;
    private ArrayIterator $result;
    private int $numResult;
    private int $currentPage;

    /**
     *  Paginator construct
     * @param QueryBuilder $queryBuilder
     * @param $pageSize
     */
    public function __construct(
        private QueryBuilder $queryBuilder,
        private $pageSize = self::PAGE_SIZE,
    ){}

    /**
     * @param int $page
     * @return $this
     * @throws Exception
     */
    final public function pagination(int $page = 1): self
    {
        $this->currentPage = (int) max(1, $page);
        $firsResult = ($this->currentPage - 1) * $this->pageSize;

        $query = $this->queryBuilder
            ->setFirstResult($firsResult)
            ->setMaxResults($this->pageSize)
            ->getQuery();

        $paginator = new  \Doctrine\ORM\Tools\Pagination\Paginator($query, true);

        $this->result = $paginator->getIterator();
        $this->numResult = $paginator->count();
    }

    /**
     * @return ArrayIterator
     */
    final public function getResult(): ArrayIterator
    {
        return $this->result;
    }

    /**
     * @return int
     */
    final public function getNumResult(): int
    {
        return $this->numReult;
    }

    /**
     * @return int
     */
    final public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    final public function getLastPage():int
    {
        return (int)ceil($this->numReult / $this->pageSize);
    }

    /**
     * @return int
     */
    final public function  getPageSize(): int
    {
        return  $this->pageSize;
    }

    /**
     * @return bool
     */
    final public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * @return int
     */
    final public function getPreviousPage(): int
    {
        return max(1,$this->currentPage - 1);
    }

    /**
     * @return bool
     */
    final public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    final public function  hasToPaginate():bool
    {
        return $this->numResult > $this->pageSize;
    }
}