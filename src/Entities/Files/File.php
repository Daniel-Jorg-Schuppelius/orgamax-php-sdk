<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : File.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Files;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use DateTime;
use Psr\Log\LoggerInterface;

/**
 * Datei-Eintrag der Dateiliste (FileListData). Alle Felder sind read-only;
 * "mimetyp" ist der exakte API-Feldname (sic).
 */
class File extends NamedEntity {
    protected ?int $id;

    protected ?DateTime $recordDate;

    protected ?string $mimetyp;

    protected ?string $size;

    protected ?FileMetadata $metadata;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function getRecordDate(): ?DateTime {
        return $this->recordDate ?? null;
    }

    public function getMimetyp(): ?string {
        return $this->mimetyp ?? null;
    }

    public function getSize(): ?string {
        return $this->size ?? null;
    }

    public function getMetadata(): ?FileMetadata {
        return $this->metadata ?? null;
    }
}
