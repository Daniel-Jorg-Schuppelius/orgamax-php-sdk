<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : User.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Users;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Benutzer, wie er von GET /user geliefert wird.
 */
class User extends NamedEntity {
    protected ?string $id;

    protected ?string $firstName;

    protected ?string $lastName;

    protected ?string $email;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?string {
        return $this->id ?? null;
    }

    public function getFirstName(): ?string {
        return $this->firstName ?? null;
    }

    public function getLastName(): ?string {
        return $this->lastName ?? null;
    }

    public function getEmail(): ?string {
        return $this->email ?? null;
    }
}
