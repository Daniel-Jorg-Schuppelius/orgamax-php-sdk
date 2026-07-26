<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : OrderInvoiceData.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Orders;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Orgamax\Entities\Invoices\Invoice;
use Psr\Log\LoggerInterface;

/**
 * Data-Block von POST /order/{id}/invoice: die erzeugte Rechnung und der
 * aktualisierte Auftrag.
 */
class OrderInvoiceData extends NamedEntity {
    protected ?Invoice $invoice;

    protected ?Order $order;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getInvoice(): ?Invoice {
        return $this->invoice ?? null;
    }

    public function getOrder(): ?Order {
        return $this->order ?? null;
    }
}
