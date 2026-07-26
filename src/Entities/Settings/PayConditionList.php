<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : PayConditionList.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Settings;

use Orgamax\Contracts\Abstracts\ListResponseAbstract;
use Psr\Log\LoggerInterface;

/**
 * Antwort von GET /setting/payCondition — {"payConditionData": [...]},
 * ohne Pagination-Meta.
 */
class PayConditionList extends ListResponseAbstract {
    protected ?PayConditions $payConditionData;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getData(): ?PayConditions {
        return $this->payConditionData ?? null;
    }

    /**
     * @return array<int, PayCondition>
     */
    public function getValues(): array {
        return $this->payConditionData?->getValues() ?? [];
    }
}
