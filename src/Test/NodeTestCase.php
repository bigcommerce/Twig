<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Test;

use PHPUnit\Framework\TestCase;
use Twig\Compiler;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Node\Node;

abstract class NodeTestCase extends TestCase
{
    abstract public static function getTests();

    /**
     * @dataProvider getTests
     */
    public function testCompile($node, $source, $environment = null, $isPattern = false)
    {
        $this->assertNodeCompilation($source, $node, $environment, $isPattern);
    }

    public function assertNodeCompilation($source, Node $node, Environment $environment = null, $isPattern = false)
    {
        $compiler = $this->getCompiler($environment);
        $compiler->compile($node);

        if ($isPattern) {
            $this->assertStringMatchesFormat($source, trim($compiler->getSource()));
        } else {
            $this->assertEquals($source, trim($compiler->getSource()));
        }
    }

    protected static function getCompiler(Environment $environment = null)
    {
        return new Compiler(null === $environment ? self::getEnvironment() : $environment);
    }

    protected static function getEnvironment()
    {
        return new Environment(new ArrayLoader([]));
    }

    protected static function getVariableGetter($name, $line = false)
    {
        $line = $line > 0 ? "// line {$line}\n" : '';

        if (\PHP_VERSION_ID >= 70000) {
            return sprintf('%s($context["%s"] ?? null)', $line, $name);
        }

        if (\PHP_VERSION_ID >= 50400) {
            return sprintf('%s(isset($context["%s"]) ? $context["%s"] : null)', $line, $name, $name);
        }

        return sprintf('%s$this->getContext($context, "%s")', $line, $name);
    }

    protected static function getAttributeGetter()
    {
        if (\function_exists('twig_template_get_attributes')) {
            return 'twig_template_get_attributes($this, ';
        }

        return '$this->getAttribute(';
    }
}

class_alias('Twig\Test\NodeTestCase', 'Twig_Test_NodeTestCase');
