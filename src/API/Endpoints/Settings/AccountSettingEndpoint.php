<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : AccountSettingEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints\Settings;

use APIToolkit\Contracts\Abstracts\API\EndpointAbstract;
use APIToolkit\Entities\ID;
use Orgamax\Entities\Settings\AccountSetting;

class AccountSettingEndpoint extends EndpointAbstract {
    protected string $endpoint = 'setting/account';

    /**
     * GET /setting/account liefert ein Array mit den Einstellungen des
     * Mandanten — es gibt keine Einzel-IDs, $id wird ignoriert.
     */
    public function get(?ID $id = null): ?AccountSetting {
        self::logDebug('Fetching account settings', ['endpoint' => $this->endpoint]);

        return self::logDebugWithTimer(function (): ?AccountSetting {
            $response = parent::getContents();
            if (empty($response) || $response === '[]') {
                return null;
            }

            $decoded = json_decode($response, true);
            if (!is_array($decoded)) {
                return null;
            }
            $item = array_is_list($decoded) ? ($decoded[0] ?? null) : $decoded;

            return is_array($item) ? AccountSetting::fromArray($item, self::$logger) : null;
        }, 'Account settings fetched');
    }
}
