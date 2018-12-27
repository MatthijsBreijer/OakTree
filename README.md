# OakTree
[![Build Status](https://travis-ci.org/MatthijsBreijer/OakTree.svg?branch=master)](https://travis-ci.org/MatthijsBreijer/OakTree) 
[![codecov](https://codecov.io/gh/matthijsbreijer/oaktree/branch/master/graph/badge.svg)](https://codecov.io/gh/MatthijsBreijer/OakTree)

**A tree data structure implementation with support for key-based traversal and custom (un)serialization. Useful implementations could include product trees, file/folder structures, routing trees and a lot more. As a package its written from scratch, mostly derived from [nicmart/Tree](https://github.com/nicmart/Tree)**

## Tree structure and tree traversal methods

### Node creation
A Node can be passed any type of value during instantiation.
```php
use MatthijsBreijer\OakTree\Node;

$node = new Node('data'); 
```
### Getting and setting values
A node value can be retrieved and altered using `Node::getValue()` and `Node::setValue()`. Note that `Node::setValue()` is fluent and can be daisy-chained.
```php
var_dump( $node->getValue() ); // string(4) "data"

$node->setValue('new data');
var_dump( $node->getValue() ); // string(8) "new data"
```
### Adding children to a Node
One or more children can be added using `Node::addChild()`. This method is fluent and can be daisy-chained.
```php
$child1 = new Node('child1');
$child2 = new Node('child2');

$node->addChild($child1)
    ->addChild($child2);
```
### Added children to a Node with a specific child key
OakTree keeps track of keys for the Nodes within the tree. As such one can assign a key to a child Node.
```php
$child1 = new Node('child1');
$child2 = new Node('child2');

$node->addChild($child1, 0)
    ->addChild($child2, 'customKey');
```

### Removing a child
Remove the `$child1` and `$child2` instance from `$node` 's children. `Node::removeChild()` is fluent and can be daisy-chained.
```php
$node->removeChild($child1)
    ->removeChild($child2);
```

### Get all direct children of a Node
```php
$children = $node->getChildren(); // array(2) [0 => $child1, 'customKey' => $child2]
```
### Get all keys of direct children Nodes
```php
$children = $node->getChildrenKeys(); // array(2) [0, 'customKey']
```
### Get a child Node by its key
OakTree keeps keys intact for the Nodes within the tree. As such one can get a child Node by key.
```php
// when $child1 is located at array key '1'
$child1 = $node->getChildByKey(1);

// when $child1 is located at array key 'customKey'
$child1 = $node->getChildByKey('customKey');

// requesting a non-existent key throws \OutOfBoundsException
$node->getChildByKey('nonExistentKey');
```

### Get the child and remove child from its Node tree
Using `Node::pop()` one can remove/separate a Node (and its descendants) from a tree and alter it independently.
```php
$child1 = new Node('child1');
$child2 = new Node('child2');

$node->addChild($child1)
    ->addChild($child2);

// all examples below produce same result
$child1 = $child1->pop();
$child1 = $node->getChildren()[0]->pop();
$child1 = $node->getChildByKey(0)->pop();

// using one of the above examples $node would look as follows
var_dump($node->getChildren()); // array(1) [1 => $child2]
```
### Set children
*Please note that this method removes previously present children.* `Node::setChildren()` sets a (new) array of children Nodes. This method is fluent and can be daisy-chained. 
```php
$node->setChildren([new Node('a'), new Node('b')]);

// or with keys
$node->setChildren([0 => new Node('a'), 'customKey' => new Node('b')]);
```
### Get the parent node of a child
```php
$childNode->getParent(); // returns $parent Node
$root->getParent(); // $root has no parent Node and returns NULL
```
### Get the root Node of a tree
```php
// all return $root Node when part of the same tree
$root->getRoot();
$child1->getRoot();
$grandChild1->getRoot();
```
## Tree context
### Is the Node a leaf?
A leaf is a node with no children.
```php
$node->isLeaf(); // bool(true)
```
### Is the Node a child?
A child is a node that has a parent.
```php
$node->isChild(); // bool(true)
```
### Is the Node then root Node?
A root Node has no parent.
```php
$node->isRoot(); // bool(true)
```
## Tree alterations
OakTree uses the [Visitor Pattern](https://sourcemaking.com/design_patterns/visitor) to iterate over a tree. The visitor contains the logic to apply on the Nodes in the tree. Visitors should implement the Visitor interface `MatthijsBreijer\OakTree\Visitor\VisitorInterface`. Visitors return mixed content depending on their purpose.

### LeafVisitor
The `LeafVisitor` returns an array of leaves of a tree (`$node`'s where `Node::isLeaf()` returns true).
```php
$tree = new Node('root');
$child1 = new Node('child1');
$child2 = new Node('child2');

$tree->addChild($child1)
    ->addChild($child2);

$visitor = new LeafVisitor();

$leafs = $tree->accept($visitor); // array(2) [$child1, $child2]
```
### ClosureVisitor
The ClosureVisitor can be passed a function or`\Closure` argument to define its behavior. The example below mimicks the LeafVisitor.
```php
$tree = new Node('root');
$child1 = new Node('child1');
$child2 = new Node('child2');

$tree->addChild($child1)
    ->addChild($child2);

$closure = function(NodeInterface $node, VisitorInterface $visitor) {
    $return = $node->isLeaf() ? [$node] : [];

    foreach ($node->getChildren() as $key => $child) {
        $return = array_merge($return, $child->accept($visitor));
    }

    return $return;
};

$visitor = new ClosureVisitor($closure);

$leafs = $tree->accept($visitor); // array(2) [$child1, $child2]
```

## Serialization / Unserialization
OakTree Nodes have a `Node::toArray()` and `Node::fromArray()` method to allow customized (un)serialization of a tree. This can be used to cache data, to quickly pass around information from the tree to an API or vice versa.  The nodes also implement PHP's `\JsonSerializable` interface. 

### Basic serialization
```php
// Expose product catalog to a view
$product = new Node('Product 1');
$option1 = new Node('Extended package option 1');
$option2 = new Node('Extended package option 2');

$product->addChild($option1)
    ->addChild($option2);

// Both examples below have the same JSON encoded result
// array(2) [
//     'value' => 'Product 1', 
//     'children' => array(2) [
//         array(3) [
//             'value' => 'Extended package option 1', 
//             'children' => []
//         ],
//         array(3) [
//             'value' => 'Extended package option 2', 
//             'children' => []
//         ]
//     ]
// ]
$json = json_encode($product);
$json = json_encode( $product->toArray() );
```
### Basic unserialization
Using the above example the JSON result `$json` can be converted back to a tree as follows.
```php
$tree = Node::fromArray( json_decode($json) );
```

### Closure based serialization

```php
// Build a fictive product catalog tree
$product = new Node( new Product('Product 1') );
$option1 = new Node( new Option('Extended package option 1') );
$option2 = new Node( new Option('Extended package option 2') );

$product->addChild($option1)
    ->addChild($option2);

$closure = function($nodeValue) {
    return [
        'name' => $nodeValue->getName(),
        'type' => get_class($nodeValue)
    ];
};

// Both examples below have the same JSON encoded result
// array(2) [
//     'value' => array(2) [
//         'name' => 'Product 1', 
//         'type' => 'Product'
//     ],
//     'children' => array(2) [
//         array(2) [
//             'value' => array(2) [
//                 'name' => 'Extended package option 1',
//                 'type' => 'Option'
//             ],
//             'children' => []
//         ],
//         array(2) [
//             'value' => array(2) [
//                 'name' => 'Extended package option 2',
//                 'type' => 'Option'
//             ],
//             'children' => []
//         ]
//     ]
// ]
$json = $product->toArray($closure);
```
### Closure based unserialization
Using the above example the JSON result `$json` can be converted back to a tree as follows.
```php
$closure = function($value) {
    $type = $value['type'];
    $name = $value['name'];
    return new $type($name);
};

$tree = Node::fromArray(json_decode($json), $closure);
```



