<?php
/*
 * Created on   : Wed Jul 29 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : PagedEndpointAbstract.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Contracts\Abstracts\API;

use APIToolkit\Contracts\Abstracts\API\PagedEndpointAbstract as APIToolkitPagedEndpointAbstract;
use Orgamax\Contracts\Abstracts\ListResponseAbstract;

/**
 * Basis für Endpoints mit limit/offset-Suche.
 *
 * Die orgaMAX-API begrenzt Listen serverseitig (Default 100, Maximum 250) und
 * meldet die Gesamtzahl im meta-Block; searchAll() kommt aus dem api-toolkit.
 */
abstract class PagedEndpointAbstract extends APIToolkitPagedEndpointAbstract {
    public const MAX_PAGE_SIZE = 250;

    /**
     * @param array<string, mixed> $queryParams
     * @param array<string, mixed> $options
     */
    abstract public function search(array $queryParams = [], array $options = []): ?ListResponseAbstract;

    /**
     * @return array<string, mixed>
     */
    protected function pageQueryParams(int $page, int $pageSize): array {
        return ['limit' => $pageSize, 'offset' => $page * $pageSize];
    }

    /**
     * @param array<string, mixed> $queryParams
     * @param array<string, mixed> $options
     * @return array<int, mixed>
     */
    protected function pageItems(array $queryParams, array $options): array {
        return $this->search($queryParams, $options)?->getValues() ?? [];
    }
}
