<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : PayCondition.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Settings;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Zahlungsbedingung. In offerText/invoiceText kann {{dueDays}} als
 * Platzhalter für die Fälligkeitstage verwendet werden.
 */
class PayCondition extends NamedEntity {
    protected ?int $id;

    protected ?string $name;

    protected ?bool $isBasic;

    protected ?string $offerText;

    protected ?string $invoiceText;

    protected ?int $dueDays;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function isValid(): bool {
        return parent::isValid() && isset($this->name);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function getName(): ?string {
        return $this->name ?? null;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }

    public function isBasic(): bool {
        return $this->isBasic ?? false;
    }

    public function getOfferText(): ?string {
        return $this->offerText ?? null;
    }

    public function setOfferText(?string $offerText): void {
        $this->offerText = $offerText;
    }

    public function getInvoiceText(): ?string {
        return $this->invoiceText ?? null;
    }

    public function setInvoiceText(?string $invoiceText): void {
        $this->invoiceText = $invoiceText;
    }

    public function getDueDays(): ?int {
        return $this->dueDays ?? null;
    }

    public function setDueDays(?int $dueDays): void {
        $this->dueDays = $dueDays;
    }
}
