<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ItemResponseAbstract.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Contracts\Abstracts;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use APIToolkit\Contracts\Interfaces\NamedEntityInterface;

/**
 * Envelope für Einzelobjekt-Antworten der orgaMAX-API: {"meta": {}, "data": {...}}.
 * Subklassen deklarieren das typisierte data-Property und ein passendes getData().
 */
abstract class ItemResponseAbstract extends NamedEntity {
    /** @var array<string, mixed>|null */
    protected ?array $meta;

    /**
     * @return array<string, mixed>|null
     */
    public function getMeta(): ?array {
        return $this->meta ?? null;
    }

    abstract public function getData(): ?NamedEntityInterface;
}
