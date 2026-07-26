<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ArticleSettingsEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints\Settings;

use APIToolkit\Contracts\Abstracts\API\EndpointAbstract;
use APIToolkit\Contracts\Interfaces\NamedEntityInterface;
use APIToolkit\Entities\ID;
use APIToolkit\Exceptions\NotAllowedException;
use Orgamax\Entities\Settings\ArticleSetting;

class ArticleSettingsEndpoint extends EndpointAbstract {
    protected string $endpoint = 'setting/article';

    public function get(?ID $id = null): ?NamedEntityInterface {
        self::logErrorAndThrow(NotAllowedException::class, 'Article settings cannot be fetched, use GET setting/miscellaneous instead', [], null, 405);
    }

    /**
     * POST /setting/article — setzt Einheiten und/oder Kategorien und liefert
     * die aktuellen Artikel-Einstellungen zurück (Status 200).
     */
    public function create(ArticleSetting $data): ?ArticleSetting {
        self::logDebug('Creating article setting', ['endpoint' => $this->endpoint]);

        return self::logInfoWithTimer(function () use ($data): ?ArticleSetting {
            $response = parent::postContents($data->toArray(), [], null, 200);
            if (empty($response) || $response === '[]') {
                return null;
            }

            $decoded = json_decode($response, true);
            if (!is_array($decoded)) {
                return null;
            }
            $item = array_is_list($decoded) ? ($decoded[0] ?? null) : $decoded;

            return is_array($item) ? ArticleSetting::fromArray($item, self::$logger) : null;
        }, 'Article setting created');
    }
}
