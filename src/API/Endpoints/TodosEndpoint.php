<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : TodosEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\Contracts\Abstracts\API\PagedEndpointAbstract;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\Todos\{TodoList, TodoMessageList, TodoRelations, Todos};

/**
 * To-dos (Aufgaben) der orgaMAX-API. Achtung, einige Spec-Kuriositäten:
 * GET /todo/{id} liefert die Nachrichten des To-dos (nicht das To-do selbst),
 * und eine Nachricht wird per POST /todo/message/{id} GELÖSCHT.
 */
class TodosEndpoint extends PagedEndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'todo';

    /**
     * Legt ein To-do an (POST /todo/, Antwort 200). Der Request-Body ist laut
     * Spec-Beispielen flach: content, dueToDate sowie optionale ID-Arrays für
     * direkt zu verknüpfende Kunden, Benutzer und Lieferanten. Die Antwort ist
     * ein Array von TodoData-Objekten.
     *
     * @param array<int, int> $customerIds
     * @param array<int, int> $userIds
     * @param array<int, int> $supplierIds
     */
    public function create(string $content, string $dueToDate, array $customerIds = [], array $userIds = [], array $supplierIds = []): Todos {
        self::logDebug('Creating todo', ['endpoint' => $this->endpoint]);

        $body = [
            'content' => $content,
            'dueToDate' => $dueToDate,
        ];
        if (!empty($customerIds)) {
            $body['customerIds'] = $customerIds;
        }
        if (!empty($userIds)) {
            $body['userIds'] = $userIds;
        }
        if (!empty($supplierIds)) {
            $body['supplierIds'] = $supplierIds;
        }

        return self::logInfoWithTimer(
            fn () => Todos::fromJson(
                parent::postContents($body, [], "{$this->getEndpointUrl()}/", 200),
                self::$logger
            ),
            'Todo created'
        );
    }

    /**
     * ACHTUNG: GET /todo/{id} liefert NICHT das To-do selbst, sondern die
     * Liste seiner Nachrichten (TodoMessageList).
     */
    public function get(?ID $id = null): ?TodoMessageList {
        if (is_null($id)) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'ID is required for getting the messages of a todo');
        }
        self::logDebug('Fetching todo messages', ['id' => $id->toString()]);

        return self::logDebugWithTimer(function () use ($id): ?TodoMessageList {
            $response = parent::getContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return TodoMessageList::fromJson($response, self::$logger);
        }, "Todo messages fetched (ID: {$id->toString()})");
    }

    public function search(array $queryParams = [], array $options = []): ?TodoList {
        self::logDebug('Searching todos', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?TodoList {
            $response = parent::getContents($queryParams, $options);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return TodoList::fromJson($response, self::$logger);
        }, 'Todos search completed');
    }

    public function delete(ID $id): bool {
        self::logDebug('Deleting todo', ['id' => $id->toString()]);

        return self::logInfoWithTimer(function () use ($id): bool {
            parent::deleteContents([], "{$this->getEndpointUrl()}/{$id->toString()}", 204);

            return true;
        }, "Todo deleted (ID: {$id->toString()})");
    }

    /**
     * Setzt das Fälligkeitsdatum neu (PUT /todo/{id}, Body {"dueToDate": "Y-m-d"}).
     * Antwort ist 200 ohne Body.
     */
    public function resetDueDate(ID $id, string $dueToDate): bool {
        self::logDebug('Resetting todo due date', ['id' => $id->toString(), 'dueToDate' => $dueToDate]);

        return self::logInfoWithTimer(function () use ($id, $dueToDate): bool {
            parent::putContents(['dueToDate' => $dueToDate], [], "{$this->getEndpointUrl()}/{$id->toString()}", 200);

            return true;
        }, "Todo due date reset (ID: {$id->toString()})");
    }

    /**
     * Legt eine Nachricht zu einem To-do an (POST /todo/{id}/message, Antwort
     * 200). Die Antwort ist laut Spec — wie bei create() — ein Array von
     * TodoData-Objekten.
     */
    public function createMessage(ID $id, string $message): Todos {
        self::logDebug('Creating todo message', ['id' => $id->toString()]);

        return self::logInfoWithTimer(
            fn () => Todos::fromJson(
                parent::postContents(['message' => $message], [], "{$this->getEndpointUrl()}/{$id->toString()}/message", 200),
                self::$logger
            ),
            "Todo message created (Todo-ID: {$id->toString()})"
        );
    }

    /**
     * Löscht eine To-do-Nachricht. Spec-Kuriosität: das Löschen erfolgt per
     * POST /todo/message/{id} (nicht DELETE!), Antwort 200 ohne Body.
     */
    public function deleteMessage(ID $id): bool {
        self::logDebug('Deleting todo message', ['id' => $id->toString()]);

        return self::logInfoWithTimer(function () use ($id): bool {
            parent::postContents([], [], "{$this->getEndpointUrl()}/message/{$id->toString()}", 200);

            return true;
        }, "Todo message deleted (Message-ID: {$id->toString()})");
    }

    /**
     * Verknüpft Entitäten mit einem To-do (PUT /todo/{id}/link).
     * Body: Array von {id, type}.
     */
    public function link(ID $id, TodoRelations $relations): bool {
        self::logDebug('Linking entities to todo', ['id' => $id->toString(), 'count' => $relations->count()]);

        return self::logInfoWithTimer(function () use ($id, $relations): bool {
            parent::putContents($relations->toArray(), [], "{$this->getEndpointUrl()}/{$id->toString()}/link", 200);

            return true;
        }, "Todo linked (ID: {$id->toString()})");
    }

    /**
     * Löst Verknüpfungen eines To-dos (PUT /todo/{id}/unlink).
     * Body: Array von {id, type}.
     */
    public function unlink(ID $id, TodoRelations $relations): bool {
        self::logDebug('Unlinking entities from todo', ['id' => $id->toString(), 'count' => $relations->count()]);

        return self::logInfoWithTimer(function () use ($id, $relations): bool {
            parent::putContents($relations->toArray(), [], "{$this->getEndpointUrl()}/{$id->toString()}/unlink", 200);

            return true;
        }, "Todo unlinked (ID: {$id->toString()})");
    }

    /**
     * Setzt den Status eines To-dos (PUT /todo/{id}/status, Body {"status": ...}).
     */
    public function setStatus(ID $id, string $status): bool {
        self::logDebug('Setting todo status', ['id' => $id->toString(), 'status' => $status]);

        return self::logInfoWithTimer(function () use ($id, $status): bool {
            parent::putContents(['status' => $status], [], "{$this->getEndpointUrl()}/{$id->toString()}/status", 200);

            return true;
        }, "Todo status set (ID: {$id->toString()})");
    }
}
