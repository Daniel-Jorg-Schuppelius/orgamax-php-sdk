<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ExpensesEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints;

use APIToolkit\Contracts\Abstracts\API\EndpointAbstract;
use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\Common\ResourceResponse;
use Orgamax\Entities\Expenses\{Expense, ExpenseList, ExpenseResponse};

class ExpensesEndpoint extends EndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'expense';

    public function create(Expense $data): ResourceResponse {
        self::logDebug('Creating expense', ['endpoint' => $this->endpoint]);

        return self::logInfoWithTimer(
            fn () => ResourceResponse::fromJson(
                // Die API antwortet hier mit 200 statt 201.
                parent::postContents($data->toArray(), [], "{$this->getEndpointUrl()}/", 200),
                self::$logger
            ),
            'Expense created'
        );
    }

    public function get(?ID $id = null): ?Expense {
        if (is_null($id)) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'ID is required for getting an expense');
        }
        self::logDebug('Fetching expense', ['id' => $id->toString()]);

        return self::logDebugWithTimer(function () use ($id): ?Expense {
            $response = parent::getContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return ExpenseResponse::fromJson($response, self::$logger)->getData();
        }, "Expense fetched (ID: {$id->toString()})");
    }

    public function search(array $queryParams = [], array $options = []): ?ExpenseList {
        self::logDebug('Searching expenses', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?ExpenseList {
            $response = parent::getContents($queryParams, $options);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return ExpenseList::fromJson($response, self::$logger);
        }, 'Expenses search completed');
    }

    public function update(ID $id, Expense $data): bool {
        self::logDebug('Updating expense', ['id' => $id->toString()]);

        return self::logInfoWithTimer(function () use ($id, $data): bool {
            // Die API antwortet hier mit 204 ohne Body.
            parent::putContents($data->toArray(), [], "{$this->getEndpointUrl()}/{$id->toString()}", 204);

            return true;
        }, "Expense updated (ID: {$id->toString()})");
    }

    public function delete(ID $id): bool {
        self::logDebug('Deleting expense', ['id' => $id->toString()]);

        return self::logInfoWithTimer(function () use ($id): bool {
            parent::deleteContents([], "{$this->getEndpointUrl()}/{$id->toString()}", 204);

            return true;
        }, "Expense deleted (ID: {$id->toString()})");
    }

    /**
     * Löscht einen einzelnen Beleg (Receipt) einer Ausgabe.
     */
    public function deleteReceipt(ID $id): bool {
        self::logDebug('Deleting expense receipt', ['id' => $id->toString()]);

        return self::logInfoWithTimer(function () use ($id): bool {
            parent::deleteContents([], "{$this->getEndpointUrl()}/receipt/{$id->toString()}", 204);

            return true;
        }, "Expense receipt deleted (ID: {$id->toString()})");
    }
}
