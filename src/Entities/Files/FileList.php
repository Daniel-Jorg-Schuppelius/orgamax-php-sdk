<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : FileList.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Files;

use Orgamax\Contracts\Abstracts\ListResponseAbstract;
use Psr\Log\LoggerInterface;

/**
 * Antwort von GET /file und GET /file/{id}/meta — paginierte Dateiliste.
 */
class FileList extends ListResponseAbstract {
    protected ?Files $data;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getData(): ?Files {
        return $this->data ?? null;
    }

    /**
     * @return array<int, File>
     */
    public function getValues(): array {
        return $this->data?->getValues() ?? [];
    }
}
