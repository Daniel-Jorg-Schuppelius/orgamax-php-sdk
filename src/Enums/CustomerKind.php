<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : CustomerKind.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Enums;

/**
 * Art eines Kunden bzw. einer Adresse: Person oder Firma.
 */
enum CustomerKind: string {
    case PERSON = "person";
    case COMPANY = "company";
}
