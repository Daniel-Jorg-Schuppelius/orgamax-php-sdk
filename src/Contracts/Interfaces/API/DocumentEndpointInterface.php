<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : DocumentEndpointInterface.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Contracts\Interfaces\API;

use APIToolkit\Contracts\Interfaces\API\EndpointInterface;
use APIToolkit\Entities\ID;

/**
 * Endpoint mit Dokument-Route (GET {resource}/document/{id}) — liefert das
 * Beleg-PDF als Binärstring.
 */
interface DocumentEndpointInterface extends EndpointInterface {
    public function document(ID $id, ?string $type = null, ?string $filename = null): string;
}
