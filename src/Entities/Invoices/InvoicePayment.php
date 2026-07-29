<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : InvoicePayment.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Invoices;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use APIToolkit\Traits\MoneyAccessorTrait;
use CommonToolkit\ValueObjects\Money;
use DateTimeImmutable;
use Orgamax\Enums\PaymentType;
use Psr\Log\LoggerInterface;

/**
 * Zahlung zu einer Rechnung. Ist amount nicht der volle Rechnungsbetrag, muss
 * type partial/discount/bankcharge/surcharge sein, sonst antwortet die API
 * mit einem Typ-Fehler.
 */
class InvoicePayment extends NamedEntity {
    use MoneyAccessorTrait;

    protected ?int $id;

    protected ?float $amount;

    protected ?PaymentType $type;

    protected ?string $date;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function isValid(): bool {
        return parent::isValid() && isset($this->amount) && isset($this->type) && isset($this->date);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function getAmount(): ?float {
        return $this->amount ?? null;
    }

    public function setAmount(?float $amount): void {
        $this->amount = $amount;
    }

    public function getType(): ?PaymentType {
        return $this->type ?? null;
    }

    public function setType(?PaymentType $type): void {
        $this->type = $type;
    }

    public function getDate(): ?string {
        return $this->date ?? null;
    }

    /**
     * Zahlungsdatum im Format Y-m-d.
     */
    public function setDate(?string $date): void {
        $this->date = $date;
    }

    /*
     * Betragsfelder der API sind JSON-Zahlen; für exakte Rechnungen liefern
     * die folgenden Accessoren sie als Money in der Belegwährung.
     */
    public function getAmountAsMoney(): ?Money {
        return $this->toMoney($this->amount ?? null);
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
