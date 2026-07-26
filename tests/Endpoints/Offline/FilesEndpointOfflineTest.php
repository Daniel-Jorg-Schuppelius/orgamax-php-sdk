<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : FilesEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\API\Endpoints\FilesEndpoint;
use Orgamax\Entities\Files\{File, FileData, FileList};
use Tests\Contracts\OfflineEndpointTest;

class FilesEndpointOfflineTest extends OfflineEndpointTest {
    private const PDF_CONTENT = "%PDF-1.4 binary content";

    private FilesEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new FilesEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        $uploadBody = json_encode([
            'meta' => [],
            'data' => [
                'giniDocId' => 'gini-4711',
                'receiptNumber' => '1234abc',
                'date' => '2026-07-01',
                'priceTotal' => 119.0,
                'vatRate' => 19,
                'payee' => 'Muster GmbH',
            ],
        ]);
        $this->assertNotFalse($uploadBody);
        $this->mockClient->addResponse('POST', 'file/upload', 201, $uploadBody);

        $metaBody = json_encode([
            'meta' => [
                'count' => 1,
                'totalCount' => 1,
            ],
            'data' => [
                [
                    'id' => 12,
                    'recordDate' => '2026-07-01 10:15:00',
                    'mimetyp' => 'application/pdf',
                    'size' => '2048',
                    'metadata' => ['height' => 842.0, 'rotate' => 0.0, 'scale' => 1.0, 'width' => 595.0],
                ],
            ],
        ]);
        $this->assertNotFalse($metaBody);
        $this->mockClient->addResponse('GET', 'file/12/meta', 200, $metaBody);

        $analyzeBody = json_encode([
            'data' => [
                'giniDocId' => 'gini-4711',
                'receiptNumber' => 'RE-2026-0815',
                'date' => '2026-06-30',
                'priceTotal' => 238.0,
                'vatRate' => 19,
                'payee' => 'Muster GmbH',
                'payeeStreet' => 'Musterstr. 1',
                'payeeCity' => 'Musterstadt',
                'payeeZipCode' => '12345',
                'payeeIban' => 'DE02120300000000202051',
                'payeeBic' => 'BYLADEM1001',
            ],
        ]);
        $this->assertNotFalse($analyzeBody);
        $this->mockClient->addResponse('GET', 'file/12/analyze', 200, $analyzeBody);

        $updateBody = json_encode([
            'data' => [
                'receiptNumber' => '1234abc',
            ],
        ]);
        $this->assertNotFalse($updateBody);
        $this->mockClient->addResponse('PUT', 'file/12/meta', 200, $updateBody);

        // Download (GET file/12, optional mit filename-Query) — nach den
        // spezifischeren meta-/analyze-Patterns registrieren.
        $this->mockClient->addResponse('GET', 'file/12*', 200, self::PDF_CONTENT, ['Content-Type' => 'application/pdf']);

        $listBody = json_encode([
            'meta' => [
                'count' => 2,
                'totalCount' => 15,
            ],
            'data' => [
                ['id' => 12, 'recordDate' => '2026-07-01 10:15:00', 'mimetyp' => 'application/pdf', 'size' => '2048'],
                ['id' => 13, 'recordDate' => '2026-07-02 09:00:00', 'mimetyp' => 'image/png', 'size' => '512'],
            ],
        ]);
        $this->assertNotFalse($listBody);
        // Query-Parameter werden an die URI angehängt, daher Wildcard.
        $this->mockClient->addResponse('GET', 'file*', 200, $listBody);

