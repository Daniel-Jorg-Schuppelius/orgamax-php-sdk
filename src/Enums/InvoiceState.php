<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : InvoiceState.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Enums;

/**
 * Status einer Rechnung.
 */
enum InvoiceState: string {
    case DRAFT = "draft";
    case LOCKED = "locked";
    case PARTIALLY_PAID = "partiallyPaid";
    case PAID = "paid";
    case CANCELLED = "cancelled";
}
