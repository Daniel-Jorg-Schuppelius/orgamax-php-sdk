<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : InvoicesEndpoint.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Endpoints;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\Contracts\Abstracts\API\DocumentEndpointAbstract;
use Orgamax\Contracts\Interfaces\API\SearchableEndpointInterface;
use Orgamax\Entities\Invoices\{Invoice, InvoiceList, InvoiceLockInfo, InvoiceLockResponse, InvoicePayment, InvoicePayments, InvoiceResponse};

class InvoicesEndpoint extends DocumentEndpointAbstract implements SearchableEndpointInterface {
    protected string $endpoint = 'invoice';

    public function get(?ID $id = null): ?Invoice {
        if (is_null($id)) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'ID is required for getting an invoice');
        }
        self::logDebug('Fetching invoice', ['id' => $id->toString()]);

        return self::logDebugWithTimer(function () use ($id): ?Invoice {
            $response = parent::getContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}");
            if (empty($response) || $response === '[]') {
                return null;
            }

            return InvoiceResponse::fromJson($response, self::$logger)->getData();
        }, "Invoice fetched (ID: {$id->toString()})");
    }

    public function search(array $queryParams = [], array $options = []): ?InvoiceList {
        self::logDebug('Searching invoices', ['queryParams' => $queryParams]);

        return self::logDebugWithTimer(function () use ($queryParams, $options): ?InvoiceList {
            $response = parent::getContents($queryParams, $options);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return InvoiceList::fromJson($response, self::$logger);
        }, 'Invoices search completed');
    }

    /**
     * Erfasst eine Zahlung zur Rechnung; die API antwortet mit allen
     * Zahlungen der Rechnung als nacktem JSON-Array.
     */
    public function addPayment(ID $id, InvoicePayment $payment): ?InvoicePayments {
        if (!$payment->isValid()) {
            self::logErrorAndThrow(InvalidArgumentException::class, 'Invoice payment is not valid: amount, type and date are required');
        }
        self::logDebug('Adding payment to invoice', ['id' => $id->toString()]);

        return self::logInfoWithTimer(function () use ($id, $payment): ?InvoicePayments {
            $response = parent::postContents($payment->toArray(), [], "{$this->getEndpointUrl()}/{$id->toString()}/payment", 200);
            if (empty($response) || $response === '[]') {
                return null;
            }

            return InvoicePayments::fromJson($response, self::$logger);
        }, "Invoice payment added (ID: {$id->toString()})");
    }

    /**
     * Sperrt und finalisiert die Rechnung (kein Request-Body).
     */
    public function lock(ID $id): ?InvoiceLockInfo {
        self::logDebug('Locking invoice', ['id' => $id->toString()]);

        return self::logInfoWithTimer(
            fn (): ?InvoiceLockInfo => InvoiceLockResponse::fromJson(
                parent::putContents([], [], "{$this->getEndpointUrl()}/{$id->toString()}/lock", 200),
                self::$logger
            )->getData(),
            "Invoice locked (ID: {$id->toString()})"
        );
    }

    /**
     * Versendet die Rechnung per E-Mail; die API antwortet mit text/plain "Ok".
     *
     * @param array<int, string> $recipients
     */
    public function send(ID $id, array $recipients, string $subject, ?string $attachmentName = null): bool {
        self::logDebug('Sending invoice', ['id' => $id->toString(), 'recipients' => $recipients]);

        $data = [
            'recipients' => $recipients,
            'subject' => $subject,
        ];
        if (!is_null($attachmentName)) {
            $data['attachmentName'] = $attachmentName;
        }

        return self::logInfoWithTimer(function () use ($id, $data): bool {
            parent::postContents($data, [], "{$this->getEndpointUrl()}/{$id->toString()}/send", 200);

            return true;
        }, "Invoice sent (ID: {$id->toString()})");
    }

    /**
     * Lädt das Rechnungs-PDF als Binärstring herunter.
     *
     * @deprecated Die API-Route GET /invoice/{id}/download ist veraltet —
     *             stattdessen document() verwenden (GET /invoice/document/{id}).
     */
    public function download(ID $id): string {
        self::logDebug('Downloading invoice (deprecated route)', ['id' => $id->toString()]);

        return self::logDebugWithTimer(
            fn (): string => parent::getContents(
                [],
                ['headers' => ['Accept' => 'application/pdf']],
                "{$this->getEndpointUrl()}/{$id->toString()}/download"
            ),
            "Invoice downloaded (ID: {$id->toString()})"
        );
    }
}
