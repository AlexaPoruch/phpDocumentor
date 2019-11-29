<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */
namespace phpDocumentor\Descriptor;

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Tests the functionality for the Collection class.
 */
class CollectionTest extends MockeryTestCase
{
    /** @var Collection $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new Collection();
    }

    /**
     * @covers \phpDocumentor\Descriptor\Collection::__construct
     */
    public function testInitialize() : void
    {
        $fixture = new Collection();

        $this->assertEmpty($fixture->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Collection::__construct
     */
    public function testInitializeWithExistingArray() : void
    {
        $expected = [1, 2];
        $fixture  = new Collection($expected);

        $this->assertEquals($expected, $fixture->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Collection::add
     */
    public function testAddNewItem() : void
    {
        $expected          = ['abc'];
        $expectedSecondRun = ['abc', 'def'];

        $this->fixture->add('abc');

        $this->assertEquals($expected, $this->fixture->getAll());

        $this->fixture->add('def');

        $this->assertEquals($expectedSecondRun, $this->fixture->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Collection::set
     * @covers \phpDocumentor\Descriptor\Collection::offsetSet
     */
    public function testSetItemsWithKey() : void
    {
        $expected          = ['z' => 'abc'];
        $expectedSecondRun = ['z' => 'abc', 'y' => 'def'];

        $this->assertEquals([], $this->fixture->getAll());

        $this->fixture->set('z', 'abc');

        $this->assertEquals($expected, $this->fixture->getAll());

        $this->fixture->set('y', 'def');

        $this->assertEquals($expectedSecondRun, $this->fixture->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Collection::set
     */
    public function testSetItemsWithEmptyKeyShouldThrowException() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->fixture->set('', 'abc');
    }

    /**
     * @covers \phpDocumentor\Descriptor\Collection::offsetSet
     */
    public function testSetItemsUsingOffsetSetWithEmptyKeyShouldThrowException() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->fixture->offsetSet('', 'abc');
    }

    /**
     * @covers \phpDocumentor\Descriptor\Collection::get
     * @covers \phpDocumentor\Descriptor\Collection::__get
     * @covers \phpDocumentor\Descriptor\Collection::offsetGet
     */
    public function testRetrievalOfItems() : void
    {
        $this->fixture['a'] = 'abc';
        $this->assertEquals('abc', $this->fixture->__get('a'));
        $this->assertEquals('abc', $this->fixture['a']);
        $this->assertEquals('abc', $this->fixture->get('a'));
        $this->assertCount(1, $this->fixture);

        $this->assertEquals('def', $this->fixture->get(1, 'def'));
        $this->assertCount(2, $this->fixture);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Collection::getAll
     */
    public function testRetrieveAllItems() : void
    {
        $this->fixture['a'] = 'abc';
        $this->assertSame(['a' => 'abc'], $this->fixture->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Collection::getIterator
     */
    public function testGetIterator() : void
    {
        $this->fixture['a'] = 'abc';
        $this->assertInstanceOf('ArrayIterator', $this->fixture->getIterator());
        $this->assertSame(['a' => 'abc'], $this->fixture->getIterator()->getArrayCopy());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Collection::count
     * @covers \phpDocumentor\Descriptor\Collection::offsetUnset
     */
    public function testCountReturnsTheNumberOfElements() : void
    {
        $this->assertCount(0, $this->fixture);
        $this->assertEquals(0, $this->fixture->count());

        $this->fixture[0] = 'abc';

        $this->assertCount(1, $this->fixture);
        $this->assertEquals(1, $this->fixture->count());

        $this->fixture[1] = 'def';

        $this->assertCount(2, $this->fixture);
        $this->assertEquals(2, $this->fixture->count());

        unset($this->fixture[0]);

        $this->assertCount(1, $this->fixture);
        $this->assertEquals(1, $this->fixture->count());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Collection::clear
     */
    public function testClearingTheCollection() : void
    {
        $this->fixture[1] = 'a';
        $this->fixture[2] = 'b';

        $this->assertCount(2, $this->fixture);

        $this->fixture->clear();

        $this->assertCount(0, $this->fixture);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Collection::offsetExists
     */
    public function testIfExistingElementsAreDetected() : void
    {
        $this->assertArrayNotHasKey(0, $this->fixture);
        $this->assertFalse($this->fixture->offsetExists(0));

        $this->fixture[0] = 'abc';

        $this->assertArrayHasKey(0, $this->fixture);
        $this->assertTrue($this->fixture->offsetExists(0));
    }

    /**
     * @covers \phpDocumentor\Descriptor\Collection::merge
     */
    public function testIfAfterMergeCollectionContainsAllItems() : void
    {
        $expected         = [0 => 'a', 1 => 'b', 2 => 'c'];
        $this->fixture[1] = 'a';
        $this->fixture[2] = 'b';

        $collection2    = new Collection();
        $collection2[4] = 'c';

        $result = $this->fixture->merge($collection2);

        $this->assertSame($expected, $result->getAll());
    }
}
