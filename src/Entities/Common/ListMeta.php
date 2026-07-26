<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ListMeta.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Common;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Meta-Block der Listen-Antworten: Anzahl der zurückgegebenen und aller
 * Datensätze sowie ressourcenspezifische Filter-Zähler (z. B. draft/paid).
 */
class ListMeta extends NamedEntity {
    protected ?int $count;

    protected ?int $totalCount;

    /** @var array<string, mixed>|null */
    protected ?array $filter;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getCount(): ?int {
        return $this->count ?? null;
    }

    public function getTotalCount(): ?int {
        return $this->totalCount ?? null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getFilter(): ?array {
        return $this->filter ?? null;
    }
}
