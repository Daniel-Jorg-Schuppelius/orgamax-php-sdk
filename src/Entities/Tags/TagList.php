<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : TagList.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Tags;

use Orgamax\Contracts\Abstracts\ListResponseAbstract;
use Psr\Log\LoggerInterface;

/**
 * Antwort von GET /tags — vollständige Tag-Liste (nicht paginiert).
 */
class TagList extends ListResponseAbstract {
    protected ?Tags $data;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getData(): ?Tags {
        return $this->data ?? null;
    }

    /**
     * @return array<int, Tag>
     */
    public function getValues(): array {
        return $this->data?->getValues() ?? [];
    }
}
