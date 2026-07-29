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

use APIToolkit\API\Pagination\OffsetPaginator;
use APIToolkit\Contracts\Abstracts\API\EndpointAbstract;
use Generator;
use Orgamax\Contracts\Abstracts\ListResponseAbstract;

/**
 * Basis für Endpoints mit limit/offset-Suche.
 *
 * Die orgaMAX-API begrenzt Listen serverseitig (Default 100, Maximum 250) und
 * meldet die Gesamtzahl im meta-Block. searchAll() läuft die Seiten über den
 * {@see OffsetPaginator} des api-toolkits durch, sodass Aufrufer weder Offset
 * noch Abbruchbedingung selbst führen müssen.
 */
abstract class PagedEndpointAbstract extends EndpointAbstract {
    /** Serverseitiges Maximum je Anfrage. */
    public const MAX_LIMIT = 250;

    public const DEFAULT_LIMIT = 100;

    /**
     * @param array<string, mixed> $queryParams
     * @param array<string, mixed> $options
     */
    abstract public function search(array $queryParams = [], array $options = []): ?ListResponseAbstract;

    /**
     * Iteriert alle Treffer einer Suche über sämtliche Seiten hinweg.
     *
     * @param array<string, mixed> $queryParams Suchparameter ohne limit/offset
     * @param array<string, mixed> $options
     * @param int|null $maxPages Obergrenze für die Zahl geladener Seiten
     * @return Generator<int, mixed>
     */
    public function searchAll(array $queryParams = [], int $limit = self::DEFAULT_LIMIT, array $options = [], ?int $maxPages = null): Generator {
        $limit = max(1, min($limit, self::MAX_LIMIT));

        $paginator = new OffsetPaginator(
            fn (int $page): array => $this->search(
                array_merge($queryParams, ['limit' => $limit, 'offset' => $page * $limit]),
                $options
            )?->getValues() ?? [],
            $limit,
            0,
            $maxPages
        );

        yield from $paginator;
    }
}
