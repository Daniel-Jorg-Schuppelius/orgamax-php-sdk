<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : MiscellaneousEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints\Settings;

use APIToolkit\Contracts\Abstracts\API\EndpointAbstract;
use APIToolkit\Entities\ID;
use Orgamax\Entities\Settings\Miscellaneous;

class MiscellaneousEndpoint extends EndpointAbstract {
    protected string $endpoint = 'setting/miscellaneous';

    /**
     * GET /setting/miscellaneous — in der orgaMAX-App gepflegte
     * Auswahllisten; es gibt keine Einzel-IDs, $id wird ignoriert.
     */
    public function get(?ID $id = null): ?Miscellaneous {
        self::logDebug('Fetching miscellaneous settings', ['endpoint' => $this->endpoint]);

        return self::logDebugWithTimer(function (): ?Miscellaneous {
            $response = parent::getContents();
            if (empty($response) || $response === '[]') {
                return null;
            }

            return Miscellaneous::fromJson($response, self::$logger);
        }, 'Miscellaneous settings fetched');
    }
}
