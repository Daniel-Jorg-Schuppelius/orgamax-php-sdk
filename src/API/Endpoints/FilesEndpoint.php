<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : FilesEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\Contracts\Abstracts\API\PagedEndpointAbstract;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\Files\{File, FileData, FileDataResponse, FileList};

class FilesEndpoint extends PagedEndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'file';

    /**
     * Lädt eine Datei hoch (multipart/form-data).
     *
     * @param string $filePath Pfad zur lokalen Datei
     * @param string|null $kind Belegart, z. B. "Rechnungseingang"
     * @param array<int, string> $tags
     */
    public function upload(string $filePath, ?string $kind = null, array $tags = []): ?FileData {
        if (!is_file($filePath)) {
            self::logErrorAndThrow(InvalidArgumentException::class, "File not found: {$filePath}");
        }
        self::logDebug('Uploading file', ['filePath' => $filePath, 'kind' => $kind, 'tags' => $tags]);

        $contents = file_get_contents($filePath);
        if ($contents === false) {
            self::logErrorAndThrow(InvalidArgumentException::class, "File is not readable: {$filePath}");
        }

        $fields = [];
        if (!is_null($kind)) {
            $fields['kind'] = $kind;
        }

        $parts = [[
            'name' => 'file',
            'contents' => $contents,
            'filename' => basename($filePath),
        ]];
        foreach ($tags as $tag) {
            $parts[] = ['name' => 'tags[]', 'contents' => $tag];
        }

        return self::logInfoWithTimer(function () use ($fields, $parts): ?FileData {
            $response = parent::postMultipart($fields, $parts, [], "{$this->getEndpointUrl()}/upload", 201);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return FileDataResponse::fromJson($response, self::$logger)->getData();
        }, 'File uploaded');
    }

    /**
     * Liefert die Metadaten einer Datei — GET file/{id}/meta antwortet mit
     * einer FileList-Struktur; zurückgegeben wird deren erster Eintrag.
     */
    public function get(?ID $id = null): ?File {
        if (is_null($id)) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'ID is required for getting file meta data');
        }
        self::logDebug('Fetching file meta data', ['id' => $id->toString()]);

        return self::logDebugWithTimer(function () use ($id): ?File {
            $response = parent::getContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}/meta");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return FileList::fromJson($response, self::$logger)->getValues()[0] ?? null;
        }, "File meta data fetched (ID: {$id->toString()})");
    }

    public function search(array $queryParams = [], array $options = []): ?FileList {
        self::logDebug('Searching files', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?FileList {
            $response = parent::getContents($queryParams, $options, $this->getEndpointUrl());
            if (empty($response) || $response === '[]') {
                return null;
            }

            return FileList::fromJson($response, self::$logger);
        }, 'Files search completed');
    }

    /**
     * Lädt den Binärinhalt einer Datei herunter.
     */
    public function download(ID $id, ?string $filename = null): string {
        self::logDebug('Downloading file', ['id' => $id->toString(), 'filename' => $filename]);

        $queryParams = array_filter([
            'filename' => $filename,
        ], fn ($value) => $value !== null);

        return self::logDebugWithTimer(
            fn () => parent::getContents($queryParams, [], "{$this->getEndpointUrl()}/{$id->toString()}"),
            "File downloaded (ID: {$id->toString()})"
        );
    }

    /**
     * Startet die Belegerkennung für eine Datei.
     */
    public function analyze(ID $id): ?FileData {
        self::logDebug('Analyzing file', ['id' => $id->toString()]);

        return self::logDebugWithTimer(function () use ($id): ?FileData {
            $response = parent::getContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}/analyze");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return FileDataResponse::fromJson($response, self::$logger)->getData();
        }, "File analyzed (ID: {$id->toString()})");
    }

    /**
     * Aktualisiert Metadaten einer Datei, z. B. {"name": "...", "tags": [...]},
     * {"customer": {"id": 1}}, {"supplier": {"id": 1}} oder {"receiptNumber": "..."}.
     *
     * @param array<string, mixed> $meta
     */
    public function updateMeta(ID $id, array $meta): ?FileData {
        self::logDebug('Updating file meta data', ['id' => $id->toString(), 'meta' => $meta]);

        return self::logInfoWithTimer(function () use ($id, $meta): ?FileData {
            $response = parent::putContents($meta, [], "{$this->getEndpointUrl()}/{$id->toString()}/meta", 200);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return FileDataResponse::fromJson($response, self::$logger)->getData();
        }, "File meta data updated (ID: {$id->toString()})");
    }

    public function delete(ID $id): bool {
        self::logDebug('Deleting file', ['id' => $id->toString()]);

        return self::logInfoWithTimer(function () use ($id): bool {
            parent::deleteContents([], "{$this->getEndpointUrl()}/{$id->toString()}", 204);

            return true;
        }, "File deleted (ID: {$id->toString()})");
    }
}
