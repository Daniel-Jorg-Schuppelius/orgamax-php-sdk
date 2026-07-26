<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ExpenseList.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Expenses;

use Orgamax\Contracts\Abstracts\ListResponseAbstract;
use Psr\Log\LoggerInterface;

/**
 * Antwort von GET /expense — paginierte Ausgabenliste
 * (meta mit count/totalCount und Filter-Zählern all/inProgress/open/paid).
 */
class ExpenseList extends ListResponseAbstract {
    protected ?Expenses $data;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getData(): ?Expenses {
        return $this->data ?? null;
    }

    /**
     * @return array<int, Expense>
     */
    public function getValues(): array {
        return $this->data?->getValues() ?? [];
    }
}
