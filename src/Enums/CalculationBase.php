<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : CalculationBase.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Enums;

/**
 * Kalkulationsbasis eines Artikels: Preis netto oder brutto gepflegt.
 */
enum CalculationBase: string {
    case NET = "net";
    case GROSS = "gross";
}
