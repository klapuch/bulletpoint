<?php

/**
 * @testCase
 * @phpVersion > 7.0.0
 */

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Wiki;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Model\Access;

require __DIR__ . '/../../../bootstrap.php';

final class CachedDocument extends TestCase\Mockery {
    private $cache;

    public function setUp() {
        parent::setUp();
        $this->cache = $this->mockery('Nette\Caching\IStorage');
    }

    public function testCaching() {
        $this->cache->shouldReceive('read')
            ->andReturn('<Any>')
            ->times(4)
            ->with('Bulletpoint\Model\Wiki\CachedDocument::description');
        $this->cache->shouldReceive('read')
            ->andReturn('<Any>')
            ->times(4)
            ->with('Bulletpoint\Model\Wiki\CachedDocument::title');
        $this->cache->shouldReceive('read')
            ->andReturn(new \DateTime('2000'))
            ->times(4)
            ->with('Bulletpoint\Model\Wiki\CachedDocument::date');
        $this->cache->shouldReceive('read')
            ->andReturn(new Fake\Identity(10))
            ->times(4)
            ->with('Bulletpoint\Model\Wiki\CachedDocument::author');
        $this->cache->shouldReceive('read')
            ->andReturn(new Fake\InformationSource(10))
            ->times(4)
            ->with('Bulletpoint\Model\Wiki\CachedDocument::source');
        $this->cache->shouldReceive('read')
            ->never();
        $this->cache->shouldReceive('write')->never();
        $document = new Wiki\CachedDocument(new Fake\Document(1), $this->cache);

        Assert::same('<Any>', $document->description());
        Assert::same('<Any>', $document->description());

        Assert::same('<Any>', $document->title());
        Assert::same('<Any>', $document->title());


        Assert::equal(new \DateTime('2000'), $document->date());
        Assert::equal(new \DateTime('2000'), $document->date());

        Assert::equal(new Fake\Identity(10), $document->author());
        Assert::equal(new Fake\Identity(10), $document->author());

        Assert::equal(new Fake\InformationSource(10), $document->source());
        Assert::equal(new Fake\InformationSource(10), $document->source());

        Assert::same(1, $document->id());
        Assert::same(1, $document->id());

        Assert::true(true);
    }
}


(new CachedDocument())->run();
