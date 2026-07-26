<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : DeliveryConditionsEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints\Settings;

use APIToolkit\Contracts\Abstracts\API\EndpointAbstract;
use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\Settings\{DeliveryCondition, DeliveryConditionList, DeliveryConditionResponse};

class DeliveryConditionsEndpoint extends EndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'setting/deliveryCondition';

    /**
     * POST /setting/deliveryCondition/ — liefert die angelegte
     * Lieferbedingung zurück (Status 200).
     */
    public function create(DeliveryCondition $data): ?DeliveryCondition {
        if (!$data->isValid()) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'DeliveryCondition data is not valid: name is required');
        }
        self::logDebug('Creating delivery condition', ['endpoint' => $this->endpoint]);

        return self::logInfoWithTimer(function () use ($data): ?DeliveryCondition {
            $response = parent::postContents($data->toArray(), [], "{$this->getEndpointUrl()}/", 200);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return DeliveryCondition::fromJson($response, self::$logger);
        }, 'DeliveryCondition created');
    }

    public function get(?ID $id = null): ?DeliveryCondition {
        if (is_null($id)) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'ID is required for getting a delivery condition');
        }
        self::logDebug('Fetching delivery condition', ['id' => $id->toString()]);

        return self::logDebugWithTimer(function () use ($id): ?DeliveryCondition {
            $response = parent::getContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return DeliveryConditionResponse::fromJson($response, self::$logger)->getData();
        }, "DeliveryCondition fetched (ID: {$id->toString()})");
    }

    public function search(array $queryParams = [], array $options = []): ?DeliveryConditionList {
        self::logDebug('Searching delivery conditions', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?DeliveryConditionList {
            $response = parent::getContents($queryParams, $options, $this->getEndpointUrl());
            if (empty($response) || $response === '[]') {
                return null;
            }

            return DeliveryConditionList::fromJson($response, self::$logger);
        }, 'DeliveryConditions search completed');
    }

    /**
     * PUT /setting/deliveryCondition/{id} — antwortet mit 204 (no content).
     */
    public function update(ID $id, DeliveryCondition $data): bool {
        if (!$data->isValid()) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'DeliveryCondition data is not valid: name is required');
        }
        self::logDebug('Updating delivery condition', ['id' => $id->toString()]);

        return self::logInfoWithTimer(function () use ($id, $data): bool {
            parent::putContents($data->toArray(), [], "{$this->getEndpointUrl()}/{$id->toString()}", 204);

            return true;
        }, "DeliveryCondition updated (ID: {$id->toString()})");
    }
}
