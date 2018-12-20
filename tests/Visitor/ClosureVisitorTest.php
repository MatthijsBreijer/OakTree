<?php
namespace MatthijsBreijer\Tests\OakTree\Node;

use MatthijsBreijer\OakTree\Node\Node;
use MatthijsBreijer\OakTree\Visitor\ClosureVisitor;
use PHPUnit\Framework\TestCase;

class ClosureVisitorTest extends TestCase
{
    /**
     * @var int
     */
    private $closureCalls = 0;


    /**
     * @var MatthijsBreijer\OakTree\Visitor\ClosureVisitor
     */
    private $visitor;

    /**
     * Test that the Closure is called
     * @test
     */
    public function testClosureIsCalled()
    {
        $closure = function() {
                $this->closureCalls++;
            };

	$visitor = new ClosureVisitor($closure);

        $node = new Node();
        $visitor->visit($node);

        $this->assertSame(1, $this->closureCalls);
    }

    /**
     * Test that the Closure is called with correct arguments
     * @test
     */
    public function testClosureContainsNodeAndVisitorArguments()
    {
        $testNode = new Node;

        $closure = function($node, $visitor) use ($testNode) {
                $this->closureCalls++;

                $this->assertSame($this->visitor, $visitor);
                $this->assertSame($testNode, $node);
            };

        $this->visitor = new ClosureVisitor($closure);
        $this->visitor->visit($testNode);

        // assert closure is called so we know assertions in closure are performed
        $this->assertSame(1, $this->closureCalls);
    }

}
