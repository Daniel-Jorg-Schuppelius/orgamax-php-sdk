<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : GraduatedPrice.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Articles;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use APIToolkit\Traits\MoneyAccessorTrait;
use CommonToolkit\ValueObjects\Money;
use Psr\Log\LoggerInterface;

/**
 * Staffelpreis eines Artikels: ab quantity gilt der Netto- bzw. Brutto-Stückpreis
 * (je nach calculationBase des Artikels).
 */
class GraduatedPrice extends NamedEntity {
    use MoneyAccessorTrait;

    protected ?float $quantity;

    protected ?float $netUnitPrice;

    protected ?float $grossUnitPrice;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getQuantity(): ?float {
        return $this->quantity ?? null;
    }

    public function setQuantity(?float $quantity): void {
        $this->quantity = $quantity;
    }

    public function getNetUnitPrice(): ?float {
        return $this->netUnitPrice ?? null;
    }

    public function setNetUnitPrice(?float $netUnitPrice): void {
        $this->netUnitPrice = $netUnitPrice;
    }

    public function getGrossUnitPrice(): ?float {
        return $this->grossUnitPrice ?? null;
    }

    public function setGrossUnitPrice(?float $grossUnitPrice): void {
        $this->grossUnitPrice = $grossUnitPrice;
    }

    /*
     * Betragsfelder der API sind JSON-Zahlen; für exakte Rechnungen liefern
     * die folgenden Accessoren sie als Money in der Belegwährung.
     */
    public function getNetUnitPriceAsMoney(): ?Money {
        return $this->toMoney($this->netUnitPrice ?? null);
    }

    public function getGrossUnitPriceAsMoney(): ?Money {
        return $this->toMoney($this->grossUnitPrice ?? null);
    }
}
