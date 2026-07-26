<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ChartOfAccount.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Bookkeeping;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * FIBU-Konto aus dem Kontenrahmen, wie es von
 * GET /bookkeeping/getchartofaccounts geliefert wird.
 */
class ChartOfAccount extends NamedEntity {
    protected ?string $id;

    protected ?float $accountNo;

    protected ?string $accountDescriptionLong;

    protected ?string $accountDescriptionShort;

    protected ?string $accountDescriptionCustom;

    protected ?string $accountCategory;

    /** @var array<int|string, mixed>|null */
    protected ?array $allowedVatRates;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?string {
        return $this->id ?? null;
    }

    public function getAccountNo(): ?float {
        return $this->accountNo ?? null;
    }

    public function getAccountDescriptionLong(): ?string {
        return $this->accountDescriptionLong ?? null;
    }

    public function getAccountDescriptionShort(): ?string {
        return $this->accountDescriptionShort ?? null;
    }

    public function getAccountDescriptionCustom(): ?string {
        return $this->accountDescriptionCustom ?? null;
    }

    public function getAccountCategory(): ?string {
        return $this->accountCategory ?? null;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getAllowedVatRates(): ?array {
        return $this->allowedVatRates ?? null;
    }
}
