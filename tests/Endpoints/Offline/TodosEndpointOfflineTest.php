<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : TodosEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\API\Endpoints\TodosEndpoint;
use Orgamax\Entities\Todos\{TodoList, TodoMessageList, TodoRelations, Todos};
use Orgamax\Enums\TodoRelationType;
use Tests\Contracts\OfflineEndpointTest;

class TodosEndpointOfflineTest extends OfflineEndpointTest {
    private TodosEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new TodosEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        // POST /todo/ antwortet laut Spec mit 200 und einem Array von TodoData.
        $createBody = json_encode([
            [
                'id' => 7,
                'content' => 'My personal To-Do',
                'dueToDate' => '2020-02-02',
                'tenantId' => '1',
                'userId' => '2',
                'appData' => ['appName' => 'orgaMAX', 'appId' => 'app-1'],
                'creator' => ['firstName' => 'Max', 'lastName' => 'Mustermann'],
                'createdAt' => '2026-07-26T10:00:00+02:00',
                'updatedAt' => '2026-07-26T10:00:00+02:00',
            ],
        ]);
        $this->assertNotFalse($createBody);
        $this->mockClient->addResponse('POST', 'todo/', 200, $createBody);

        // GET /todo/{id} liefert die Nachrichten des To-dos.
        $messagesBody = json_encode([
            'meta' => ['count' => 2],
            'data' => [
                ['id' => 12, 'todoId' => 7, 'tenantd' => 1, 'creatorId' => 2, 'message' => 'Lorem ipsum dolor sit amet', 'type' => 'message'],
                ['id' => 13, 'todoId' => 7, 'tenantd' => 1, 'creatorId' => 2, 'message' => 'Second message', 'type' => 'message'],
            ],
        ]);
        $this->assertNotFalse($messagesBody);
        $this->mockClient->addResponse('GET', 'todo/7', 200, $messagesBody);

        $listBody = json_encode([
            'meta' => [
                'count' => 2,
                'filter' => ['all' => [], 'done' => [], 'future' => [], 'overdue' => [], 'my' => []],
            ],
            'data' => [
                ['id' => 7, 'date' => '2020-02-02'],
                ['id' => 8, 'date' => '2021-06-10'],
            ],
        ]);
        $this->assertNotFalse($listBody);
        // Query-Parameter werden an die URI angehängt, daher Wildcard.
        // GET todo/7 ist zuvor registriert und matcht weiterhin zuerst.
        $this->mockClient->addResponse('GET', 'todo*', 200, $listBody);

        $this->mockClient->addResponse('DELETE', 'todo/7', 204, '');

        // PUT /todo/{id} (Reset due date) antwortet mit 200 ohne Body.
        $this->mockClient->addResponse('PUT', 'todo/7', 200, '');

        $createMessageBody = json_encode([
            ['id' => 7, 'content' => 'My personal To-Do', 'dueToDate' => '2020-02-02'],
        ]);
        $this->assertNotFalse($createMessageBody);
        $this->mockClient->addResponse('POST', 'todo/7/message', 200, $createMessageBody);

        // Spec-Kuriosität: Nachricht löschen per POST, Antwort 200 ohne Body.
        $this->mockClient->addResponse('POST', 'todo/message/12', 200, '');

        $linkBody = json_encode(['meta' => [], 'data' => ['id' => 7]]);
        $this->assertNotFalse($linkBody);
        $this->mockClient->addResponse('PUT', 'todo/7/link', 200, $linkBody);
        $this->mockClient->addResponse('PUT', 'todo/7/unlink', 200, $linkBody);

