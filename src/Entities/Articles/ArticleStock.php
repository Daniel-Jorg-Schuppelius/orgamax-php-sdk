<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ArticleStock.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Articles;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

class ArticleStock extends NamedEntity {
    protected ?float $stockAmount;

    protected ?float $reservedAmount;

    protected ?float $availableAmount;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getStockAmount(): ?float {
        return $this->stockAmount ?? null;
    }

    public function getReservedAmount(): ?float {
        return $this->reservedAmount ?? null;
    }

    public function getAvailableAmount(): ?float {
        return $this->availableAmount ?? null;
    }
}
