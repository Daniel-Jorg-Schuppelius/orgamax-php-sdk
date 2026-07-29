<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : Invoice.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Invoices;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use APIToolkit\Traits\MoneyAccessorTrait;
use CommonToolkit\ValueObjects\Money;
use DateTimeImmutable;
use Orgamax\Entities\Common\{CustomerData, Positions};
use Orgamax\Entities\Settings\PayCondition;
use Orgamax\Enums\{InvoiceState, InvoiceType, PriceKind};
use Psr\Log\LoggerInterface;

/**
 * Rechnung — Vereinigung der Detail- (InvoiceDataObject) und Listen-Felder
 * (InvoiceListData) der API.
 */
class Invoice extends NamedEntity {
    use MoneyAccessorTrait;

    protected ?int $id;

    protected ?string $number;

    protected ?string $date;

    protected ?InvoiceType $type;

    protected ?PriceKind $priceKind;

    protected ?InvoiceState $state;

    protected ?int $customerId;

    protected ?float $totalNet;

    protected ?float $totalGross;

    protected ?float $outstandingAmount;

    protected ?float $cashDiscountTotal;

    protected ?string $dueToDate;

    protected ?CustomerData $customerData;

    protected ?int $payConditionId;

    protected ?PayCondition $payConditionData;

    protected ?Positions $positions;

    /** @var array<int|string, mixed>|null */
    protected ?array $metaData;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function getNumber(): ?string {
        return $this->number ?? null;
    }

    public function getDate(): ?string {
        return $this->date ?? null;
    }

    public function getType(): ?InvoiceType {
        return $this->type ?? null;
    }

    public function getPriceKind(): ?PriceKind {
        return $this->priceKind ?? null;
    }

    public function getState(): ?InvoiceState {
        return $this->state ?? null;
    }

    public function getCustomerId(): ?int {
        return $this->customerId ?? null;
    }

    public function getTotalNet(): ?float {
        return $this->totalNet ?? null;
    }

    public function getTotalGross(): ?float {
        return $this->totalGross ?? null;
    }

    public function getOutstandingAmount(): ?float {
        return $this->outstandingAmount ?? null;
    }

    public function getCashDiscountTotal(): ?float {
        return $this->cashDiscountTotal ?? null;
    }

    public function getDueToDate(): ?string {
        return $this->dueToDate ?? null;
    }

    public function getCustomerData(): ?CustomerData {
        return $this->customerData ?? null;
    }

    public function getPayConditionId(): ?int {
        return $this->payConditionId ?? null;
    }

    public function getPayConditionData(): ?PayCondition {
        return $this->payConditionData ?? null;
    }

    public function getPositions(): ?Positions {
        return $this->positions ?? null;
    }

    /**
     * Zusatzdaten aus der Listen-Antwort (Dunning/Cancellation, von der API
     * nur vage spezifiziert).
     *
     * @return array<int|string, mixed>|null
     */
    public function getMetaData(): ?array {
        return $this->metaData ?? null;
    }

    /*
     * Betragsfelder der API sind JSON-Zahlen; für exakte Rechnungen liefern
     * die folgenden Accessoren sie als Money in der Belegwährung.
     */
    public function getTotalNetAsMoney(): ?Money {
        return $this->toMoney($this->totalNet ?? null);
    }

    public function getTotalGrossAsMoney(): ?Money {
        return $this->toMoney($this->totalGross ?? null);
    }

    public function getOutstandingAmountAsMoney(): ?Money {
        return $this->toMoney($this->outstandingAmount ?? null);
    }

    public function getCashDiscountTotalAsMoney(): ?Money {
        return $this->toMoney($this->cashDiscountTotal ?? null);
    }

    /**
     * Datumsfelder kommen als String ("YYYY-MM-DD" bzw. ISO-8601) von der
     * API; die folgenden Accessoren geben sie typisiert zurück, ohne das
     * Wire-Format der Property anzutasten.
     */
    protected static function toDateTime(?string $value): ?DateTimeImmutable {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (\Exception) {
            return null;
        }
    }

    public function getDateAsDateTime(): ?DateTimeImmutable {
        return self::toDateTime($this->date ?? null);
    }

    public function getDueToDateAsDateTime(): ?DateTimeImmutable {
        return self::toDateTime($this->dueToDate ?? null);
    }
}
