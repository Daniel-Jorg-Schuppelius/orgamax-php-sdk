<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : TodoMessage.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Todos;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Nachricht zu einem To-do (TodoMessageListData). Das Feld "tenantd" heißt
 * in der Spec tatsächlich so (sic, Tippfehler der API) — Name beibehalten,
 * da die Hydration über die exakten API-Feldnamen läuft.
 */
class TodoMessage extends NamedEntity {
    protected ?int $id;

    protected ?int $todoId;

    protected ?int $tenantd;

    protected ?int $creatorId;

    protected ?string $message;

    protected ?string $type;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function getTodoId(): ?int {
        return $this->todoId ?? null;
    }

    /**
     * Mandanten-ID; Feldname "tenantd" ist ein Spec-Tippfehler der API.
     */
    public function getTenantd(): ?int {
        return $this->tenantd ?? null;
    }

    public function getCreatorId(): ?int {
        return $this->creatorId ?? null;
    }

    public function getMessage(): ?string {
        return $this->message ?? null;
    }

    public function getType(): ?string {
        return $this->type ?? null;
    }
}
