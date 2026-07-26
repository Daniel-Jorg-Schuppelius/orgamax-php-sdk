<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : MiscellaneousData.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Settings;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * data-Teil der Antwort von GET /setting/miscellaneous — in der orgaMAX-App
 * gepflegte Auswahllisten.
 */
class MiscellaneousData extends NamedEntity {
    /** @var array<int, string>|null */
    protected ?array $articleCategories;

    /** @var array<int, string>|null */
    protected ?array $articleUnits;

    /** @var array<int, string>|null */
    protected ?array $customerCategories;

    /** @var array<int, string>|null */
    protected ?array $jobTitles;

    /** @var array<int, string>|null */
    protected ?array $salutations;

    /** @var array<int, string>|null */
    protected ?array $titles;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    /**
     * @return array<int, string>|null
     */
    public function getArticleCategories(): ?array {
        return $this->articleCategories ?? null;
    }

    /**
     * @return array<int, string>|null
     */
    public function getArticleUnits(): ?array {
        return $this->articleUnits ?? null;
    }

    /**
     * @return array<int, string>|null
     */
    public function getCustomerCategories(): ?array {
        return $this->customerCategories ?? null;
    }

    /**
     * @return array<int, string>|null
     */
    public function getJobTitles(): ?array {
        return $this->jobTitles ?? null;
    }

    /**
     * @return array<int, string>|null
     */
    public function getSalutations(): ?array {
        return $this->salutations ?? null;
    }

    /**
     * @return array<int, string>|null
     */
    public function getTitles(): ?array {
        return $this->titles ?? null;
    }
}
