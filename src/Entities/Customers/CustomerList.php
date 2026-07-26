<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : CustomerList.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Customers;

use Orgamax\Contracts\Abstracts\ListResponseAbstract;
use Psr\Log\LoggerInterface;

/**
 * Antwort von GET /customer — paginierte Kundenliste.
 */
class CustomerList extends ListResponseAbstract {
    protected ?Customers $data;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getData(): ?Customers {
        return $this->data ?? null;
    }

    /**
     * @return array<int, Customer>
     */
    public function getValues(): array {
        return $this->data?->getValues() ?? [];
    }
}
