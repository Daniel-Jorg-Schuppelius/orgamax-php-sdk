<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : SuppliersEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\Contracts\Abstracts\API\PagedEndpointAbstract;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\Suppliers\{Supplier, SupplierList, SupplierResponse};

class SuppliersEndpoint extends PagedEndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'supplier';

    /**
     * POST /supplier (OHNE trailing slash). Die Spec liefert den vollständigen
     * Lieferanten im {meta, data}-Envelope zurück.
     */
    public function create(Supplier $data): SupplierResponse {
        self::logDebug('Creating supplier', ['endpoint' => $this->endpoint]);

        return self::logInfoWithTimer(
            fn () => SupplierResponse::fromJson(
                parent::postContents($data->toArray(), [], $this->getEndpointUrl(), 201),
                self::$logger
            ),
            'Supplier created'
        );
    }

    public function get(?ID $id = null): ?Supplier {
        if (is_null($id)) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'ID is required for getting a supplier');
        }
        self::logDebug('Fetching supplier', ['id' => $id->toString()]);

        return self::logDebugWithTimer(function () use ($id): ?Supplier {
            $response = parent::getContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return SupplierResponse::fromJson($response, self::$logger)->getData();
        }, "Supplier fetched (ID: {$id->toString()})");
    }

    public function search(array $queryParams = [], array $options = []): ?SupplierList {
        self::logDebug('Searching suppliers', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?SupplierList {
            $response = parent::getContents($queryParams, $options);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return SupplierList::fromJson($response, self::$logger);
        }, 'Suppliers search completed');
    }

    /**
     * PUT /supplier/{id}. Laut Spec enthält die Antwort den vollständigen
     * SupplierData-Datensatz (nicht nur {id}), daher SupplierResponse statt
     * Common\ResourceResponse.
     */
    public function update(ID $id, Supplier $data): SupplierResponse {
        self::logDebug('Updating supplier', ['id' => $id->toString()]);

        return self::logInfoWithTimer(
            fn () => SupplierResponse::fromJson(
                parent::putContents($data->toArray(), [], "{$this->getEndpointUrl()}/{$id->toString()}", 200),
                self::$logger
            ),
            "Supplier updated (ID: {$id->toString()})"
        );
    }

    public function delete(ID $id): bool {
        self::logDebug('Deleting supplier', ['id' => $id->toString()]);

        return self::logInfoWithTimer(function () use ($id): bool {
            parent::deleteContents([], "{$this->getEndpointUrl()}/{$id->toString()}", 204);

            return true;
        }, "Supplier deleted (ID: {$id->toString()})");
    }
}
