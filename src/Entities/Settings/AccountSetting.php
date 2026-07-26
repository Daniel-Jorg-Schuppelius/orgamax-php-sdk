<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : AccountSetting.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Settings;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Orgamax\Entities\Common\Address;
use Orgamax\Enums\SalesTaxFrequency;
use Psr\Log\LoggerInterface;

/**
 * Mandanten-Einstellungen (GET /setting/account, nur lesend).
 */
class AccountSetting extends NamedEntity {
    protected ?string $accountEmail;

    protected ?string $bankAccountBic;

    protected ?string $bankAccountIban;

    protected ?string $businessField;

    protected ?string $businessFieldExtension;

    protected ?string $businessSize;

    protected ?Address $companyAddress;

    protected ?string $companyType;

    protected ?bool $isSmallBusiness;

    protected ?bool $isSubjectToImputedTaxation;

    protected ?string $paypalUserName;

    protected ?bool $permanentExtensionOfPaymentDeadline;

    protected ?string $phone;

    protected ?SalesTaxFrequency $salesTaxFrequency;

    protected ?string $senderEmail;

    protected ?string $senderEmailName;

    protected ?string $taxNumber;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getAccountEmail(): ?string {
        return $this->accountEmail ?? null;
    }

    public function getBankAccountBic(): ?string {
        return $this->bankAccountBic ?? null;
    }

    public function getBankAccountIban(): ?string {
        return $this->bankAccountIban ?? null;
    }

    public function getBusinessField(): ?string {
        return $this->businessField ?? null;
    }

    public function getBusinessFieldExtension(): ?string {
        return $this->businessFieldExtension ?? null;
    }

    public function getBusinessSize(): ?string {
        return $this->businessSize ?? null;
    }

    public function getCompanyAddress(): ?Address {
        return $this->companyAddress ?? null;
    }

    public function getCompanyType(): ?string {
        return $this->companyType ?? null;
    }

    public function isSmallBusiness(): bool {
        return $this->isSmallBusiness ?? false;
    }

    public function isSubjectToImputedTaxation(): bool {
        return $this->isSubjectToImputedTaxation ?? false;
    }

    public function getPaypalUserName(): ?string {
        return $this->paypalUserName ?? null;
    }

    public function getPermanentExtensionOfPaymentDeadline(): ?bool {
        return $this->permanentExtensionOfPaymentDeadline ?? null;
    }

    public function getPhone(): ?string {
        return $this->phone ?? null;
    }

    public function getSalesTaxFrequency(): ?SalesTaxFrequency {
        return $this->salesTaxFrequency ?? null;
    }

    public function getSenderEmail(): ?string {
        return $this->senderEmail ?? null;
    }

    public function getSenderEmailName(): ?string {
        return $this->senderEmailName ?? null;
    }

    public function getTaxNumber(): ?string {
        return $this->taxNumber ?? null;
    }
}
