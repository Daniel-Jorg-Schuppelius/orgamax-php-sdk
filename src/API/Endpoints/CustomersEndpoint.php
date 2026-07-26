<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : CustomersEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints;

use APIToolkit\Contracts\Abstracts\API\EndpointAbstract;
use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\Common\ResourceResponse;
use Orgamax\Entities\Customers\{Customer, CustomerList, CustomerResponse};

class CustomersEndpoint extends EndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'customer';

    public function create(Customer $data): CustomerResponse {
        self::logDebug('Creating customer', ['endpoint' => $this->endpoint]);

        return self::logInfoWithTimer(
            fn () => CustomerResponse::fromJson(
                parent::postContents($data->toArray(), [], "{$this->getEndpointUrl()}/", 201),
                self::$logger
            ),
            'Customer created'
        );
    }

    public function get(?ID $id = null): ?Customer {
        if (is_null($id)) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'ID is required for getting a customer');
        }
        self::logDebug('Fetching customer', ['id' => $id->toString()]);

        return self::logDebugWithTimer(function () use ($id): ?Customer {
            $response = parent::getContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return CustomerResponse::fromJson($response, self::$logger)->getData();
        }, "Customer fetched (ID: {$id->toString()})");
    }

    public function search(array $queryParams = [], array $options = []): ?CustomerList {
        self::logDebug('Searching customers', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?CustomerList {
            $response = parent::getContents($queryParams, $options);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return CustomerList::fromJson($response, self::$logger);
        }, 'Customers search completed');
    }

    public function update(ID $id, Customer $data): ResourceResponse {
        self::logDebug('Updating customer', ['id' => $id->toString()]);

        return self::logInfoWithTimer(
            fn () => ResourceResponse::fromJson(
                parent::putContents($data->toArray(), [], "{$this->getEndpointUrl()}/{$id->toString()}", 200),
                self::$logger
            ),
            "Customer updated (ID: {$id->toString()})"
        );
    }
}
