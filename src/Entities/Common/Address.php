<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : Address.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Common;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Adresse, wie sie bei Kunden, Lieferanten und in den Account-Einstellungen
 * verwendet wird (Vereinigung der Adress-Varianten der API; nicht belegte
 * Felder werden bei der Serialisierung weggelassen).
 */
class Address extends NamedEntity {
    protected ?string $id;

    protected ?string $city;

    protected ?string $companyName;

    protected ?string $companyNameAffix;

    protected ?string $contactPersonId;

    protected ?string $country;

    protected ?string $countryIso;

    protected ?string $isoCountry;

    protected ?string $firstName;

    protected ?string $lastName;

    protected ?string $kind;

    protected ?string $name;

    protected ?string $salutation;

    protected ?string $street;

    protected ?string $title;

    protected ?string $type;

    protected ?string $zipCode;

    protected ?bool $isDefault;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?string {
        return $this->id ?? null;
    }

    public function getCity(): ?string {
        return $this->city ?? null;
    }

    public function setCity(?string $city): void {
        $this->city = $city;
    }

    public function getCompanyName(): ?string {
        return $this->companyName ?? null;
    }

    public function setCompanyName(?string $companyName): void {
        $this->companyName = $companyName;
    }

    public function getCompanyNameAffix(): ?string {
        return $this->companyNameAffix ?? null;
    }

    public function setCompanyNameAffix(?string $companyNameAffix): void {
        $this->companyNameAffix = $companyNameAffix;
    }

    public function getContactPersonId(): ?string {
        return $this->contactPersonId ?? null;
    }

    public function getCountry(): ?string {
        return $this->country ?? null;
    }

    public function getCountryIso(): ?string {
        return $this->countryIso ?? null;
    }

    public function setCountryIso(?string $countryIso): void {
        $this->countryIso = $countryIso;
    }

    public function getIsoCountry(): ?string {
        return $this->isoCountry ?? null;
    }

    public function getFirstName(): ?string {
        return $this->firstName ?? null;
    }

    public function setFirstName(?string $firstName): void {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string {
        return $this->lastName ?? null;
    }

    public function setLastName(?string $lastName): void {
        $this->lastName = $lastName;
    }

    public function getKind(): ?string {
        return $this->kind ?? null;
    }

    public function setKind(?string $kind): void {
        $this->kind = $kind;
    }

    public function getName(): ?string {
        return $this->name ?? null;
    }

    public function getSalutation(): ?string {
        return $this->salutation ?? null;
    }

    public function setSalutation(?string $salutation): void {
        $this->salutation = $salutation;
    }

    public function getStreet(): ?string {
        return $this->street ?? null;
    }

    public function setStreet(?string $street): void {
        $this->street = $street;
    }

    public function getTitle(): ?string {
        return $this->title ?? null;
    }

    public function setTitle(?string $title): void {
        $this->title = $title;
    }

    public function getType(): ?string {
        return $this->type ?? null;
    }

    public function getZipCode(): ?string {
        return $this->zipCode ?? null;
    }

    public function setZipCode(?string $zipCode): void {
        $this->zipCode = $zipCode;
    }

    public function isDefault(): bool {
        return $this->isDefault ?? false;
    }
}
