<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : OffersEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\Contracts\Abstracts\API\DocumentEndpointAbstract;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\Offers\{Offer, OfferList, OfferResponse};

class OffersEndpoint extends DocumentEndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'offer';

    public function get(?ID $id = null): ?Offer {
        if (is_null($id)) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'ID is required for getting an offer');
        }
        self::logDebug('Fetching offer', ['id' => $id->toString()]);

        return self::logDebugWithTimer(function () use ($id): ?Offer {
            $response = parent::getContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return OfferResponse::fromJson($response, self::$logger)->getData();
        }, "Offer fetched (ID: {$id->toString()})");
    }

    public function search(array $queryParams = [], array $options = []): ?OfferList {
        self::logDebug('Searching offers', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?OfferList {
            $response = parent::getContents($queryParams, $options);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return OfferList::fromJson($response, self::$logger);
        }, 'Offers search completed');
    }
}
