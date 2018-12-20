<?php
namespace MatthijsBreijer\OakTree\Visitor;

use MatthijsBreijer\OakTree\Node\NodeInterface;

/**
 * A Closure based Visitor
 */
class ClosureVisitor implements VisitorInterface
{

    /**
     * @var \Closure
     */
    private $closure;

    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**a
     * @param NodeInterface $node
     * @return mixed
     */
    public function visit(NodeInterface $node) {
        // Can not call Closure directly from instance property
        $closure = $this->closure;

        // A callback cannot self-reference the visitor.
        // We  pass it to closure here for recursive operations
	return $closure($node, $this);
    }
}
