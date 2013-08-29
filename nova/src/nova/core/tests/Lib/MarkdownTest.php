<?php

use nova\core\lib\Markdown;
use nova\core\lib\TestCase;
use dflydev\markdown\MarkdownParser;

class MarkdownTest extends TestCase {

	protected $markdown;

	public function setUp()
	{
		$this->markdown = new Markdown(new MarkdownParser);
	}

	/**
	 * @covers	Markdown::parse
	 */
	public function testConvertsToHtml()
	{
		$h1Expects = "<h1>Hello World</h1>\n";
		$pExpects = "<p>Hello world</p>\n";

		$h1Actual = $this->markdown->parse("# Hello World");
		$pActual = $this->markdown->parse("Hello world");

		$this->assertSame($h1Expects, $h1Actual);
		$this->assertSame($pExpects, $pActual);
	}

}