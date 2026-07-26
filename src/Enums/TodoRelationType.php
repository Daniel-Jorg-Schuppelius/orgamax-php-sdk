<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : TodoRelationType.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\Enums;

/**
 * Typ einer mit einem To-do verknüpften Entität.
 */
enum TodoRelationType: string {
    case FILE = "file";
    case CUSTOMER = "customer";
    case SUPPLIER = "supplier";
    case USER = "user";
}
