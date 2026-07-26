<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : TodoCreator.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Todos;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Ersteller eines To-dos (creator-Feld).
 */
class TodoCreator extends NamedEntity {
    protected ?string $firstName;

    protected ?string $lastName;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getFirstName(): ?string {
        return $this->firstName ?? null;
    }

    public function getLastName(): ?string {
        return $this->lastName ?? null;
    }
}
