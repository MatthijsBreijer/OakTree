<?php
namespace MatthijsBreijer\OakTree\Node;

use MatthijsBreijer\OakTree\Visitor\VisitorInterface;

class Node implements NodeInterface
{
    /**
     * @var null|NodeInterface
     */
    private $parent = null;

    /**
     * Child nodes
     * @var NodeInterface[]
     */
    private $children = [];

    /**
     * @var mixed
     */
    private $value = null;

    /**
     * {@inheritdoc}
     */
    public function __construct($value = null, array $children = [])
    {
        $this->setValue($value);
        $this->setChildren($children);
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(NodeInterface $child, $key = null) : NodeInterface
    {
        if (is_null($key)) {
            $this->children[] = $child;
        }
        else {
            $this->children[$key] = $child;
        }
        
        $child->setParent($this);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(NodeInterface $parent = null) : NodeInterface
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren() : array
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildrenKeys() : array
    {
        return array_keys($this->children);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildByKey($key)
    {
        if (!array_key_exists($key, $this->children)) {
	    throw new \OutOfBoundsException('Key `' . $key . '` does not exist');

        }

        return $this->children[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function setChildren(array $children) : NodeInterface
    {
        $this->removeAllChildren();
        foreach ($children as $key => $child) {
            $this->addChild($child, $key);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAllChildren() : NodeInterface
    {
        foreach ($this->children as $child) {
            $this->removeChild($child);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot() : NodeInterface
    {
        $root = $this;
        $node = $this;

        while( !is_null($node = $node->getParent()) ) {
            $root = $node;
        }

        return $root;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value) : NodeInterface
    {
        $this->value = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue() 
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(NodeInterface $remove) : NodeInterface
    {
        foreach ($this->children as $key => $child) {
            if ($child === $remove) {
                unset($this->children[$key]);
                $child->setParent(null);
                break;
            }
        }

        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function pop() : NodeInterface
    {
        $parent = $this->getParent();
        if (!is_null($parent)) {
            $parent->removeChild($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isChild() : bool
    {
        return !$this->isRoot();
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot() : bool
    {
        return is_null($this->parent);
    }

    /**
     * {@inheritDoc}
     */
    public function isLeaf() : bool
    {
        return count($this->children) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(\Closure $serializer = null) : array
    {
        $node = [
            'value' => static::processValue($this->getValue(), $serializer),
            'children' => []
        ];

        foreach ($this->children as $key => $childNode) {
            $node['children'][$key] = $childNode->toArray($serializer);
        }

        return $node;
    }

    /**
     * (un)serialize value if Closure is passed
     * @param mixed $value
     * @param Closure|null $callback
     * @return mixed
     */
    private static function processValue($value, \Closure $closure = null)
    {
        if (is_callable($closure)) {
            $value = $closure($value);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromArray(array $array, \Closure $unserializer = null) : NodeInterface
    {
        $node = new Node(static::processValue($array['value'], $unserializer));

        foreach ($array['children'] as $key => $child) {
            $node->addChild(static::fromArray($child, $unserializer), $key);
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function accept(VisitorInterface $visitor)
    {
        return $visitor->visit($this);
    }

}
