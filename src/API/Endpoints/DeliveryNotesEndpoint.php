<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : DeliveryNotesEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\Contracts\Abstracts\API\DocumentEndpointAbstract;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\DeliveryNotes\{DeliveryNote, DeliveryNoteList, DeliveryNoteResponse};

class DeliveryNotesEndpoint extends DocumentEndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'deliveryNote';

    public function get(?ID $id = null): ?DeliveryNote {
        if (is_null($id)) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'ID is required for getting a delivery note');
        }
        self::logDebug('Fetching delivery note', ['id' => $id->toString()]);

        return self::logDebugWithTimer(function () use ($id): ?DeliveryNote {
            $response = parent::getContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return DeliveryNoteResponse::fromJson($response, self::$logger)->getData();
        }, "Delivery note fetched (ID: {$id->toString()})");
    }

    public function search(array $queryParams = [], array $options = []): ?DeliveryNoteList {
        self::logDebug('Searching delivery notes', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?DeliveryNoteList {
            $response = parent::getContents($queryParams, $options, $this->getEndpointUrl());
            if (empty($response) || $response === '[]') {
                return null;
            }

            return DeliveryNoteList::fromJson($response, self::$logger);
        }, 'Delivery notes search completed');
    }
}
