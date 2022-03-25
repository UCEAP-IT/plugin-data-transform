<?php

namespace aleksip\DataTransformPlugin\Twig;

use Twig\Node\IncludeNode as Twig_Node_Include;

class PatternDataIncludeNode extends Twig_Node_Include
{
    use PatternDataNodeTrait;

    public function __construct(Twig_Node_Include $originalNode, $data)
    {
        $variables = $originalNode->hasNode('variables')
            ? $originalNode->getNode('variables')
            : null
        ;
        parent::__construct(
          $originalNode->getNode('expr'),
          $variables,
          $originalNode->getAttribute('only'),
          $originalNode->getAttribute('ignore_missing'),
          $originalNode->getTemplateLine(),
          $originalNode->getNodeTag()
        );

        $this->setData($data);
    }
}
