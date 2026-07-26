<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : PriceKind.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Enums;

/**
 * Preisart eines Belegs (Invoice, Offer, Order): netto oder brutto.
 */
enum PriceKind: string {
    case NET = "net";
    case GROSS = "gross";
}
