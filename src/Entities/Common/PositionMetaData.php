<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : PositionMetaData.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Common;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use APIToolkit\Traits\MoneyAccessorTrait;
use CommonToolkit\ValueObjects\Money;
use Psr\Log\LoggerInterface;

/**
 * Meta-Daten einer Belegposition (Referenz auf den zugrundeliegenden Artikel).
 */
class PositionMetaData extends NamedEntity {
    use MoneyAccessorTrait;

    protected ?int $id;

    protected ?string $type;

    protected ?string $number;

    protected ?float $purchasePrice;

    protected ?string $calculationBase;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function getType(): ?string {
        return $this->type ?? null;
    }

    public function getNumber(): ?string {
        return $this->number ?? null;
    }

    public function getPurchasePrice(): ?float {
        return $this->purchasePrice ?? null;
    }

    public function getCalculationBase(): ?string {
        return $this->calculationBase ?? null;
    }

    /*
     * Betragsfelder der API sind JSON-Zahlen; für exakte Rechnungen liefern
     * die folgenden Accessoren sie als Money in der Belegwährung.
     */
    public function getPurchasePriceAsMoney(): ?Money {
        return $this->toMoney($this->purchasePrice ?? null);
    }
}
