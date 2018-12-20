<?php
namespace MatthijsBreijer\OakTree\Visitor;

use MatthijsBreijer\OakTree\Node\NodeInterface;

/**
 * A visitor that returns an array with only leafs of a tree
 */
class LeafVisitor implements VisitorInterface
{

    /**a
     * @param NodeInterface $node
     * @return NodeInterface[]
     */
    public function visit(NodeInterface $node) : array
    {
	$return = $node->isLeaf() ? [$node] : [];

        foreach ($node->getChildren() as $key => $child) {
            $return = array_merge($return, $child->accept($this));
        }

        return $return;
    }
}
