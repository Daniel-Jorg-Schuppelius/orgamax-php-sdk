<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : FileData.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Files;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use APIToolkit\Traits\MoneyAccessorTrait;
use CommonToolkit\ValueObjects\Money;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;

/**
 * Ergebnis der Belegerkennung (FileDataObject) — Antwort von Upload,
 * Analyze und Meta-Update.
 */
class FileData extends NamedEntity {
    use MoneyAccessorTrait;

    protected ?string $giniDocId;

    protected ?string $receiptNumber;

    protected ?string $date;

    protected ?float $priceTotal;

    protected ?int $vatRate;

    protected ?string $payee;

    protected ?string $payeeStreet;

    protected ?string $payeeCity;

    protected ?string $payeeZipCode;

    protected ?string $payeeIban;

    protected ?string $payeeBic;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getGiniDocId(): ?string {
        return $this->giniDocId ?? null;
    }

    public function getReceiptNumber(): ?string {
        return $this->receiptNumber ?? null;
    }

    public function getDate(): ?string {
        return $this->date ?? null;
    }

    public function getPriceTotal(): ?float {
        return $this->priceTotal ?? null;
    }

    public function getVatRate(): ?int {
        return $this->vatRate ?? null;
    }

    public function getPayee(): ?string {
        return $this->payee ?? null;
    }

    public function getPayeeStreet(): ?string {
        return $this->payeeStreet ?? null;
    }

    public function getPayeeCity(): ?string {
        return $this->payeeCity ?? null;
    }

    public function getPayeeZipCode(): ?string {
        return $this->payeeZipCode ?? null;
    }

    public function getPayeeIban(): ?string {
        return $this->payeeIban ?? null;
    }

    public function getPayeeBic(): ?string {
        return $this->payeeBic ?? null;
    }

    /*
     * Betragsfelder der API sind JSON-Zahlen; für exakte Rechnungen liefern
     * die folgenden Accessoren sie als Money in der Belegwährung.
     */
    public function getPriceTotalAsMoney(): ?Money {
        return $this->toMoney($this->priceTotal ?? null);
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
