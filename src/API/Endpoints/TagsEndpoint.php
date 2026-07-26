<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : TagsEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints;

use APIToolkit\Contracts\Abstracts\API\EndpointAbstract;
use APIToolkit\Contracts\Interfaces\NamedEntityInterface;
use APIToolkit\Entities\ID;
use APIToolkit\Exceptions\NotAllowedException;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\Tags\TagList;

class TagsEndpoint extends EndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'tags';

    public function get(?ID $id = null): ?NamedEntityInterface {
        self::logErrorAndThrow(NotAllowedException::class, 'The tags resource has no single-item route, use search()', [], null, 405);
    }

    /**
     * Liefert alle Tags. Die Route ist laut Spec nicht paginiert,
     * Query-Parameter werden dennoch durchgereicht.
     */
    public function search(array $queryParams = [], array $options = []): ?TagList {
        self::logDebug('Searching tags', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?TagList {
            $response = parent::getContents($queryParams, $options);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return TagList::fromJson($response, self::$logger);
        }, 'Tags search completed');
    }
}
