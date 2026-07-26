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
use DateTime;
use Orgamax\Entities\Common\{CustomerData, Positions};
use Orgamax\Entities\Settings\PayCondition;
use Orgamax\Enums\{PriceKind, SalesDocumentState};
use Psr\Log\LoggerInterface;

/**
 * Angebot. Vereinigung der Listen- (OfferListData) und Detail-Felder
 * (OfferDataObject) der API; die Ressource ist rein lesend.
 */
class Offer extends NamedEntity {
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
}
