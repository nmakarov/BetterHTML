<?php

if (class_exists('Yii'))
	$framework = 'Yii';
else if (class_exists('App'))
	$framework = 'App';
else
	throw new Exception ("No framework found.");

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."TestCase{$framework}.php");

class BetterHtmlTest extends ATestCase
{
// App::i()->import('framework/BetterHTMLElement');
// class BetterHtmlTest extends AppTest
// {

	public function testBetterHtml()
	{
		$this->assertTrue(is_object(bh()));

		$p = bh("<p/>")->text('abc');

		$this->assertEquals("<p>abc</p>", $p->asHtml(FALSE));

		// $p->append('<div />')->just()->text('first div');
		// $p->append('<div />')->just()->text('second div');
		// $p->append('<div />')->just()->text('third div');

		$p  -> append('<div />')->just()->text('first div')->end()
			-> append('<div />')->just()->text('second div')->end()
			-> append('<div />')->just()->text('third div')->end();


		print $p->asHtml(FALSE);

		$this->assertEquals('third div', $p->just()->text());

		print "first/next1: " . $p->firstChild()->next()->asHtml(FALSE) . "\n";
		print "first/next2: " . $p->firstChild()->next()->next()->asHtml(FALSE) . "\n";
		print "ultimate: " . $p->firstChild()->next()->next()->next()->next()->next()->next()->next()->next()->asHtml(FALSE) . "\n";


		$this->assertTrue($p->firstChild()->next()->next()->next()->next()->next()->next()->next()->next()->isEmpty());

		// $p = bh("<div />")->addClass('class1')->addClass('class2')->after("<p />", TRUE)->addClass('p_class1')->after("<span />", TRUE)->after('text:abcde')->parent()->parent();
		// print "\n".$p->asHtml()."\n";

		// $t = bh("<table />");
		// $headers_row = $t->after("<thead />", TRUE)->after("<tr />", TRUE);

		// $headers_row->after("<td />",TRUE)->after("text:field 1");
		// $headers_row->after("<td />",TRUE)->after("text:field 2");
		// $headers_row->after("<td />",TRUE)->after("text:field 3");

		// print "\n".$t->asHtml()."\n";

		// $headers_row->addClass('abc');

		// print "\n".$t->asHtml()."\n";

	}
}