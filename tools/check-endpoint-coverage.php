#!/usr/bin/env php
<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : check-endpoint-coverage.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

/**
 * Vergleicht die Operationen der OpenAPI-Spec (docs/OpenAPI/orgamax-openapi.json)
 * mit den im SDK implementierten Endpoint-Klassen (src/API/Endpoints).
 *
 * Heuristik: eine Operation gilt als abgedeckt, wenn eine Endpoint-Klasse das
 * erste Pfadsegment als $endpoint-Property (oder als urlPath-Literal) verwendet
 * und die HTTP-Methode über die zugehörigen parent-Helfer (getContents,
 * postContents, putContents, deleteContents, postMultipart) aufruft.
 */
$root = dirname(__DIR__);
$specFile = $root . '/docs/OpenAPI/orgamax-openapi.json';
$endpointDir = $root . '/src/API/Endpoints';

if (!file_exists($specFile)) {
    fwrite(STDERR, "Spec nicht gefunden: {$specFile}\n");
    exit(1);
}

$specRaw = file_get_contents($specFile);
if ($specRaw === false) {
    fwrite(STDERR, "Spec nicht lesbar: {$specFile}\n");
    exit(1);
}

/** @var array<string, mixed> $spec */
$spec = json_decode($specRaw, true);
if (!is_array($spec) || !isset($spec['paths']) || !is_array($spec['paths'])) {
    fwrite(STDERR, "Ungültige Spec: {$specFile}\n");
    exit(1);
}

// Quellcode aller Endpoint-Klassen einsammeln
$sources = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($endpointDir));
foreach ($iterator as $fileInfo) {
    if ($fileInfo instanceof SplFileInfo && $fileInfo->isFile() && $fileInfo->getExtension() === 'php') {
        $content = file_get_contents($fileInfo->getPathname());
        if ($content !== false) {
            $sources[$fileInfo->getPathname()] = $content;
        }
    }
}
$allSource = implode("\n", $sources);

$methodMarkers = [
    'get' => ['getContents', 'getArray', 'getEntity', 'document('],
    'post' => ['postContents', 'postMultipart', 'postEntity'],
    'put' => ['putContents', 'putEntity'],
    'patch' => ['patchContents', 'patchEntity'],
    'delete' => ['deleteContents'],
];

$covered = [];
$missing = [];

foreach ($spec['paths'] as $path => $operations) {
    if (!is_array($operations)) {
        continue;
    }
    // erstes Pfadsegment, z. B. "article" aus "/article/{id}"
    $segments = array_values(array_filter(explode('/', (string) $path)));
    $first = $segments[0] ?? '';
    $prefix = $first === 'setting' && isset($segments[1]) && !str_contains($segments[1], '{')
        ? "setting/{$segments[1]}"
        : $first;

    foreach ($operations as $httpMethod => $operation) {
        if (!in_array($httpMethod, ['get', 'post', 'put', 'patch', 'delete'], true)) {
            continue;
        }

        $hasEndpoint = str_contains($allSource, "'{$prefix}'")
            || str_contains($allSource, "\"{$prefix}\"")
            || str_contains($allSource, "'{$prefix}/")
            || str_contains($allSource, "\"{$prefix}/");
        $hasMethod = false;
        foreach ($methodMarkers[$httpMethod] as $marker) {
            if (str_contains($allSource, $marker)) {
                $hasMethod = true;
                break;
            }
        }

        $label = strtoupper((string) $httpMethod) . ' ' . $path;
        if ($hasEndpoint && $hasMethod) {
            $covered[] = $label;
        } else {
            $missing[] = $label;
        }
    }
}

echo "== orgaMAX Endpoint-Coverage ==\n\n";
echo 'Abgedeckt: ' . count($covered) . ' / ' . (count($covered) + count($missing)) . " Operationen\n\n";

if (!empty($missing)) {
    echo "Ohne erkennbare Implementierung:\n";
    foreach ($missing as $label) {
        echo "  - {$label}\n";
    }
    exit(1);
}

echo "Alle Operationen der Spec sind (heuristisch) abgedeckt.\n";
exit(0);
