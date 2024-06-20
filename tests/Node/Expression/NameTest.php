<?php

namespace Twig\Tests\Node\Expression;

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Twig\Environment;
use Twig\Loader\LoaderInterface;
use Twig\Loader\SourceContextLoaderInterface;
use Twig\Node\Expression\NameExpression;
use Twig\Test\NodeTestCase;

class NameTest extends NodeTestCase
{
    public function testConstructor()
    {
        $node = new NameExpression('foo', 1);

        $this->assertEquals('foo', $node->getAttribute('name'));
    }

    public static function getTests()
    {
        $loader = new class implements LoaderInterface, SourceContextLoaderInterface {
            public function getSource($name)
            {
            }

            public function getCacheKey($name)
            {
            }

            public function isFresh($name, $time)
            {
            }

            public function getSourceContext($name)
            {
            }
        };
        $node = new NameExpression('foo', 1);
        $context = new NameExpression('_context', 1);

        $env = new Environment($loader, ['strict_variables' => true]);
        $env1 = new Environment($loader, ['strict_variables' => false]);

        if (\PHP_VERSION_ID >= 70000) {
            $output = '($context["foo"] ?? $this->getContext($context, "foo"))';
        } elseif (\PHP_VERSION_ID >= 50400) {
            $output = '(isset($context["foo"]) ? $context["foo"] : $this->getContext($context, "foo"))';
        } else {
            $output = '$this->getContext($context, "foo")';
        }

        return [
            [$node, "// line 1\n".$output, $env],
            [$node, self::getVariableGetter('foo', 1), $env1],
            [$context, "// line 1\n\$context"],
        ];
    }
}
