<?php

require_once(dirname(__FILE__).'/../../BetterHTMLElement.php');

class BetterHtmlTest extends PHPUnit_Framework_TestCase
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

	public function testTable()
	{
		$headers = array(
			  array('title' => 'Task', 'sortable' => TRUE)
			, array('title' => 'Completion', 'sortable' => TRUE, 'align' => 'right')
			, array('title' => 'Manager', 'sortable' => TRUE)
		);

		$data = array(
			  array('Hire a good PHP dev', 15, 'Peter')
			, array('Fix the printer', 100, 'Josh')
			, array('Release v2.15', 89, 'Peter')
			, array('Fix Bug #9', 0, 'nobody')
		);

		$options = array(
			'editable' => 'true'
		);

		// make the table
		$table = bh("<table />");

		$table->addClass('table')->attr('id', 'table5')->addClass('datagrid');
		$table->attr('hidden', 'hidden');

		if (isset($options['editable']))
			$table->addClass('editable');

		// make the header line
		$thead_tr = $table->append('<thead />')->just()->append('<tr/>')->just();

		// build the headers row
		foreach ($headers as $index=>$header)
		{
			if (isset($header['hidden']))
				continue;

			// make a header
			$th = bh("<th />");

			// fill it with props and attrs
			if (isset($header['title']))
				$th->text($header['title']);
			if (isset($header['align']))
				$th->attr('align', $header['align']);
			if (isset($header['sortable']))
				$th->addClass('sortable');

			// and stick it to the header line
			$table->find('tr')->append($th);
		}

		// append the table body
		$tbody = $table->append("<tbody />")->just();

		// deal with data rows
		foreach ($data as $id=>$row)
		{
			// again, make the line
			$tr = $tbody->append("<tr />")->just();

			// loop through headers
			foreach ($headers as $index=>$header)
			{
				$tr->attr('id', $id);

				// no one needs hidden data
				if (isset($header['hidden']))
					continue;

				// stick the next data cell to the line
				$td = $tr->append("<td />")->just()->text($row[$index]);

				// tweak some more attrs
				if (isset($header['align']))
					$td->attr('align', $header['align']);
			}
		}

		// done!
		print "\n --- Here goes the table:\n\n";
		print $table->asHtml();

	}
}
