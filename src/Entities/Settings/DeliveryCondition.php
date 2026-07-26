<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : DeliveryCondition.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Settings;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Psr\Log\LoggerInterface;

/**
 * Lieferbedingung.
 */
class DeliveryCondition extends NamedEntity {
    protected ?int $id;

    protected ?string $name;

    protected ?bool $isDefault;

    protected ?string $text;

    protected ?float $deliveryDays;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function isValid(): bool {
        return parent::isValid() && isset($this->name);
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function getName(): ?string {
        return $this->name ?? null;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }

    public function isDefault(): bool {
        return $this->isDefault ?? false;
    }

    public function getText(): ?string {
        return $this->text ?? null;
    }

    public function setText(?string $text): void {
        $this->text = $text;
    }

    public function getDeliveryDays(): ?float {
        return $this->deliveryDays ?? null;
    }

    public function setDeliveryDays(?float $deliveryDays): void {
        $this->deliveryDays = $deliveryDays;
    }
}
