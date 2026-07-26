<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ArticleTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Entities;

use Orgamax\Entities\Articles\Article;
use Orgamax\Enums\CalculationBase;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase {
    public function test_hydration_from_array(): void {
        $article = new Article([
            'id' => 90,
            'number' => '0015',
            'title' => 'My Article',
            'unit' => 'Stk.',
            'calculationBase' => 'gross',
            'priceGross' => 200.25,
            'vatPercent' => 19,
            'notesAlert' => true,
            'graduatedPriceList' => [
                ['quantity' => 1000, 'netUnitPrice' => 40],
                ['quantity' => 2000, 'netUnitPrice' => 30],
            ],
        ]);

        $this->assertSame(90, $article->getId());
        $this->assertSame('0015', $article->getNumber());
        $this->assertSame(CalculationBase::GROSS, $article->getCalculationBase());
        $this->assertSame(200.25, $article->getPriceGross());
        $this->assertTrue($article->getNotesAlert());
        $this->assertCount(2, $article->getGraduatedPriceList() ?? []);
        $this->assertSame(30.0, $article->getGraduatedPriceList()?->getValues()[1]->getNetUnitPrice());
        $this->assertTrue($article->isValid());
    }

    public function test_incomplete_article_is_invalid(): void {
        $article = new Article(['title' => 'My Article']);

        $this->assertFalse($article->isValid());
    }

    public function test_json_serialize(): void {
        $article = new Article([
            'title' => 'My Article',
            'unit' => 'Stk.',
            'number' => '0015',
            'price' => 200.25,
            'vatPercent' => 19,
            'calculationBase' => 'net',
        ]);

        $result = $article->toArray();

        $this->assertSame('My Article', $result['title']);
        $this->assertSame('Stk.', $result['unit']);
        $this->assertSame('0015', $result['number']);
        $this->assertSame('net', $result['calculationBase']);
        $this->assertArrayNotHasKey('id', $result);
        $this->assertArrayNotHasKey('description', $result);
    }
}
