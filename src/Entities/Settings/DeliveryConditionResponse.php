<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : DeliveryConditionResponse.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Settings;

use Orgamax\Contracts\Abstracts\ItemResponseAbstract;
use Psr\Log\LoggerInterface;

/**
 * Antwort von GET /setting/deliveryCondition/{id} — {meta, data}-Envelope.
 */
class DeliveryConditionResponse extends ItemResponseAbstract {
    protected ?DeliveryCondition $data;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getData(): ?DeliveryCondition {
        return $this->data ?? null;
    }
}
