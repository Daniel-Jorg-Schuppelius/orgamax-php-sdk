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

    /**
     * Legt ein Angebot an (POST /offer/). orgaMAX vergibt die Nummer aus den
     * Angebotseinstellungen; das Angebot entsteht im Zustand `open`.
     */
    public function create(Offer $data): ?Offer {
        if (!$data->isValid()) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'Offer data is not valid: customerId is required');
        }
        self::logDebug('Creating offer', ['endpoint' => $this->endpoint]);

        return self::logInfoWithTimer(
            fn (): ?Offer => $this->offerFrom(parent::postContents($data->toArray(), [], "{$this->getEndpointUrl()}/", [200, 201])),
            'Offer created'
        );
    }

    /**
     * Ändert ein Angebot (PUT /offer/{id}). Gesendete Positionen ersetzen die
     * ganze Liste; ein schon fakturiertes Angebot lehnt orgaMAX ab (LOCKED).
     */
    public function update(ID $id, Offer $data): ?Offer {
        self::logDebug('Updating offer', ['id' => $id->toString()]);

        return self::logInfoWithTimer(
            fn (): ?Offer => $this->offerFrom(parent::putContents($data->toArray(), [], "{$this->getEndpointUrl()}/{$id->toString()}", 200)),
            "Offer updated (ID: {$id->toString()})"
        );
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

    /** Die Spec nennt als Antwort das Angebot selbst; die übrigen Routen liefern es im {meta, data}-Envelope. */
    private function offerFrom(string $response): ?Offer {
        $decoded = json_decode($response, true);
        if (!is_array($decoded) || $decoded === []) {
            return null;
        }

        return (new OfferResponse(array_key_exists('data', $decoded) ? $decoded : ['data' => $decoded], self::$logger))->getData();
    }
}