        $statusBody = json_encode(['meta' => [], 'data' => ['id' => 7, 'doneAt' => '2026-07-26']]);
        $this->assertNotFalse($statusBody);
        $this->mockClient->addResponse('PUT', 'todo/7/status', 200, $statusBody);
    }

    public function test_create_todo(): void {
        $result = $this->endpoint->create('My personal To-Do', '2020-02-02', [1, 21, 45], [2, 7, 9], [3, 6, 8]);

        $this->assertInstanceOf(Todos::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals(7, $result->getFirstValue()?->getId());
        $this->assertEquals('My personal To-Do', $result->getFirstValue()?->getContent());
        $this->assertEquals('Max', $result->getFirstValue()?->getCreator()?->getFirstName());
        $this->assertEquals('orgaMAX', $result->getFirstValue()?->getAppData()?->getAppName());
        $this->assertRequestMade('POST', 'todo/');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame('My personal To-Do', $payload['content']);
        $this->assertSame('2020-02-02', $payload['dueToDate']);
        $this->assertSame([1, 21, 45], $payload['customerIds']);
        $this->assertSame([2, 7, 9], $payload['userIds']);
        $this->assertSame([3, 6, 8], $payload['supplierIds']);
    }

    public function test_create_minimal_todo_omits_relation_ids(): void {
        $this->endpoint->create('My To-do text', '2021-06-10');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame('My To-do text', $payload['content']);
        $this->assertArrayNotHasKey('customerIds', $payload);
        $this->assertArrayNotHasKey('userIds', $payload);
        $this->assertArrayNotHasKey('supplierIds', $payload);
    }

    public function test_get_todo_returns_messages(): void {
        $result = $this->endpoint->get(new ID(7));

        $this->assertInstanceOf(TodoMessageList::class, $result);
        $this->assertEquals(2, $result->getMeta()?->getCount());
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('Lorem ipsum dolor sit amet', $result->getValues()[0]->getMessage());
        $this->assertEquals(7, $result->getValues()[0]->getTodoId());
        $this->assertEquals(1, $result->getValues()[0]->getTenantd());
        $this->assertRequestMade('GET', 'todo/7');
    }

    public function test_get_without_id_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->get();
    }

    public function test_search_todos(): void {
        $result = $this->endpoint->search(['limit' => 2, 'activeFilter' => 'my']);

        $this->assertInstanceOf(TodoList::class, $result);
        $this->assertEquals(2, $result->getMeta()?->getCount());
        $this->assertCount(2, $result->getValues());
        $this->assertEquals(8, $result->getValues()[1]->getId());
        $this->assertEquals('2021-06-10', $result->getValues()[1]->getDate());
        $this->assertRequestMade('GET', 'todo*');
    }

    public function test_delete_todo(): void {
        $this->assertTrue($this->endpoint->delete(new ID(7)));
        $this->assertRequestMade('DELETE', 'todo/7');
    }

    public function test_reset_due_date(): void {
        $this->assertTrue($this->endpoint->resetDueDate(new ID(7), '2020-02-02'));
        $this->assertRequestMade('PUT', 'todo/7');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame(['dueToDate' => '2020-02-02'], $payload);
    }

    public function test_create_message(): void {
        $result = $this->endpoint->createMessage(new ID(7), 'Lorem ipsum dolor sit amet');

        $this->assertInstanceOf(Todos::class, $result);
        $this->assertEquals(7, $result->getFirstValue()?->getId());
        $this->assertRequestMade('POST', 'todo/7/message');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame(['message' => 'Lorem ipsum dolor sit amet'], $payload);
    }

    public function test_delete_message(): void {
        $this->assertTrue($this->endpoint->deleteMessage(new ID(12)));
        $this->assertRequestMade('POST', 'todo/message/12');
    }

    public function test_link_todo(): void {
        $relations = new TodoRelations([
            ['id' => 1, 'type' => 'customer'],
            ['id' => 3, 'type' => 'file'],
        ]);

        $this->assertTrue($this->endpoint->link(new ID(7), $relations));
        $this->assertRequestMade('PUT', 'todo/7/link');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame([
            ['id' => 1, 'type' => 'customer'],
            ['id' => 3, 'type' => 'file'],
        ], $payload);
    }

    public function test_unlink_todo(): void {
        $relations = new TodoRelations([
            ['id' => 1, 'type' => TodoRelationType::CUSTOMER->value],
        ]);

        $this->assertTrue($this->endpoint->unlink(new ID(7), $relations));
        $this->assertRequestMade('PUT', 'todo/7/unlink');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame([['id' => 1, 'type' => 'customer']], $payload);
    }

    public function test_set_status(): void {
        $this->assertTrue($this->endpoint->setStatus(new ID(7), 'done'));
        $this->assertRequestMade('PUT', 'todo/7/status');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame(['status' => 'done'], $payload);
    }
}
