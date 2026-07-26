<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ContactPerson.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Customers;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Ansprechpartner eines Kunden. Nur ein Ansprechpartner darf isMainContact
 * sein; birthday wird als Datum im Format Y-m-d übertragen.
 */
class ContactPerson extends NamedEntity {
    protected ?int $id;

    protected ?string $salutation;

    protected ?string $title;

    protected ?string $firstName;

    protected ?string $lastName;

    protected ?string $birthday;

    protected ?string $email;

    protected ?string $job;

    protected ?string $mobile;

    protected ?string $phone1;

    protected ?string $phone2;

    protected ?string $fax;

    protected ?bool $isMainContact;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function getSalutation(): ?string {
        return $this->salutation ?? null;
    }

    public function setSalutation(?string $salutation): void {
        $this->salutation = $salutation;
    }

    public function getTitle(): ?string {
        return $this->title ?? null;
    }

    public function setTitle(?string $title): void {
        $this->title = $title;
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

    public function getBirthday(): ?string {
        return $this->birthday ?? null;
    }

    public function setBirthday(?string $birthday): void {
        $this->birthday = $birthday;
    }

    public function getEmail(): ?string {
        return $this->email ?? null;
    }

    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    public function getJob(): ?string {
        return $this->job ?? null;
    }

    public function setJob(?string $job): void {
        $this->job = $job;
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

    public function getFax(): ?string {
        return $this->fax ?? null;
    }

    public function setFax(?string $fax): void {
        $this->fax = $fax;
    }

    public function isMainContact(): bool {
        return $this->isMainContact ?? false;
    }

    public function setIsMainContact(?bool $isMainContact): void {
        $this->isMainContact = $isMainContact;
    }
}
