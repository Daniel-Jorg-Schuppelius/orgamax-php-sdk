<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : Customer.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Customers;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Orgamax\Entities\Common\{Address, Addresses};
use Orgamax\Enums\CustomerKind;
use Psr\Log\LoggerInterface;

/**
 * Kunde. Vereinigung der Listen-, Detail- und Antwort-Felder der API.
 * Beim Anlegen/Ändern werden kind, Name bzw. Firmenname über die
 * billingAddress der customerDefaultAddress übertragen.
 */
class Customer extends NamedEntity {
    protected ?string $id;

    protected ?string $name;

    protected ?string $number;

    protected ?string $category;

    protected ?CustomerKind $kind;

    protected ?string $salutation;

    protected ?string $title;

    protected ?string $firstName;

    protected ?string $lastName;

    protected ?string $companyName;

    protected ?string $companyNameAffix;

    protected ?string $email;

    protected ?string $fax;

    protected ?string $mobile;

    protected ?string $phone1;

    protected ?string $phone2;

    protected ?string $website;

    protected ?string $countryIso;

    protected ?string $vatNumber;

    protected ?float $discount;

    protected ?float $accountNo;

    protected ?bool $netPriceAsDefault;

    protected ?string $notes;

    protected ?bool $notesAlert;

    protected ?float $totalSales;

    protected ?float $balance;

    protected ?string $deliveryConditionId;

    protected ?string $payConditionId;

    protected ?string $timeTrackingHourlyRate;

    protected ?string $hourlyRateId;

    protected ?string $bankAccounts;

    /** @var array<int|string, mixed>|null */
    protected ?array $mainContact;

    /** @var array<int|string, mixed>|null */
    protected ?array $externalReferences;

    /** @var array<int|string, mixed>|null */
    protected ?array $tags;

    /** @var array<int|string, mixed>|null */
    protected ?array $customValues;

    protected ?Address $address;

    protected ?CustomerDefaultAddress $customerDefaultAddress;

    protected ?Addresses $addresses;

    protected ?ContactPersons $contactPersons;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?string {
        return $this->id ?? null;
    }

    public function getName(): ?string {
        return $this->name ?? null;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }

    public function getNumber(): ?string {
        return $this->number ?? null;
    }

    public function setNumber(?string $number): void {
        $this->number = $number;
    }

    public function getCategory(): ?string {
        return $this->category ?? null;
    }

    public function setCategory(?string $category): void {
        $this->category = $category;
    }

    public function getKind(): ?CustomerKind {
        return $this->kind ?? null;
    }

    public function setKind(?CustomerKind $kind): void {
        $this->kind = $kind;
    }

    public function getSalutation(): ?string {
        return $this->salutation ?? null;
    }

    public function getTitle(): ?string {
        return $this->title ?? null;
    }

    public function getFirstName(): ?string {
        return $this->firstName ?? null;
    }

    public function getLastName(): ?string {
        return $this->lastName ?? null;
    }

    public function getCompanyName(): ?string {
        return $this->companyName ?? null;
    }

    public function getCompanyNameAffix(): ?string {
        return $this->companyNameAffix ?? null;
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

    public function getCountryIso(): ?string {
        return $this->countryIso ?? null;
    }

    public function getVatNumber(): ?string {
        return $this->vatNumber ?? null;
    }

    public function setVatNumber(?string $vatNumber): void {
        $this->vatNumber = $vatNumber;
    }

    public function getDiscount(): ?float {
        return $this->discount ?? null;
    }

    public function setDiscount(?float $discount): void {
        $this->discount = $discount;
    }

    public function getAccountNo(): ?float {
        return $this->accountNo ?? null;
    }

    public function setAccountNo(?float $accountNo): void {
        $this->accountNo = $accountNo;
    }

    public function getNetPriceAsDefault(): ?bool {
        return $this->netPriceAsDefault ?? null;
    }

    public function setNetPriceAsDefault(?bool $netPriceAsDefault): void {
        $this->netPriceAsDefault = $netPriceAsDefault;
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

    public function getTotalSales(): ?float {
        return $this->totalSales ?? null;
    }

    public function getBalance(): ?float {
        return $this->balance ?? null;
    }

    public function getDeliveryConditionId(): ?string {
        return $this->deliveryConditionId ?? null;
    }

    public function setDeliveryConditionId(?string $deliveryConditionId): void {
        $this->deliveryConditionId = $deliveryConditionId;
    }

    public function getPayConditionId(): ?string {
        return $this->payConditionId ?? null;
    }

    public function setPayConditionId(?string $payConditionId): void {
        $this->payConditionId = $payConditionId;
    }

    public function getTimeTrackingHourlyRate(): ?string {
        return $this->timeTrackingHourlyRate ?? null;
    }

    public function getHourlyRateId(): ?string {
        return $this->hourlyRateId ?? null;
    }

    public function getBankAccounts(): ?string {
        return $this->bankAccounts ?? null;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getMainContact(): ?array {
        return $this->mainContact ?? null;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getExternalReferences(): ?array {
        return $this->externalReferences ?? null;
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

    /**
     * @return array<int|string, mixed>|null
     */
    public function getCustomValues(): ?array {
        return $this->customValues ?? null;
    }

    public function getAddress(): ?Address {
        return $this->address ?? null;
    }

    public function getCustomerDefaultAddress(): ?CustomerDefaultAddress {
        return $this->customerDefaultAddress ?? null;
    }

    public function setCustomerDefaultAddress(?CustomerDefaultAddress $customerDefaultAddress): void {
        $this->customerDefaultAddress = $customerDefaultAddress;
    }

    public function getAddresses(): ?Addresses {
        return $this->addresses ?? null;
    }

    public function setAddresses(?Addresses $addresses): void {
        $this->addresses = $addresses;
    }

    public function getContactPersons(): ?ContactPersons {
        return $this->contactPersons ?? null;
    }

    public function setContactPersons(?ContactPersons $contactPersons): void {
        $this->contactPersons = $contactPersons;
    }
}
