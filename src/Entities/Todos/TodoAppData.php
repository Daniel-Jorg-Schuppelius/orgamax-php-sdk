<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : TodoAppData.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Todos;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * App-Informationen eines To-dos (appData-Feld).
 */
class TodoAppData extends NamedEntity {
    protected ?string $appName;

    protected ?string $appId;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getAppName(): ?string {
        return $this->appName ?? null;
    }

    public function getAppId(): ?string {
        return $this->appId ?? null;
    }
}
