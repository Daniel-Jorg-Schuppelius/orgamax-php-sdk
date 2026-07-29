<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : Todo.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Todos;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use DateTime;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;

/**
 * To-do, Vereinigung der Listen- (id, date) und Detail-Felder (TodoDataObject)
 * der API; content stammt aus den Request-Beispielen von POST /todo/.
 * Angelegt wird ein To-do über einen flachen Body (content, dueToDate,
 * customerIds/userIds/supplierIds), daher ist die Entity rein lesend.
 */
class Todo extends NamedEntity {
    protected ?int $id;

    protected ?string $date;

    protected ?string $content;

    protected ?string $dueToDate;

    protected ?string $tenantId;

    protected ?string $userId;

    protected ?string $doneAt;

    /** @var array<int|string, mixed>|null */
    protected ?array $metaData;

    protected ?TodoAppData $appData;

    protected ?TodoCreator $creator;

    protected ?DateTime $createdAt;

    protected ?DateTime $updatedAt;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function getDate(): ?string {
        return $this->date ?? null;
    }

    public function getContent(): ?string {
        return $this->content ?? null;
    }

    public function getDueToDate(): ?string {
        return $this->dueToDate ?? null;
    }

    public function getTenantId(): ?string {
        return $this->tenantId ?? null;
    }

    public function getUserId(): ?string {
        return $this->userId ?? null;
    }

    public function getDoneAt(): ?string {
        return $this->doneAt ?? null;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getMetaData(): ?array {
        return $this->metaData ?? null;
    }

    public function getAppData(): ?TodoAppData {
        return $this->appData ?? null;
    }

    public function getCreator(): ?TodoCreator {
        return $this->creator ?? null;
    }

    public function getCreatedAt(): ?DateTime {
        return $this->createdAt ?? null;
    }

    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt ?? null;
    }

    /**
     * Datumsfelder kommen als String ("YYYY-MM-DD" bzw. ISO-8601) von der
     * API; die folgenden Accessoren geben sie typisiert zurück, ohne das
     * Wire-Format der Property anzutasten.
     */
    protected static function toDateTime(?string $value): ?DateTimeImmutable {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (\Exception) {
            return null;
        }
    }

    public function getDateAsDateTime(): ?DateTimeImmutable {
        return self::toDateTime($this->date ?? null);
    }

    public function getDueToDateAsDateTime(): ?DateTimeImmutable {
        return self::toDateTime($this->dueToDate ?? null);
    }

    public function getDoneAtAsDateTime(): ?DateTimeImmutable {
        return self::toDateTime($this->doneAt ?? null);
    }
}
