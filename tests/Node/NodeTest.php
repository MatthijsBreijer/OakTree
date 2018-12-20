<?php
namespace MatthijsBreijer\Tests\OakTree\Node;

use MatthijsBreijer\OakTree\Node\Node;
use MatthijsBreijer\OakTree\Visitor\VisitorInterface;
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{

    /**
     * @test
     */
    public function testAddChildAndGetChildren() 
    {
        $node = new Node;
        $result = $node
            ->addChild($child1 = new Node)
            ->addChild($child2 = new Node);
        
        $this->assertSame([$child1, $child2], $node->getChildren());
    }

    /**
     * @test
     */
    public function testAddChildSetsParent()
    {
        $node = new Node;
        $result = $node
            ->addChild($child1 = new Node)
            ->addChild($child2 = new Node);

        $this->assertSame($node, $child1->getParent());
        $this->assertSame($node, $child2->getParent());
    }

    /**
     * @test
     */
    public function testSetAndGetChildren() 
    {
        $node = (new Node)
            ->addChild($child1 = new Node, 0)
            ->addChild($child2 = new Node, 'a');

        $node->setChildren($children = [0 => $child1, 'a' => $child2]);

        $this->assertSame($children, $node->getChildren());
    }

    /**
     * @test
     */
    public function testSetChildrenSetsParent()
    {
        $node = new Node;
        $child1 = new Node('1');
        $child2 = new Node('2');

        $node->setChildren([0 => $child1, 'a' => $child2]);

        $this->assertSame($node, $child1->getParent());
        $this->assertSame($node, $child2->getParent());
    }

    /**
     * @test
     */
    public function testGetChildrenKeys()
    {
        $node = (new Node)
            ->addChild(new Node, 0)
            ->addChild(new Node, 'a');

        $this->assertSame([0, 'a'], $node->getChildrenKeys());
    }

    /**
     * @test
     */
    public function testGetAndSetParent()
    {
        $node = new Node;
        $child = new Node;
        $child->setParent($node);
        
        $this->assertSame($node, $child->getParent());
    }

    /**
     * @test
     */
    public function testSetAndGetValue()
    {
        $node = new Node;
        $value = 'testing';
        
        $node->setValue($value);
        $this->assertSame($value, $node->getValue());
    }

    /**
     * @test
     */
    public function testRemoveChild()
    {
        $tree = (new Node)
            ->addChild($child1 = new Node)
            ->addChild($child2 = new Node);

        $this->assertcount(2, $tree->getChildren());

        $tree->removeChild($child1);

        $this->assertCount(1, $tree->getChildren());
        $this->assertNull($child1->getParent());
    }

    /**
     * @test
     */
    public function testPopRootReturnsRoot()
    {
        $root = (new Node('root'))
            ->addChild(new Node('A'));

        $this->assertSame($root, $root->pop());
    }

    /**
     * @test
     */
    public function testPopSubTreeReturnsSubTree()
    {
        $root = (new Node('root'))
            ->addChild($A = new Node('A'));
        
        $A->addChild($B = new Node('B'));

        $this->assertSame($A, $A->pop());
    }

    /**
     * @test
     */
    public function testPopSubTreeRemovesParent()
    {
        $root = (new Node('root'))
            ->addChild($A = new Node('A'));

        $A->addChild($B = new Node('B'));

        $this->assertNull($A->pop()->getParent());
    }

    /**
     * @test
     */
    public function testPopSubTreeIsRemovedFromRoot()
    {
        $root = (new Node('root'))
            ->addChild($A = new Node('A'));

        $A->addChild($B = new Node('B'));
        
        $A->pop();

        $this->assertCount(0, $root->getChildren());
    }

    /**
     * @test
     */
    public function testIsChild()
    {
        $tree = (new Node)
            ->addChild($child = new Node);

        $this->assertTrue($child->isChild());
        $this->assertFalse($tree->isChild());
    }

    /**
     * @test
     */
    public function testIsRoot()
    {
        $tree = (new Node)
            ->addChild($child = new Node);

        $this->assertTrue($tree->isRoot());
        $this->assertFalse($child->isRoot());
    }    

    /**
     * @test
     */
    public function testIsLeaf()
    {
        $tree = (new Node)
            ->addChild($child = new Node);

        $this->assertTrue($child->isLeaf());
        $this->assertFalse($tree->isLeaf());
    }    

    /**
     * @test
     * @dataProvider constructorArguments
     * @param mixed $value
     * @param NodeInterface[] $children
     */
    public function testConstructor($value, $children)
    {
        $node = new Node($value, $children);

        $this->assertSame($value, $node->getValue());
        $this->assertSame($children, $node->getChildren());
    }    

    /**
     * Provides testdata for testConstructor()
     */
    public function constructorArguments()
    {
        return [
            [null, []],
            [null, [new Node]],
            ['abc', ['key' => new Node]],
            ['def', []]
        ];
    }

    /**
     * @test
     */
    public function testToArrayAndFromArray()
    {
        $tree = (new Node)
            ->addChild(new Node);
        
        $array = [
            'value' => null,
            'children' => [ 0 => ['value' => null, 'children' => []] ]
        ];
        
        $this->assertSame($array, $tree->toArray());
        $this->assertEquals($tree, Node::fromArray($array));

        // JSONSerialize should do the same as toArray()
        $this->assertSame($array, $tree->jsonSerialize());
    }

    /**
     * @test
     */
    public function testToArrayAndFromArrayWithCustomKey()
    {
        $tree = (new Node('root'))
            ->addChild(new Node, 'custom');
        
        $array = [
            'value' => 'root',
            'children' => [ 
                'custom' => ['value' => null, 'children' => []] 
            ]
        ];

        $this->assertSame($array, $tree->toArray());
        $this->assertEquals($tree, Node::fromArray($array));

        // JSONSerialize should do the same as toArray()
        $this->assertSame($array, $tree->jsonSerialize());
    }

    /**
     * @test
     */
    public function testToArrayAndFromArrayWithSerializer()
    {
        $tree = (new Node('root'))
            ->addChild(new Node(new \StdClass));
        
        $serializer = function($value) { return serialize($value); };
        $unserializer = function($value) { return unserialize($value); };

        $array = [
            'value' => $serializer('root'),
            'children' => [ 
                0 => ['value' => $serializer(new \StdClass), 'children' => []] 
            ]
        ];

        $this->assertSame($array, $tree->toArray($serializer));
        $this->assertEquals($tree, Node::fromArray($array, $unserializer));
    }

    /**
     * @test
     */
    public function testNodeShouldAcceptVisitor()
    {
        $node = new Node;

        $visitor = $this->createMock(VisitorInterface::class);
        $visitor->expects($this->once())
            ->method('visit')
            ->with($this->equalto($node));

        $node->accept($visitor);
    }

    /**
     * @test
     */
    public function testFluency()
    {
	$node = new Node('node');
        $parent = new Node('parent');
        $child = new Node('child');

        $this->assertSame($node, $node->addChild($child));
        $this->assertSame($node, $node->setParent($parent));
        $this->assertSame($node, $node->setValue('value'));
        $this->assertSame($node, $node->removeChild($child));

    }
}
