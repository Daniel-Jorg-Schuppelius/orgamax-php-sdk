<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ArticleSetting.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Settings;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Artikel-Einstellungen (POST /setting/article): Einheiten und Kategorien,
 * wie sie in den Positionen von Rechnungen/Angeboten/Lieferscheinen
 * verwendet werden.
 */
class ArticleSetting extends NamedEntity {
    /** @var array<int, string>|null */
    protected ?array $units;

    /** @var array<int, string>|null */
    protected ?array $categories;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    /**
     * @return array<int, string>|null
     */
    public function getUnits(): ?array {
        return $this->units ?? null;
    }

    /**
     * @param array<int, string>|null $units
     */
    public function setUnits(?array $units): void {
        $this->units = $units;
    }

    /**
     * @return array<int, string>|null
     */
    public function getCategories(): ?array {
        return $this->categories ?? null;
    }

    /**
     * @param array<int, string>|null $categories
     */
    public function setCategories(?array $categories): void {
        $this->categories = $categories;
    }
}
