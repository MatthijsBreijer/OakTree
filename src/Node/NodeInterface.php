<?php
namespace MatthijsBreijer\OakTree\Node;

use MatthijsBreijer\OakTree\Visitor\VisitorInterface;

interface NodeInterface extends \JsonSerializable
{

    /**
     * @param mixed $value
     * @param NodeInterface[] $children 
     */
    public function __construct($value, array $children = []);

    /**
     * Add a child to node, automatically sets parent too
     * @param NodeInterface $child
     * @return NodeInterface self for fluency
     */
    public function addChild(NodeInterface $child) : NodeInterface;

    /**
     * Set the parent of a node
     * @param NodeInterface $parent
     * @return NodeInterface for fluency
     */
    public function setParent(NodeInterface $parent = null) : NodeInterface;

    /**
     * Get children of a node
     * @return NodeInterface[]
     */
    public function getChildren() : array;

    /**
     * Get keys of the child nodes
     * @return mixed[]
     */
    public function getChildrenKeys() : array;

    /**
     * Get the child Node by array key
     * @param mixed $key
     * @return NodeInterface|null
     * @throws \OutOfBoundsException
     */
    public function getChildByKey($key);

    /**
     * Set the children of a Node, array keys are kept within the tree
     * @param NodeInterface[] $children
     * @return NodeInterface The Node children are added to for fluency
     */
    public function setChildren(array $children) : NodeInterface;

    /**
     * Remove all child Nodes from current Node
     * @return NodeInterface
     */
    public function removeAllChildren() : NodeInterface;

    /**
     * Get parent of a Node
     * @return null|NodeInterface
     */
    public function getParent();

    /**
     * Get the root node of a tree
     * @return NodeInterface
     */
    public function getRoot() : NodeInterface;

    /**
     * Set the value stored in the Node
     * @return NodeInterface for fluency
     */
    public function setValue($value) : NodeInterface;

    /**
     * Get the value stored in the Node
     * @return mixed
     */
    public function getValue();

    /**
     * Remove child from tree
     * @param NodeInterface $child
     * @return NodeInterface for fluency
     */
    public function removeChild(NodeInterface $child) : NodeInterface;

    /**
     * Remove a Node from a tree and return the removed Node instance
     * @return NodeInterface
     */
    public function pop() : NodeInterface;

    /**
     * Is Node a child of another node?
     * @return boolean
     */
    public function isChild() : bool;

    /**
     * Is Node the root of the tree (without a parent)?
     * @return boolean
     */
    public function isRoot() : bool;

    /**
     * Is the Node a leaf (without children)?
     * @return boolean
     */
    public function isLeaf() : bool;

    /**
     * Convert node (and children) to array
     * @param Closure|null $serializer optional callback to serialize Node value
     * @return array
     */
    public function toArray(\Closure $serializer = null) : array;

    /**
     * Convert array structure to Node(s)
     * @param array $array
     * @param Closure|null $unserializer optional callback to unserialize Node value
     * @return NodeInterface
     */
    public static function fromArray(array $array, \Closure $unserializer = null) : NodeInterface;

    /**
     * Convert data structure (including children) to array
     * so it can be JSON-serialized
     * @return array
     */
    public function jsonSerialize() : array;

    /**
     * Alter the tree with a Visitor
     * @param MatthijsBreijer\OakTree\Visitor\VisitorInterface
     */
    public function accept(VisitorInterface $visitor);

}
