<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : OrdersEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\Contracts\Abstracts\API\DocumentEndpointAbstract;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\Common\ResourceResponse;
use Orgamax\Entities\Orders\{Order, OrderInvoiceResponse, OrderList, OrderResponse};

class OrdersEndpoint extends DocumentEndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'order';

    public function create(Order $data): ResourceResponse {
        if (!$data->isValid()) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'Order data is not valid: customerId is required');
        }
        self::logDebug('Creating order', ['endpoint' => $this->endpoint]);

        return self::logInfoWithTimer(
            fn () => ResourceResponse::fromJson(
                // Die Spec antwortet hier mit 200 (nicht 201).
                parent::postContents($data->toArray(), [], "{$this->getEndpointUrl()}/", 200),
                self::$logger
            ),
            'Order created'
        );
    }

    public function get(?ID $id = null): ?Order {
        if (is_null($id)) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'ID is required for getting an order');
        }
        self::logDebug('Fetching order', ['id' => $id->toString()]);

        return self::logDebugWithTimer(function () use ($id): ?Order {
            $response = parent::getContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return OrderResponse::fromJson($response, self::$logger)->getData();
        }, "Order fetched (ID: {$id->toString()})");
    }

    public function search(array $queryParams = [], array $options = []): ?OrderList {
        self::logDebug('Searching orders', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?OrderList {
            $response = parent::getContents($queryParams, $options);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return OrderList::fromJson($response, self::$logger);
        }, 'Orders search completed');
    }

    /**
     * Erzeugt eine Rechnung aus dem Auftrag (POST /order/{id}/invoice).
     */
    public function createInvoice(ID $id): OrderInvoiceResponse {
        self::logDebug('Creating invoice from order', ['id' => $id->toString()]);

        return self::logInfoWithTimer(
            fn () => OrderInvoiceResponse::fromJson(
                parent::postContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}/invoice", 200),
                self::$logger
            ),
            "Invoice created from order (ID: {$id->toString()})"
        );
    }
}
