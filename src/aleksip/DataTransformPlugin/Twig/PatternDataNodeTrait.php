<?php

namespace aleksip\DataTransformPlugin\Twig;
use Twig\Compiler as Twig_Compiler;

trait PatternDataNodeTrait
{
    protected $data;

    public function setData($data)
    {
        if (is_int($data) || is_float($data)) {
            if (false !== $locale = setlocale(LC_NUMERIC, 0)) {
                setlocale(LC_NUMERIC, 'C');
            }

            $this->data .= $data;

            if (false !== $locale) {
                setlocale(LC_NUMERIC, $locale);
            }
        } elseif (null === $data) {
            $this->data .= 'null';
        } elseif (is_bool($data)) {
            $this->data .= ($data ? 'true' : 'false');
        } elseif (is_array($data)) {
            $this->data .= 'array(';
            $first = true;
            foreach ($data as $key => $v) {
                if (!$first) {
                    $this->data .= ', ';
                }
                $first = false;
                $this->setData($key);
                $this->data .= ' => ';
                $this->setData($v);
            }
            $this->data .= ')';
        } elseif (is_object($data)) {
            $this->data .= 'unserialize(';
            $this->data .= $this->string(serialize($data));
            $this->data .= ')';
        } else {
            $this->data .= $this->string($data);
        }
    }

    protected function string($value)
    {
        return sprintf('"%s"', addcslashes($value, "\0\t\"\$\\"));
    }

    protected function addTemplateArguments(Twig_Compiler $compiler)
    {
        $variables = $this->hasNode('variables')
            ? $this->getNode('variables')
            : null
        ;
        if (null === $variables) {
            if (false === $this->getAttribute('only')) {
                $compiler
                    ->raw('array_merge($context, ')
                    ->raw($this->data)
                    ->raw(')')
                ;
            }
            else {
                $compiler->raw('array()');
            }
        } elseif (false === $this->getAttribute('only')) {
            $compiler
                ->raw('array_merge($context, ')
                ->raw($this->data)
                ->raw(', ')
                ->subcompile($variables)
                ->raw(')')
            ;
        } else {
            $compiler->subcompile($variables);
        }
    }
}
