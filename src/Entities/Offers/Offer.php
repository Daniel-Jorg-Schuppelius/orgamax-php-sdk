<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : Offer.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Offers;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use APIToolkit\Traits\MoneyAccessorTrait;
use CommonToolkit\ValueObjects\Money;
use DateTime;
use DateTimeImmutable;
use Orgamax\Entities\Common\{CustomerData, Positions};
use Orgamax\Entities\Settings\PayCondition;
use Orgamax\Enums\{PriceKind, SalesDocumentState};
use Psr\Log\LoggerInterface;

/**
 * Angebot. Vereinigung der Listen- (OfferListData) und Detail-Felder
 * (OfferDataObject) der API; die Ressource ist rein lesend.
 */
class Offer extends NamedEntity {
    use MoneyAccessorTrait;

    protected ?int $id;

    protected ?string $number;

    protected ?string $date;

    protected ?int $customerId;

    protected ?PriceKind $priceKind;

    protected ?SalesDocumentState $state;

    protected ?float $totalNet;

    protected ?float $totalGross;

    protected ?float $outstandingAmount;

    protected ?float $cashDiscountTotal;

    protected ?string $deliveryConditionId;

    protected ?string $letterNumerationId;

    protected ?string $letterPaperSettingId;

    protected ?DateTime $sentAt;

    protected ?string $notes;

    protected ?bool $isLocked;

    protected ?bool $smallBusiness;

    protected ?string $invoiceId;

    protected ?int $payConditionId;

    protected ?PayCondition $payConditionData;

    protected ?Positions $positions;

    protected ?CustomerData $customerData;

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

    public function getCustomerId(): ?int {
        return $this->customerId ?? null;
    }

    public function getPriceKind(): ?PriceKind {
        return $this->priceKind ?? null;
    }

    public function getState(): ?SalesDocumentState {
        return $this->state ?? null;
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

    public function getDeliveryConditionId(): ?string {
        return $this->deliveryConditionId ?? null;
    }

    public function getLetterNumerationId(): ?string {
        return $this->letterNumerationId ?? null;
    }

    public function getLetterPaperSettingId(): ?string {
        return $this->letterPaperSettingId ?? null;
    }

    public function getSentAt(): ?DateTime {
        return $this->sentAt ?? null;
    }

    public function getNotes(): ?string {
        return $this->notes ?? null;
    }

    public function isLocked(): bool {
        return $this->isLocked ?? false;
    }

    public function isSmallBusiness(): bool {
        return $this->smallBusiness ?? false;
    }

    public function getInvoiceId(): ?string {
        return $this->invoiceId ?? null;
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

    public function getCustomerData(): ?CustomerData {
        return $this->customerData ?? null;
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
}
