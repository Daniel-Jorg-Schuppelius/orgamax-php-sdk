<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : UsersEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints;

use APIToolkit\Contracts\Interfaces\NamedEntityInterface;
use APIToolkit\Entities\ID;
use APIToolkit\Exceptions\NotAllowedException;
use Orgamax\Contracts\Abstracts\API\PagedEndpointAbstract;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\Users\UserList;

class UsersEndpoint extends PagedEndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'user';

    public function get(?ID $id = null): ?NamedEntityInterface {
        self::logErrorAndThrow(NotAllowedException::class, 'The user resource has no single-item route, use search()', [], null, 405);
    }

    public function search(array $queryParams = [], array $options = []): ?UserList {
        self::logDebug('Searching users', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?UserList {
            $response = parent::getContents($queryParams, $options);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return UserList::fromJson($response, self::$logger);
        }, 'Users search completed');
    }
}
