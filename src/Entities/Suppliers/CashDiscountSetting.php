<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : CashDiscountSetting.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Suppliers;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Skonto-Einstellungen eines Lieferanten (cashDiscountSetting-Feld).
 */
class CashDiscountSetting extends NamedEntity {
    protected ?int $dueDays;

    protected ?float $discount;

    protected ?int $discountDays;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getDueDays(): ?int {
        return $this->dueDays ?? null;
    }

    public function setDueDays(?int $dueDays): void {
        $this->dueDays = $dueDays;
    }

    public function getDiscount(): ?float {
        return $this->discount ?? null;
    }

    public function setDiscount(?float $discount): void {
        $this->discount = $discount;
    }

    public function getDiscountDays(): ?int {
        return $this->discountDays ?? null;
    }

    public function setDiscountDays(?int $discountDays): void {
        $this->discountDays = $discountDays;
    }
}
