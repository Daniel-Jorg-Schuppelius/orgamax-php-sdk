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
            $item = $this->raw();

            return $item === [] ? null : AccountSetting::fromArray($item, self::$logger);
        }, 'Account settings fetched');
    }

    /**
     * Kontoeinstellungen unverändert als Array — für Felder, welche die Spec
     * (und damit AccountSetting) nicht kennt. Leeres Array, wenn die API
     * nichts liefert.
     *
     * @return array<string, mixed>
     */
    public function raw(): array {
        $response = parent::getContents();
        if (empty($response) || $response === '[]') {
            return [];
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            return [];
        }
        $item = array_is_list($decoded) ? ($decoded[0] ?? null) : $decoded;

        /** @var array<string, mixed> */
        return is_array($item) ? $item : [];
    }
}
