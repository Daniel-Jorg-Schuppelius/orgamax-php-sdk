<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : AuthEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints;

use APIToolkit\Contracts\Abstracts\API\EndpointAbstract;
use APIToolkit\Contracts\Interfaces\NamedEntityInterface;
use APIToolkit\Entities\ID;
use APIToolkit\Exceptions\NotAllowedException;
use Orgamax\Entities\Auth\Token;

/**
 * Auth-Route der orgaMAX-API. Erwartet einen Client mit Basic-Auth
 * (API-Key als Username, API-Secret als Passwort), siehe
 * Client::forCredentials().
 */
class AuthEndpoint extends EndpointAbstract {
    protected string $endpoint = 'auth/token';

    public function get(?ID $id = null): ?NamedEntityInterface {
        self::logErrorAndThrow(NotAllowedException::class, 'Getting a token requires an ownershipId, use token()', [], null, 405);
    }

    /**
     * Bezieht mit der ownershipId (aus der Callback-URL der Erweiterung,
     * Query-Parameter "iid") ein Bearer-Token.
     */
    public function token(string $ownershipId): Token {
        self::logDebug('Requesting bearer token', ['endpoint' => $this->endpoint]);

        return self::logInfoWithTimer(
            fn () => Token::fromJson(
                parent::postContents(['ownershipId' => $ownershipId], [], null, 200),
                self::$logger
            ),
            'Bearer token created'
        );
    }
}
