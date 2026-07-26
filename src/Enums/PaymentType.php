<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : PaymentType.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Enums;

/**
 * Typ einer Rechnungszahlung; bei Teilbeträgen muss partial/discount/bankcharge/surcharge verwendet werden.
 */
enum PaymentType: string {
    case PAYMENT = "payment";
    case PARTIAL = "partial";
    case DISCOUNT = "discount";
    case BANKCHARGE = "bankcharge";
    case SURCHARGE = "surcharge";
}
