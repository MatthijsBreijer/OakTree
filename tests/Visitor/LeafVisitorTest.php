<?php
namespace MatthijsBreijer\Tests\OakTree\Node;

use MatthijsBreijer\OakTree\Node\Node;
use MatthijsBreijer\OakTree\Visitor\LeafVisitor;
use PHPUnit\Framework\TestCase;

class LeafVisitorTest extends TestCase
{
    /**
     * @var MatthijsBreijer\OakTree\Visitor\LeafVisitor
     */
    private $visitor;

    protected function setUp()
    {
        $this->visitor = new LeafVisitor;
    }

    /**
     * Test if a single node (and thus a leaf) is returned
     * @test
     */
    public function testOneNode()
    {
        $node = new Node();

        $expected = [$node];
        $result = $this->visitor->visit($node);

        $this->assertSame($expected, $result);
    }

    /**
     * Test all leafs of a tree are returned
     * Root
     * |- A
     * |- B
     * |  |- C
     * |  \- D
     * |     \- E
     * \- F
     *
     * Starting from root expects [A, C, E, F]
     * @test
     */
    public function testTree()
    {
        $root = (new Node('root'))
            ->addChild($A = new Node('A'))
            ->addChild($B = new Node('B'))
            ->addChild($F = new Node('F'));

        $B->addChild($C = new Node('C'))
            ->addChild($D = new Node('D'));

        $D->addChild($E = new Node('E'));

	$expected = [$A, $C, $E, $F];
        $this->assertSame($expected, $this->visitor->visit($root));
    }

    /**
     * Test all leafs of a subtree are returned
     * Root
     * |- A
     * |- B
     * |  |- C
     * |  \- D
     * |     \- E
     * \- F
     *
     * Starting from B expects [C, E]
     * @test
     */
    public function testSubTree()
    {
        $root = (new Node('root'))
            ->addChild($A = new Node('A'))
            ->addChild($B = new Node('B'))
            ->addChild($F = new Node('F'));

        $B->addChild($C = new Node('C'))
            ->addChild($D = new Node('D'));

        $D->addChild($E = new Node('E'));

        $expected = [$C, $E];
        $this->assertSame($expected, $this->visitor->visit($B));
    }
}
