<?php

namespace App\Model\Representation;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\Annotation as JMS;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use OpenApi\Annotations as OA;

/**
 * Class Pagination
 * @package App\Model\Representation
 * @JMS\ExclusionPolicy("all")
 * @OA\Schema()
 */
class Pagination
{
    public const DEFAULT_LIMIT = 25;
    public const DEFAULT_PAGE  = 1;

    /**
     * @var int
     * @OA\Property()
     * @JMS\Expose()
     * @JMS\Groups({"pagination"})
     */
    private $currentPageNumber;

    /**
     * @var int
     * @OA\Property()
     * @JMS\Expose()
     * @JMS\Groups({"pagination"})
     */
    private $numItemsPerPage;

    /**
     * @var int
     * @OA\Property()
     * @JMS\Expose()
     * @JMS\Groups({"pagination"})
     */
    private $totalCount;

    /**
     * @var int
     * @OA\Property()
     * @JMS\Expose()
     * @JMS\Groups({"pagination"})
     */
    private $totalPages;

    /**
     * @var array
     * @OA\Property(@OA\Items())
     * @JMS\Expose()
     * @JMS\Groups({"pagination"})
     */
    private $items = array();

    /**
     * @param PaginationInterface $pagination
     * @return Pagination
     */
    public static function createFromPaginationInterface(PaginationInterface $pagination): Pagination
    {
        $representation = (new Pagination())
            ->setNumItemsPerPage($pagination->getItemNumberPerPage())
            ->setCurrentPageNumber($pagination->getCurrentPageNumber())
            ->setTotalPages(ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()))
            ->setTotalCount($pagination->getTotalItemCount())
            ->setItems($pagination->getItems());

        return $representation;
    }

    /**
     * @param QueryBuilder $qb
     * @param PaginatorInterface $paginator
     * @param ParamFetcher $fetcher
     * @return Pagination
     */
    public static function paginate(QueryBuilder $qb, PaginatorInterface $paginator, ParamFetcher $fetcher): Pagination
    {
        $pagination = $paginator->paginate(
            $qb,
            $fetcher->get('page'),
            $fetcher->get('limit'),
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => $qb->getRootAliases()[0].'.'.$fetcher->get('sort'),
                PaginatorInterface::DEFAULT_SORT_DIRECTION  => $fetcher->get('direction'),
            ]
        );

        return self::createFromPaginationInterface($pagination);
    }

    /**
     * @return int
     */
    public function getCurrentPageNumber(): int
    {
        return $this->currentPageNumber;
    }

    /**
     * @param int $currentPageNumber
     * @return Pagination
     */
    public function setCurrentPageNumber(int $currentPageNumber): Pagination
    {
        $this->currentPageNumber = $currentPageNumber;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumItemsPerPage(): int
    {
        return $this->numItemsPerPage;
    }

    /**
     * @param int $numItemsPerPage
     * @return Pagination
     */
    public function setNumItemsPerPage(int $numItemsPerPage): Pagination
    {
        $this->numItemsPerPage = $numItemsPerPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     * @return Pagination
     */
    public function setTotalCount(int $totalCount): Pagination
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @param int $totalPages
     * @return Pagination
     */
    public function setTotalPages(int $totalPages): Pagination
    {
        $this->totalPages = $totalPages;

        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return Pagination
     */
    public function setItems(array $items): Pagination
    {
        $this->items = $items;

        return $this;
    }
}