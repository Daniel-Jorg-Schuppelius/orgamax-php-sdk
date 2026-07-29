<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : SearchableEndpointInterface.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Contracts\Interfaces\API;

use APIToolkit\Contracts\Interfaces\API\EndpointInterfaces\SearchableEndpointInterface as APIToolkitSearchableEndpointInterface;
use Orgamax\Contracts\Abstracts\ListResponseAbstract;

/**
 * Endpoint mit paginierter Listen-Route (offset, limit, orderBy, desc, search).
 */
interface SearchableEndpointInterface extends APIToolkitSearchableEndpointInterface {
    /**
     * @param array<string, mixed> $queryParams
     * @param array<string, mixed> $options
     */
    public function search(array $queryParams = [], array $options = []): ?ListResponseAbstract;
}
