<?php

namespace App\Service;

class PaginatorService
{

    public function render(string $itemsName, $queryBuilder, int $limit = 10, int $page = 1, $increment = 10): array
    {
        $offset = (int) ($page - 1) * $limit;

        $nbItems = count($queryBuilder->getQuery()->getResult());
        $lastPageNb = ceil($nbItems / $limit);

        // Add a limit and an offset to the query to filter query result
        $queryBuilder
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        // Get the items of the requested page only
        $data = $queryBuilder->getQuery()->getResult();

        $isLastPage =  $this->isLastPage($page, $lastPageNb);

        // Build the array of options for the render function
        $options = [
            $itemsName => $data,
            'page' => $page,
            'limit' => $limit,
            'isLastPage' => $isLastPage,
            'lastPageNb' => (int) $lastPageNb,
            'nextpage' => ($limit + $increment)
        ];

        return $options;
    }

    public function isLastPage($page, $lastPageNb): bool
    {
        if ($page >= $lastPageNb) {
            return true;
        }

        return false;
    }
}
