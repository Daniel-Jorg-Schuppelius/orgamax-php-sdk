<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : CustomerData.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Common;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Kunden-Schnappschuss, wie er in Belegen (Invoice, Offer, Order,
 * DeliveryNote) eingebettet wird.
 */
class CustomerData extends NamedEntity {
    protected ?string $city;

    protected ?string $companyName;

    protected ?string $companyNameAffix;

    protected ?string $country;

    protected ?string $countryIso;

    protected ?string $kind;

    protected ?string $name;

    protected ?string $number;

    protected ?string $street;

    protected ?string $vatNumber;

    protected ?string $zip;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getCity(): ?string {
        return $this->city ?? null;
    }

    public function getCompanyName(): ?string {
        return $this->companyName ?? null;
    }

    public function getCompanyNameAffix(): ?string {
        return $this->companyNameAffix ?? null;
    }

    public function getCountry(): ?string {
        return $this->country ?? null;
    }

    public function getCountryIso(): ?string {
        return $this->countryIso ?? null;
    }

    public function getKind(): ?string {
        return $this->kind ?? null;
    }

    public function getName(): ?string {
        return $this->name ?? null;
    }

    public function getNumber(): ?string {
        return $this->number ?? null;
    }

    public function getStreet(): ?string {
        return $this->street ?? null;
    }

    public function getVatNumber(): ?string {
        return $this->vatNumber ?? null;
    }

    public function getZip(): ?string {
        return $this->zip ?? null;
    }
}
