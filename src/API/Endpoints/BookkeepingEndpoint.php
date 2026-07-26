<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : BookkeepingEndpoint.php
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
use Orgamax\Entities\Bookkeeping\ChartOfAccountsList;

class BookkeepingEndpoint extends EndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'bookkeeping/getchartofaccounts';

    public function get(?ID $id = null): ?NamedEntityInterface {
        self::logErrorAndThrow(NotAllowedException::class, 'The bookkeeping resource has no single-item route, use getChartOfAccounts()', [], null, 405);
    }

    /**
     * Liefert den paginierten Kontenrahmen.
     *
     * @param array<string, mixed> $queryParams
     * @param array<string, mixed> $options
     */
    public function getChartOfAccounts(array $queryParams = [], array $options = []): ?ChartOfAccountsList {
        self::logDebug('Fetching chart of accounts', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?ChartOfAccountsList {
            $response = parent::getContents($queryParams, $options);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return ChartOfAccountsList::fromJson($response, self::$logger);
        }, 'Chart of accounts fetched');
    }

    /**
     * Alias für getChartOfAccounts().
     */
    public function search(array $queryParams = [], array $options = []): ?ChartOfAccountsList {
        return $this->getChartOfAccounts($queryParams, $options);
    }
}
