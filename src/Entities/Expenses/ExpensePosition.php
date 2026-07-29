<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ExpensePosition.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Expenses;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use APIToolkit\Traits\MoneyAccessorTrait;
use CommonToolkit\ValueObjects\Money;
use Psr\Log\LoggerInterface;

/**
 * Position einer Ausgabe (positions-Feld) mit Buchhaltungskonto und Beträgen.
 */
class ExpensePosition extends NamedEntity {
    use MoneyAccessorTrait;

    protected ?int $id;

    protected ?float $bookkeepingAccountNo;

    protected ?string $bookkeepingAccountDescriptionShort;

    protected ?string $bookkeepingAccountDescriptionCustom;

    protected ?string $expenseDescription;

    protected ?string $bookkeepingAccountId;

    protected ?float $vat;

    protected ?float $vatPercent;

    protected ?float $totalNet;

    protected ?float $totalGross;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function getBookkeepingAccountNo(): ?float {
        return $this->bookkeepingAccountNo ?? null;
    }

    public function getBookkeepingAccountDescriptionShort(): ?string {
        return $this->bookkeepingAccountDescriptionShort ?? null;
    }

    public function getBookkeepingAccountDescriptionCustom(): ?string {
        return $this->bookkeepingAccountDescriptionCustom ?? null;
    }

    public function getExpenseDescription(): ?string {
        return $this->expenseDescription ?? null;
    }

    public function setExpenseDescription(?string $expenseDescription): void {
        $this->expenseDescription = $expenseDescription;
    }

    public function getBookkeepingAccountId(): ?string {
        return $this->bookkeepingAccountId ?? null;
    }

    public function setBookkeepingAccountId(?string $bookkeepingAccountId): void {
        $this->bookkeepingAccountId = $bookkeepingAccountId;
    }

    public function getVat(): ?float {
        return $this->vat ?? null;
    }

    public function setVat(?float $vat): void {
        $this->vat = $vat;
    }

    public function getVatPercent(): ?float {
        return $this->vatPercent ?? null;
    }

    public function setVatPercent(?float $vatPercent): void {
        $this->vatPercent = $vatPercent;
    }

    public function getTotalNet(): ?float {
        return $this->totalNet ?? null;
    }

    public function setTotalNet(?float $totalNet): void {
        $this->totalNet = $totalNet;
    }

    public function getTotalGross(): ?float {
        return $this->totalGross ?? null;
    }

    public function setTotalGross(?float $totalGross): void {
        $this->totalGross = $totalGross;
    }

    /*
     * Betragsfelder der API sind JSON-Zahlen; für exakte Rechnungen liefern
     * die folgenden Accessoren sie als Money in der Belegwährung.
     */
    public function getVatAsMoney(): ?Money {
        return $this->toMoney($this->vat ?? null);
    }

    public function getTotalNetAsMoney(): ?Money {
        return $this->toMoney($this->totalNet ?? null);
    }

    public function getTotalGrossAsMoney(): ?Money {
        return $this->toMoney($this->totalGross ?? null);
    }
}
