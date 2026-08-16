<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : Expense.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Expenses;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use APIToolkit\Traits\MoneyAccessorTrait;
use CommonToolkit\ValueObjects\Money;
use DateTimeImmutable;
use Orgamax\Enums\PayKind;
use Psr\Log\LoggerInterface;

/**
 * Ausgabe (Betriebsausgabe). Vereinigung der Listen-Felder (ExpenseListData)
 * und Detail-Felder (ExpenseDataObject) der API.
 */
class Expense extends NamedEntity {
    use MoneyAccessorTrait;

    protected ?int $id;

    protected ?string $date;

    protected ?string $payDate;

    protected ?PayKind $payKind;

    protected ?string $payee;

    protected ?string $description;

    protected ?float $price;

    protected ?float $priceTotal;

    protected ?float $vat;

    protected ?float $vatPercent;

    protected ?float $vatAmount;

    protected ?string $receiptNumber;

    protected ?string $supplierId;

    protected ?float $outstandingAmount;

    protected ?float $receiptCount;

    protected ?string $taxCategories;

    protected ?string $tags;

    protected ?string $financeApiId;

    /** @var array<int|string, mixed>|null */
    protected ?array $receipts;

    protected ?ExpenseBookings $bookings;

    protected ?ExpensePositions $positions;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function getDate(): ?string {
        return $this->date ?? null;
    }

    public function setDate(?string $date): void {
        $this->date = $date;
    }

    public function getPayDate(): ?string {
        return $this->payDate ?? null;
    }

    public function setPayDate(?string $payDate): void {
        $this->payDate = $payDate;
    }

    public function getPayKind(): ?PayKind {
        return $this->payKind ?? null;
    }

    public function setPayKind(?PayKind $payKind): void {
        $this->payKind = $payKind;
    }

    public function getPayee(): ?string {
        return $this->payee ?? null;
    }

    public function setPayee(?string $payee): void {
        $this->payee = $payee;
    }

    public function getDescription(): ?string {
        return $this->description ?? null;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function getPrice(): ?float {
        return $this->price ?? null;
    }

    public function getPriceTotal(): ?float {
        return $this->priceTotal ?? null;
    }

    public function setPriceTotal(?float $priceTotal): void {
        $this->priceTotal = $priceTotal;
    }

    public function getVat(): ?float {
        return $this->vat ?? null;
    }

    public function getVatPercent(): ?float {
        return $this->vatPercent ?? null;
    }

    public function setVatPercent(?float $vatPercent): void {
        $this->vatPercent = $vatPercent;
    }

    public function getVatAmount(): ?float {
        return $this->vatAmount ?? null;
    }

    public function getReceiptNumber(): ?string {
        return $this->receiptNumber ?? null;
    }

    public function setReceiptNumber(?string $receiptNumber): void {
        $this->receiptNumber = $receiptNumber;
    }

    public function getSupplierId(): ?string {
        return $this->supplierId ?? null;
    }

    public function setSupplierId(?string $supplierId): void {
        $this->supplierId = $supplierId;
    }

    public function getOutstandingAmount(): ?float {
        return $this->outstandingAmount ?? null;
    }

    public function getReceiptCount(): ?float {
        return $this->receiptCount ?? null;
    }

    public function getTaxCategories(): ?string {
        return $this->taxCategories ?? null;
    }

    public function getTags(): ?string {
        return $this->tags ?? null;
    }

    public function setTags(?string $tags): void {
        $this->tags = $tags;
    }

    public function getFinanceApiId(): ?string {
        return $this->financeApiId ?? null;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getReceipts(): ?array {
        return $this->receipts ?? null;
    }

    /**
     * @param array<int|string, mixed>|null $receipts
     */
    public function setReceipts(?array $receipts): void {
        $this->receipts = $receipts;
    }

    public function getBookings(): ?ExpenseBookings {
        return $this->bookings ?? null;
    }

    public function getPositions(): ?ExpensePositions {
        return $this->positions ?? null;
    }

    public function setPositions(?ExpensePositions $positions): void {
        $this->positions = $positions;
    }

    /*
     * Betragsfelder der API sind JSON-Zahlen; für exakte Rechnungen liefern
     * die folgenden Accessoren sie als Money in der Belegwährung.
     */
    public function getPriceAsMoney(): ?Money {
        return $this->toMoney($this->price ?? null);
    }

    public function getPriceTotalAsMoney(): ?Money {
        return $this->toMoney($this->priceTotal ?? null);
    }

    public function getVatAsMoney(): ?Money {
        return $this->toMoney($this->vat ?? null);
    }

    public function getVatAmountAsMoney(): ?Money {
        return $this->toMoney($this->vatAmount ?? null);
    }

    public function getOutstandingAmountAsMoney(): ?Money {
        return $this->toMoney($this->outstandingAmount ?? null);
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

    public function getPayDateAsDateTime(): ?DateTimeImmutable {
        return self::toDateTime($this->payDate ?? null);
    }
}
