<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : DeliveryNote.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\DeliveryNotes;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use DateTime;
use DateTimeImmutable;
use Orgamax\Entities\Common\{CustomerData, Positions};
use Orgamax\Entities\Settings\DeliveryCondition;
use Orgamax\Enums\DeliveryNoteState;
use Psr\Log\LoggerInterface;

/**
 * Lieferschein. Vereinigung der Listen- und Detail-Felder der API
 * (DeliveryNoteListData + DeliveryNoteDataObject).
 */
class DeliveryNote extends NamedEntity {
    protected ?int $id;

    protected ?string $number;

    protected ?string $date;

    protected ?DeliveryNoteState $state;

    protected ?int $customerId;

    protected ?int $orderId;

    protected ?string $orderNumber;

    protected ?string $deliveryConditionId;

    protected ?string $letterNumerationId;

    protected ?string $letterPaperSettingId;

    protected ?DateTime $sentAt;

    protected ?string $notes;

    protected ?CustomerData $customerData;

    protected ?DeliveryCondition $deliveryConditionData;

    protected ?Positions $positions;

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

    public function getState(): ?DeliveryNoteState {
        return $this->state ?? null;
    }

    public function getCustomerId(): ?int {
        return $this->customerId ?? null;
    }

    public function getOrderId(): ?int {
        return $this->orderId ?? null;
    }

    public function getOrderNumber(): ?string {
        return $this->orderNumber ?? null;
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

    public function getCustomerData(): ?CustomerData {
        return $this->customerData ?? null;
    }

    public function getDeliveryConditionData(): ?DeliveryCondition {
        return $this->deliveryConditionData ?? null;
    }

    public function getPositions(): ?Positions {
        return $this->positions ?? null;
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
