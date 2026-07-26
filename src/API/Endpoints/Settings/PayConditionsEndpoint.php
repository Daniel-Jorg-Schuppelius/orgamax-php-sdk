<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : PayConditionsEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints\Settings;

use APIToolkit\Contracts\Abstracts\API\EndpointAbstract;
use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\Settings\{PayCondition, PayConditionList, PayConditionResponse, PayConditions};

class PayConditionsEndpoint extends EndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'setting/payCondition';

    /**
     * POST /setting/payCondition/ — liefert die aktuelle Liste aller
     * Zahlungsbedingungen zurück (Status 200).
     */
    public function create(PayCondition $data): ?PayConditions {
        if (!$data->isValid()) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'PayCondition data is not valid: name is required');
        }
        self::logDebug('Creating pay condition', ['endpoint' => $this->endpoint]);

        return self::logInfoWithTimer(function () use ($data): ?PayConditions {
            $response = parent::postContents($data->toArray(), [], "{$this->getEndpointUrl()}/", 200);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return PayConditions::fromJson($response, self::$logger);
        }, 'PayCondition created');
    }

    public function get(?ID $id = null): ?PayCondition {
        if (is_null($id)) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'ID is required for getting a pay condition');
        }
        self::logDebug('Fetching pay condition', ['id' => $id->toString()]);

        return self::logDebugWithTimer(function () use ($id): ?PayCondition {
            $response = parent::getContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return PayConditionResponse::fromJson($response, self::$logger)->getData();
        }, "PayCondition fetched (ID: {$id->toString()})");
    }

    public function search(array $queryParams = [], array $options = []): ?PayConditionList {
        self::logDebug('Searching pay conditions', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?PayConditionList {
            $response = parent::getContents($queryParams, $options, $this->getEndpointUrl());
            if (empty($response) || $response === '[]') {
                return null;
            }

            return PayConditionList::fromJson($response, self::$logger);
        }, 'PayConditions search completed');
    }

    /**
     * PUT /setting/payCondition/ — die id steht NICHT im Pfad, sondern im
     * Body; die Antwort ist die aktuelle Liste aller Zahlungsbedingungen.
     */
    public function update(PayCondition $data): ?PayConditions {
        if (is_null($data->getId())) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'PayCondition id is required for update (id is sent in the body)');
        }
        if (!$data->isValid()) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'PayCondition data is not valid: name is required');
        }
        self::logDebug('Updating pay condition', ['id' => $data->getId()]);

        return self::logInfoWithTimer(function () use ($data): ?PayConditions {
            $response = parent::putContents($data->toArray(), [], "{$this->getEndpointUrl()}/", 200);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return PayConditions::fromJson($response, self::$logger);
        }, "PayCondition updated (ID: {$data->getId()})");
    }
}
