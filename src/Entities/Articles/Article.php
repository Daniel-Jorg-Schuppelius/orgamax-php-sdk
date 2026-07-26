<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : Article.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Articles;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use DateTime;
use Orgamax\Enums\CalculationBase;
use Psr\Log\LoggerInterface;

/**
 * Artikel, wie er in Rechnungen, Angeboten, Lieferscheinen oder eigenen Apps
 * verwendet wird. Vereinigung der Listen- und Detail-Felder der API;
 * title, unit und number sind beim Anlegen Pflicht.
 */
class Article extends NamedEntity {
    protected ?int $id;

    protected ?string $number;

    protected ?string $title;

    protected ?string $unit;

    protected ?CalculationBase $calculationBase;

    protected ?string $description;

    protected ?string $category;

    protected ?string $notes;

    protected ?bool $notesAlert;

    protected ?float $price;

    protected ?float $priceGross;

    protected ?float $vatPercent;

    protected ?float $purchasePrice;

    protected ?float $differentialTaxPrice;

    protected ?float $weight;

    protected ?float $minimumStockAmount;

    protected ?float $availableStockAmount;

    protected ?float $totalSales;

    protected ?DateTime $createdAt;

    protected ?DateTime $updatedAt;

    protected ?bool $isStockManaged;

    protected ?bool $isPosArticle;

    protected ?bool $isDifferentialTax;

    protected ?bool $isGraduatedPriceManaged;

    protected ?string $bookkeepingAccountId;

    /** @var array<int|string, mixed>|null */
    protected ?array $tags;

    /** @var array<int|string, mixed>|null */
    protected ?array $customValues;

    /** @var array<int|string, mixed>|null */
    protected ?array $differentialTaxTexts;

    /** @var array<int|string, mixed>|null */
    protected ?array $ecommerceReferences;

    protected ?ArticleStocks $stock;

    protected ?GraduatedPrices $graduatedPriceList;

    protected ?ArticleImages $images;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function isValid(): bool {
        return parent::isValid() && isset($this->title) && isset($this->unit) && isset($this->number);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function getNumber(): ?string {
        return $this->number ?? null;
    }

    public function setNumber(?string $number): void {
        $this->number = $number;
    }

    public function getTitle(): ?string {
        return $this->title ?? null;
    }

    public function setTitle(?string $title): void {
        $this->title = $title;
    }

    public function getUnit(): ?string {
        return $this->unit ?? null;
    }

    public function setUnit(?string $unit): void {
        $this->unit = $unit;
    }

    public function getCalculationBase(): ?CalculationBase {
        return $this->calculationBase ?? null;
    }

    public function setCalculationBase(?CalculationBase $calculationBase): void {
        $this->calculationBase = $calculationBase;
    }

    public function getDescription(): ?string {
        return $this->description ?? null;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function getCategory(): ?string {
        return $this->category ?? null;
    }

    public function setCategory(?string $category): void {
        $this->category = $category;
    }

    public function getNotes(): ?string {
        return $this->notes ?? null;
    }

    public function setNotes(?string $notes): void {
        $this->notes = $notes;
    }

    public function getNotesAlert(): ?bool {
        return $this->notesAlert ?? null;
    }

    public function setNotesAlert(?bool $notesAlert): void {
        $this->notesAlert = $notesAlert;
    }

    public function getPrice(): ?float {
        return $this->price ?? null;
    }

    public function setPrice(?float $price): void {
        $this->price = $price;
    }

    public function getPriceGross(): ?float {
        return $this->priceGross ?? null;
    }

    public function setPriceGross(?float $priceGross): void {
        $this->priceGross = $priceGross;
    }

    public function getVatPercent(): ?float {
        return $this->vatPercent ?? null;
    }

    public function setVatPercent(?float $vatPercent): void {
        $this->vatPercent = $vatPercent;
    }

    public function getPurchasePrice(): ?float {
        return $this->purchasePrice ?? null;
    }

    public function setPurchasePrice(?float $purchasePrice): void {
        $this->purchasePrice = $purchasePrice;
    }

    public function getDifferentialTaxPrice(): ?float {
        return $this->differentialTaxPrice ?? null;
    }

    public function getWeight(): ?float {
        return $this->weight ?? null;
    }

    public function setWeight(?float $weight): void {
        $this->weight = $weight;
    }

    public function getMinimumStockAmount(): ?float {
        return $this->minimumStockAmount ?? null;
    }

    public function getAvailableStockAmount(): ?float {
        return $this->availableStockAmount ?? null;
    }

    public function getTotalSales(): ?float {
        return $this->totalSales ?? null;
    }

    public function getCreatedAt(): ?DateTime {
        return $this->createdAt ?? null;
    }

    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt ?? null;
    }

    public function isStockManaged(): bool {
        return $this->isStockManaged ?? false;
    }

    public function isPosArticle(): bool {
        return $this->isPosArticle ?? false;
    }

    public function isDifferentialTax(): bool {
        return $this->isDifferentialTax ?? false;
    }

    public function isGraduatedPriceManaged(): bool {
        return $this->isGraduatedPriceManaged ?? false;
    }

    public function getBookkeepingAccountId(): ?string {
        return $this->bookkeepingAccountId ?? null;
    }

    public function setBookkeepingAccountId(?string $bookkeepingAccountId): void {
        $this->bookkeepingAccountId = $bookkeepingAccountId;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getTags(): ?array {
        return $this->tags ?? null;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getCustomValues(): ?array {
        return $this->customValues ?? null;
    }

    public function getStock(): ?ArticleStocks {
        return $this->stock ?? null;
    }

    public function getGraduatedPriceList(): ?GraduatedPrices {
        return $this->graduatedPriceList ?? null;
    }

    public function setGraduatedPriceList(?GraduatedPrices $graduatedPriceList): void {
        $this->graduatedPriceList = $graduatedPriceList;
    }

    public function getImages(): ?ArticleImages {
        return $this->images ?? null;
    }
}
