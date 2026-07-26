<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : SalesDocumentState.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Enums;

/**
 * Status eines Angebots oder Auftrags.
 */
enum SalesDocumentState: string {
    case OPEN = "open";
    case ACCEPT = "accept";
    case REJECTED = "rejected";
    case CANCELLED = "cancelled";
}
