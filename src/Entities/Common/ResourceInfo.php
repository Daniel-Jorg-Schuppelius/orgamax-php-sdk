<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ResourceInfo.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Common;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use APIToolkit\Entities\ID;
use Psr\Log\LoggerInterface;

/**
 * Data-Block der Create-/Update-Antworten: {"id": "90"}.
 */
class ResourceInfo extends NamedEntity {
    protected ?string $id;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?string {
        return $this->id ?? null;
    }

    /**
     * Liefert die ID als Value-Object, z. B. für Folge-Requests.
     */
    public function toID(): ?ID {
        return isset($this->id) ? new ID($this->id) : null;
    }
}
