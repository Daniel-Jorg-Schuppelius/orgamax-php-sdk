<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : DeliveryNoteList.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\DeliveryNotes;

use Orgamax\Contracts\Abstracts\ListResponseAbstract;
use Psr\Log\LoggerInterface;

/**
 * Antwort von GET /deliveryNote — paginierte Lieferscheinliste
 * (meta.filter: all/delivered/draft).
 */
class DeliveryNoteList extends ListResponseAbstract {
    protected ?DeliveryNotes $data;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getData(): ?DeliveryNotes {
        return $this->data ?? null;
    }

    /**
     * @return array<int, DeliveryNote>
     */
    public function getValues(): array {
        return $this->data?->getValues() ?? [];
    }
}
