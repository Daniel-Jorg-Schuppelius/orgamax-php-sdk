<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : FileMetadata.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Files;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Darstellungs-Metadaten einer Datei (metadata-Feld in der Dateiliste).
 */
class FileMetadata extends NamedEntity {
    protected ?float $height;

    protected ?float $rotate;

    protected ?float $scale;

    protected ?float $width;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getHeight(): ?float {
        return $this->height ?? null;
    }

    public function getRotate(): ?float {
        return $this->rotate ?? null;
    }

    public function getScale(): ?float {
        return $this->scale ?? null;
    }

    public function getWidth(): ?float {
        return $this->width ?? null;
    }
}
