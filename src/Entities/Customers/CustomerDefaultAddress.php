<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : CustomerDefaultAddress.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Entities\Customers;

use APIToolkit\Contracts\Abstracts\NamedEntity;
use Orgamax\Entities\Common\Address;
use Psr\Log\LoggerInterface;

/**
 * Standard-Adressen eines Kunden. Die billingAddress trägt beim Anlegen und
 * Ändern auch kind, firstName/lastName bzw. companyName des Kunden.
 */
class CustomerDefaultAddress extends NamedEntity {
    protected ?string $id;

    protected ?Address $billingAddress;

    protected ?Address $deliveryAddress;

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function __construct($data = null, ?LoggerInterface $logger = null) {
        parent::__construct($data, $logger);
    }

    public function getId(): ?string {
        return $this->id ?? null;
    }

    public function getBillingAddress(): ?Address {
        return $this->billingAddress ?? null;
    }

    public function setBillingAddress(?Address $billingAddress): void {
        $this->billingAddress = $billingAddress;
    }

    public function getDeliveryAddress(): ?Address {
        return $this->deliveryAddress ?? null;
    }

    public function setDeliveryAddress(?Address $deliveryAddress): void {
        $this->deliveryAddress = $deliveryAddress;
    }
}
