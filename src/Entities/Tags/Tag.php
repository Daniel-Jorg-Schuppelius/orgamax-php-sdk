<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : Tag.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Tags;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Tag, wie es von GET /tags geliefert und an Kunden, Artikeln oder Dateien
 * verwendet wird.
 */
class Tag extends NamedEntity {
    protected ?int $id;

    protected ?string $label;

    protected ?string $backgroundColor;

    protected ?string $color;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function getLabel(): ?string {
        return $this->label ?? null;
    }

    public function setLabel(?string $label): void {
        $this->label = $label;
    }

    public function getBackgroundColor(): ?string {
        return $this->backgroundColor ?? null;
    }

    public function getColor(): ?string {
        return $this->color ?? null;
    }
}
