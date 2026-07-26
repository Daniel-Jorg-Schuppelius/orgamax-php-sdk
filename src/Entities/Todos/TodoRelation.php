<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : TodoRelation.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Todos;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Orgamax\Enums\TodoRelationType;
use Psr\Log\LoggerInterface;

/**
 * Verknüpfung eines To-dos mit einer Entität (Body-Element von
 * PUT /todo/{id}/link bzw. /unlink): {id, type}.
 */
class TodoRelation extends NamedEntity {
    protected ?int $id;

    protected ?TodoRelationType $type;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getType(): ?TodoRelationType {
        return $this->type ?? null;
    }

    public function setType(?TodoRelationType $type): void {
        $this->type = $type;
    }
}
