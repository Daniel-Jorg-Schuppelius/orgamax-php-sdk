<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ListResponseAbstract.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Contracts\Abstracts;

use APIToolkit\Contracts\Abstracts\{NamedEntity, NamedValues};
use Orgamax\Entities\Common\ListMeta;

/**
 * Envelope für Listen-Antworten der orgaMAX-API: {"meta": {count, ...}, "data": [...]}.
 * Subklassen deklarieren das typisierte data-Property (eine NamedValues-Collection)
 * und ein passendes getData().
 */
abstract class ListResponseAbstract extends NamedEntity {
    protected ?ListMeta $meta;

    public function getMeta(): ?ListMeta {
        return $this->meta ?? null;
    }

    /**
     * @return NamedValues<NamedEntity>|null
     */
    abstract public function getData(): ?NamedValues;

    /**
     * Die Einträge der Liste — Grundlage der seitenweisen Iteration.
     *
     * @return array<int, mixed>
     */
    abstract public function getValues(): array;
}
