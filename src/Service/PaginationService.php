<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class PaginationService
{
    /**
     * Calcule les paramètres de pagination (page, limite et décalage) à partir de la requête HTTP.
     *
     * @param Request $request : la requête HTTP contenant le paramètre 'page'
     * @param int     $limit   : le nombre maximal d'éléments par page
     *
     * @return array : les paramètres de pagination (page, offset, limit)
     */
    public function getPaginationParams(Request $request, int $limit): array
    {
        $page = $request->query->getInt('page', 1);
        $offset = $limit * ($page - 1);

        return [
            'page' => $page,
            'offset' => $offset,
            'limit' => $limit,
        ];
    }

    /**
     * Calcule le nombre total de pages nécessaires pour paginer les éléments.
     *
     * @param int $totalItems : le nombre total d'éléments
     * @param int $limit      : le nombre maximal d'éléments par page
     *
     * @return int : le nombre total de pages
     */
    public function getTotalPages(int $totalItems, int $limit): int
    {
        return (int) ceil($totalItems / $limit);
    }
}
