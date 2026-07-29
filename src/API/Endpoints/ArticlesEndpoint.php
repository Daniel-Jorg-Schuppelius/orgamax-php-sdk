<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ArticlesEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\Contracts\Abstracts\API\PagedEndpointAbstract;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\Articles\{Article, ArticleList, ArticleResponse};
use Orgamax\Entities\Common\ResourceResponse;

class ArticlesEndpoint extends PagedEndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'article';

    public function create(Article $data): ResourceResponse {
        if (!$data->isValid()) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'Article data is not valid: title, unit and number are required');
        }
        self::logDebug('Creating article', ['endpoint' => $this->endpoint]);

        return self::logInfoWithTimer(
            fn () => ResourceResponse::fromJson(
                parent::postContents($data->toArray(), [], "{$this->getEndpointUrl()}/", 201),
                self::$logger
            ),
            'Article created'
        );
    }

    public function get(?ID $id = null): ?Article {
        if (is_null($id)) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'ID is required for getting an article');
        }
        self::logDebug('Fetching article', ['id' => $id->toString()]);

        return self::logDebugWithTimer(function () use ($id): ?Article {
            $response = parent::getContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return ArticleResponse::fromJson($response, self::$logger)->getData();
        }, "Article fetched (ID: {$id->toString()})");
    }

    public function search(array $queryParams = [], array $options = []): ?ArticleList {
        self::logDebug('Searching articles', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?ArticleList {
            $response = parent::getContents($queryParams, $options, "{$this->getEndpointUrl()}/");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return ArticleList::fromJson($response, self::$logger);
        }, 'Articles search completed');
    }

    public function update(ID $id, Article $data): ResourceResponse {
        self::logDebug('Updating article', ['id' => $id->toString()]);

        return self::logInfoWithTimer(
            fn () => ResourceResponse::fromJson(
                parent::putContents($data->toArray(), [], "{$this->getEndpointUrl()}/{$id->toString()}", 200),
                self::$logger
            ),
            "Article updated (ID: {$id->toString()})"
        );
    }

    public function delete(ID $id): bool {
        self::logDebug('Deleting article', ['id' => $id->toString()]);

        return self::logInfoWithTimer(function () use ($id): bool {
            parent::deleteContents([], "{$this->getEndpointUrl()}/{$id->toString()}", 204);

            return true;
        }, "Article deleted (ID: {$id->toString()})");
    }
}
