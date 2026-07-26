<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : CustomerResponse.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Customers;

use Orgamax\Contracts\Abstracts\ItemResponseAbstract;
use Psr\Log\LoggerInterface;

/**
 * Antwort von POST /customer/ und GET /customer/{id} — vollständiger Kunde
 * im {meta, data}-Envelope.
 */
class CustomerResponse extends ItemResponseAbstract {
    protected ?Customer $data;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getData(): ?Customer {
        return $this->data ?? null;
    }
}