        $this->mockClient->addResponse('DELETE', 'file/12', 204, '');
    }

    public function test_upload_file(): void {
        $fixture = __DIR__ . '/../../Fixtures/sample.txt';

        $result = $this->endpoint->upload($fixture, 'Rechnungseingang', ['#foo', '#bar']);

        $this->assertInstanceOf(FileData::class, $result);
        $this->assertSame('gini-4711', $result->getGiniDocId());
        $this->assertSame('1234abc', $result->getReceiptNumber());
        $this->assertRequestMade('POST', 'file/upload');

        $lastRequest = $this->mockClient->getLastRequest();
        $this->assertNotNull($lastRequest);
        $this->assertArrayHasKey('multipart', $lastRequest['options']);
        $multipart = $lastRequest['options']['multipart'];
        $this->assertIsArray($multipart);

        $names = array_column($multipart, 'name');
        $this->assertContains('kind', $names);
        $this->assertContains('file', $names);
        $this->assertSame(['tags[]', 'tags[]'], array_values(array_filter($names, fn ($name) => $name === 'tags[]')));

        foreach ($multipart as $part) {
            if ($part['name'] === 'kind') {
                $this->assertSame('Rechnungseingang', $part['contents']);
            }
            if ($part['name'] === 'file') {
                $this->assertSame('sample.txt', $part['filename']);
                $this->assertStringContainsString('orgaMAX', $part['contents']);
            }
        }
    }

    public function test_upload_missing_file_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->upload('/path/does/not/exist.pdf');
        $this->assertNoRequestsMade();
    }

    public function test_get_file_meta(): void {
        $file = $this->endpoint->get(new ID(12));

        $this->assertInstanceOf(File::class, $file);
        $this->assertSame(12, $file->getId());
        $this->assertSame('application/pdf', $file->getMimetyp());
        $this->assertSame('2048', $file->getSize());
        $this->assertSame('2026-07-01', $file->getRecordDate()?->format('Y-m-d'));
        $metadata = $file->getMetadata();
        $this->assertNotNull($metadata);
        $this->assertSame(842.0, $metadata->getHeight());
        $this->assertSame(595.0, $metadata->getWidth());
        $this->assertRequestMade('GET', 'file/12/meta');
    }

    public function test_get_file_without_id_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->get();
    }

    public function test_search_files(): void {
        $result = $this->endpoint->search(['limit' => 2]);

        $this->assertInstanceOf(FileList::class, $result);
        $this->assertEquals(2, $result->getMeta()?->getCount());
        $this->assertEquals(15, $result->getMeta()?->getTotalCount());
        $this->assertCount(2, $result->getValues());
        $this->assertSame('image/png', $result->getValues()[1]->getMimetyp());
        $this->assertRequestMade('GET', 'file?limit=2');
    }

    public function test_download_file(): void {
        $content = $this->endpoint->download(new ID(12), 'invoice.pdf');

        $this->assertSame(self::PDF_CONTENT, $content);
        $this->assertRequestMade('GET', 'file/12?filename=invoice.pdf');
    }

    public function test_download_file_without_filename(): void {
        $content = $this->endpoint->download(new ID(12));

        $this->assertSame(self::PDF_CONTENT, $content);
        $this->assertRequestMade('GET', 'file/12');
    }

    public function test_analyze_file(): void {
        $result = $this->endpoint->analyze(new ID(12));

        $this->assertInstanceOf(FileData::class, $result);
        $this->assertSame('RE-2026-0815', $result->getReceiptNumber());
        $this->assertSame(238.0, $result->getPriceTotal());
        $this->assertSame(19, $result->getVatRate());
        $this->assertSame('Muster GmbH', $result->getPayee());
        $this->assertSame('DE02120300000000202051', $result->getPayeeIban());
        $this->assertRequestMade('GET', 'file/12/analyze');
    }

    public function test_update_file_meta(): void {
        $result = $this->endpoint->updateMeta(new ID(12), [
            'name' => 'fileName.pdf',
            'tags' => ['#foo', '#bar'],
        ]);

        $this->assertInstanceOf(FileData::class, $result);
        $this->assertSame('1234abc', $result->getReceiptNumber());
        $this->assertRequestMade('PUT', 'file/12/meta');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame('fileName.pdf', $payload['name']);
        $this->assertSame(['#foo', '#bar'], $payload['tags']);
    }

    public function test_update_file_meta_with_customer(): void {
        $result = $this->endpoint->updateMeta(new ID(12), ['customer' => ['id' => 1]]);

        $this->assertInstanceOf(FileData::class, $result);
        $this->assertRequestMade('PUT', 'file/12/meta');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame(['id' => 1], $payload['customer']);
    }

    public function test_delete_file(): void {
        $this->assertTrue($this->endpoint->delete(new ID(12)));
        $this->assertRequestMade('DELETE', 'file/12');
    }
}
