<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : DeliveryNoteResponse.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\DeliveryNotes;

use Orgamax\Contracts\Abstracts\ItemResponseAbstract;
use Psr\Log\LoggerInterface;

/**
 * Antwort von GET /deliveryNote/{id} — einzelner Lieferschein im
 * {meta, data}-Envelope.
 */
class DeliveryNoteResponse extends ItemResponseAbstract {
    protected ?DeliveryNote $data;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getData(): ?DeliveryNote {
        return $this->data ?? null;
    }
}
