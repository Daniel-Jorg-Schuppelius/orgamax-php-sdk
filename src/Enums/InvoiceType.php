<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : InvoiceType.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Enums;

/**
 * Typ eines Rechnungsdokuments.
 */
enum InvoiceType: string {
    case INVOICE = "invoice";
    case CLOSING_INVOICE = "closingInvoice";
    case DEPOSIT_INVOICE = "depositInvoice";
    case RECURRING_INVOICE = "recurringInvoice";
    case RECURRING_INVOICE_TEMPLATE = "recurringInvoiceTemplate";
}
