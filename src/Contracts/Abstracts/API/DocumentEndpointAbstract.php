<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : DocumentEndpointAbstract.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Contracts\Abstracts\API;

use APIToolkit\Entities\ID;
use Orgamax\Contracts\Interfaces\API\DocumentEndpointInterface;

/**
 * Gemeinsame Dokument-Logik für belegbasierte Endpoints (Invoice, Offer,
 * Order, DeliveryNote): GET {resource}/document/{id} liefert das PDF.
 */
abstract class DocumentEndpointAbstract extends PagedEndpointAbstract implements DocumentEndpointInterface {
    public function document(ID $id, ?string $type = null, ?string $filename = null): string {
        self::logDebug('Fetching document', ['endpoint' => $this->endpoint, 'id' => $id->toString()]);

        $queryParams = array_filter([
            'type' => $type,
            'filename' => $filename,
        ], fn ($value) => $value !== null);

        return self::logDebugWithTimer(
            fn () => parent::getContents(
                $queryParams,
                ['headers' => ['Accept' => 'application/pdf']],
                "{$this->getEndpointUrl()}/document/{$id->toString()}"
            ),
            "Document fetched (ID: {$id->toString()})"
        );
    }
}
