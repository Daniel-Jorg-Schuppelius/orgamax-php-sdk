<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : PayKind.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Enums;

/**
 * Zahlungsart einer Ausgabe.
 */
enum PayKind: string {
    case CASH = "cash";
    case BANK = "bank";
    case OPEN = "open";
}
