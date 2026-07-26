<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ExpenseResponse.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Expenses;

use Orgamax\Contracts\Abstracts\ItemResponseAbstract;
use Psr\Log\LoggerInterface;

/**
 * Antwort von GET /expense/{id} — einzelne Ausgabe im {meta, data}-Envelope.
 */
class ExpenseResponse extends ItemResponseAbstract {
    protected ?Expense $data;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getData(): ?Expense {
        return $this->data ?? null;
    }
}
