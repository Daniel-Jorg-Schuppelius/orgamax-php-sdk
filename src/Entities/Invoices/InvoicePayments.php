<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : InvoicePayments.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Invoices;

use APIToolkit\Contracts\Abstracts\NamedValues;
use Psr\Log\LoggerInterface;

/**
 * Antwort von POST /invoice/{id}/payment — die API liefert ein nacktes
 * JSON-Array von InvoicePayment-Objekten (kein Envelope).
 *
 * @extends NamedValues<InvoicePayment>
 */
class InvoicePayments extends NamedValues {
    /**
     * @param array<int|string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        $this->entityName = 'data';
        $this->valueClassName = InvoicePayment::class;
        parent::__construct($data, $logger);
    }
}
