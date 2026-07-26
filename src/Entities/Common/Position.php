<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : Position.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Common;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Belegposition, wie sie in Invoice, Offer, Order und DeliveryNote verwendet
 * wird (Vereinigung der Positions-Varianten der API).
 */
class Position extends NamedEntity {
    protected ?string $id;

    protected ?string $title;

    protected ?string $description;

    protected ?bool $showDescription;

    protected ?float $amount;

    protected ?string $unit;

    protected ?float $vat;

    protected ?float $vatPercent;

    protected ?float $priceGross;

    protected ?float $priceGrossAfterDiscount;

    protected ?float $priceNet;

    protected ?float $priceNetAfterDiscount;

    protected ?float $totalNet;

    protected ?float $totalGross;

    protected ?float $totalGrossAfterDiscount;

    protected ?PositionMetaData $metaData;

    protected ?string $weight;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?string {
        return $this->id ?? null;
    }

    public function getTitle(): ?string {
        return $this->title ?? null;
    }

    public function setTitle(?string $title): void {
        $this->title = $title;
    }

    public function getDescription(): ?string {
        return $this->description ?? null;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function getShowDescription(): ?bool {
        return $this->showDescription ?? null;
    }

    public function setShowDescription(?bool $showDescription): void {
        $this->showDescription = $showDescription;
    }

    public function getAmount(): ?float {
        return $this->amount ?? null;
    }

    public function setAmount(?float $amount): void {
        $this->amount = $amount;
    }

    public function getUnit(): ?string {
        return $this->unit ?? null;
    }

    public function setUnit(?string $unit): void {
        $this->unit = $unit;
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

    public function getPriceGross(): ?float {
        return $this->priceGross ?? null;
    }

    public function setPriceGross(?float $priceGross): void {
        $this->priceGross = $priceGross;
    }

    public function getPriceGrossAfterDiscount(): ?float {
        return $this->priceGrossAfterDiscount ?? null;
    }

    public function getPriceNet(): ?float {
        return $this->priceNet ?? null;
    }

    public function setPriceNet(?float $priceNet): void {
        $this->priceNet = $priceNet;
    }

    public function getPriceNetAfterDiscount(): ?float {
        return $this->priceNetAfterDiscount ?? null;
    }

    public function getTotalNet(): ?float {
        return $this->totalNet ?? null;
    }

    public function getTotalGross(): ?float {
        return $this->totalGross ?? null;
    }

    public function getTotalGrossAfterDiscount(): ?float {
        return $this->totalGrossAfterDiscount ?? null;
    }

    public function getMetaData(): ?PositionMetaData {
        return $this->metaData ?? null;
    }

    public function getWeight(): ?string {
        return $this->weight ?? null;
    }
}
