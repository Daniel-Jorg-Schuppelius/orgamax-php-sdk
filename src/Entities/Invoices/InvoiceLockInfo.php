<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : InvoiceLockInfo.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Invoices;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * data-Block der Antwort von PUT /invoice/{id}/lock. Die Spec nennt das
 * Pfad-Feld "documenthPath" (Tippfehler); vorsorglich wird auch ein korrekt
 * geschriebenes "documentPath" aufgenommen.
 */
class InvoiceLockInfo extends NamedEntity {
    protected ?int $id;

    protected ?string $documenthPath;

    protected ?string $documentPath;

    protected ?string $number;

    protected ?string $state;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    /**
     * Feldname exakt wie in der Spec (Tippfehler inklusive).
     */
    public function getDocumenthPath(): ?string {
        return $this->documenthPath ?? null;
    }

    /**
     * Dokumentpfad; fällt auf das Spec-Tippfehler-Feld documenthPath zurück.
     */
    public function getDocumentPath(): ?string {
        return $this->documentPath ?? $this->documenthPath ?? null;
    }

    public function getNumber(): ?string {
        return $this->number ?? null;
    }

    public function getState(): ?string {
        return $this->state ?? null;
    }
}
