<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : Supplier.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Suppliers;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use DateTime;
use Orgamax\Entities\Common\{Address, Addresses};
use Psr\Log\LoggerInterface;

/**
 * Lieferant. Vereinigung der Felder von SupplierData (Detail/Create/Update)
 * und SupplierDataList (Listen-Variante: address, countryIso, mainContact).
 */
class Supplier extends NamedEntity {
    protected ?int $id;

    protected ?string $name;

    protected ?int $number;

    protected ?float $discount;

    protected ?string $email;

    protected ?string $fax;

    protected ?string $mobile;

    protected ?string $phone1;

    protected ?string $phone2;

    protected ?string $website;

    protected ?string $notes;

    protected ?bool $notesAlert;

    protected ?string $customerReference;

    protected ?string $countryIso;

    protected ?Addresses $addresses;

    protected ?Address $address;

    /** @var array<int|string, mixed>|null */
    protected ?array $mainContact;

    protected ?string $bankAccounts;

    /** @var array<int|string, mixed>|null */
    protected ?array $contactPersons;

    protected ?DateTime $deletedAt;

    /** @var array<int|string, mixed>|null */
    protected ?array $aliases;

    /** @var array<int|string, mixed>|null */
    protected ?array $tags;

    protected ?string $defaultBookkeepingAccountId;

    protected ?int $accountNo;

    protected ?int $payKindId;

    protected ?CashDiscountSetting $cashDiscountSetting;

    protected ?bool $discountEnabled;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
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

    public function getNumber(): ?int {
        return $this->number ?? null;
    }

    public function setNumber(?int $number): void {
        $this->number = $number;
    }

    public function getDiscount(): ?float {
        return $this->discount ?? null;
    }

    public function setDiscount(?float $discount): void {
        $this->discount = $discount;
    }

    public function getEmail(): ?string {
        return $this->email ?? null;
    }

    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    public function getFax(): ?string {
        return $this->fax ?? null;
    }

    public function setFax(?string $fax): void {
        $this->fax = $fax;
    }

    public function getMobile(): ?string {
        return $this->mobile ?? null;
    }

    public function setMobile(?string $mobile): void {
        $this->mobile = $mobile;
    }

    public function getPhone1(): ?string {
        return $this->phone1 ?? null;
    }

    public function setPhone1(?string $phone1): void {
        $this->phone1 = $phone1;
    }

    public function getPhone2(): ?string {
        return $this->phone2 ?? null;
    }

    public function setPhone2(?string $phone2): void {
        $this->phone2 = $phone2;
    }

    public function getWebsite(): ?string {
        return $this->website ?? null;
    }

    public function setWebsite(?string $website): void {
        $this->website = $website;
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

    public function getCustomerReference(): ?string {
        return $this->customerReference ?? null;
    }

    public function setCustomerReference(?string $customerReference): void {
        $this->customerReference = $customerReference;
    }

    public function getCountryIso(): ?string {
        return $this->countryIso ?? null;
    }

    public function getAddresses(): ?Addresses {
        return $this->addresses ?? null;
    }

    public function setAddresses(?Addresses $addresses): void {
        $this->addresses = $addresses;
    }

    public function getAddress(): ?Address {
        return $this->address ?? null;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getMainContact(): ?array {
        return $this->mainContact ?? null;
    }

    public function getBankAccounts(): ?string {
        return $this->bankAccounts ?? null;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getContactPersons(): ?array {
        return $this->contactPersons ?? null;
    }

    /**
     * @param array<int|string, mixed>|null $contactPersons
     */
    public function setContactPersons(?array $contactPersons): void {
        $this->contactPersons = $contactPersons;
    }

    public function getDeletedAt(): ?DateTime {
        return $this->deletedAt ?? null;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getAliases(): ?array {
        return $this->aliases ?? null;
    }

    /**
     * @param array<int|string, mixed>|null $aliases
     */
    public function setAliases(?array $aliases): void {
        $this->aliases = $aliases;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getTags(): ?array {
        return $this->tags ?? null;
    }

    /**
     * @param array<int|string, mixed>|null $tags
     */
    public function setTags(?array $tags): void {
        $this->tags = $tags;
    }

    public function getDefaultBookkeepingAccountId(): ?string {
        return $this->defaultBookkeepingAccountId ?? null;
    }

    public function setDefaultBookkeepingAccountId(?string $defaultBookkeepingAccountId): void {
        $this->defaultBookkeepingAccountId = $defaultBookkeepingAccountId;
    }

    public function getAccountNo(): ?int {
        return $this->accountNo ?? null;
    }

    public function setAccountNo(?int $accountNo): void {
        $this->accountNo = $accountNo;
    }

    public function getPayKindId(): ?int {
        return $this->payKindId ?? null;
    }

    public function setPayKindId(?int $payKindId): void {
        $this->payKindId = $payKindId;
    }

    public function getCashDiscountSetting(): ?CashDiscountSetting {
        return $this->cashDiscountSetting ?? null;
    }

    public function setCashDiscountSetting(?CashDiscountSetting $cashDiscountSetting): void {
        $this->cashDiscountSetting = $cashDiscountSetting;
    }

    public function getDiscountEnabled(): ?bool {
        return $this->discountEnabled ?? null;
    }

    public function setDiscountEnabled(?bool $discountEnabled): void {
        $this->discountEnabled = $discountEnabled;
    }
}
