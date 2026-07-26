<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : SalesTaxFrequency.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Enums;

/**
 * Umsatzsteuer-Voranmeldungszeitraum des Mandanten.
 */
enum SalesTaxFrequency: string {
    case MONTHLY = "monthly";
    case QUARTERLY = "quarterly";
    case YEARLY = "yearly";
}
