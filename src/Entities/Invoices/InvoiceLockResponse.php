<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : InvoiceLockResponse.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Invoices;

use Orgamax\Contracts\Abstracts\ItemResponseAbstract;
use Psr\Log\LoggerInterface;

/**
 * Antwort von PUT /invoice/{id}/lock — {data: InvoiceLockInfo}.
 */
class InvoiceLockResponse extends ItemResponseAbstract {
    protected ?InvoiceLockInfo $data;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getData(): ?InvoiceLockInfo {
        return $this->data ?? null;
    }
}
