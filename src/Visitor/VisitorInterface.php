<?php
namespace MatthijsBreijer\OakTree\Visitor;

use MatthijsBreijer\OakTree\Node\NodeInterface;

interface VisitorInterface
{
    /**
     * @param NodeInterface $node
     * @return mixed
     */
    public function visit(NodeInterface $node);
}
