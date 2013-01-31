BetterHTML
==========

Lightweight jQuery-like PHP class for building complex HTML structures

It let you build an HTML on the fly the same way you used to with jQuery.

Simple things:

~~~php
	$p = bh("<p />")->text('Nice paragraph, eh?!');

	$div = bh("<div />").addClass("contents")
		.append("<p />").just().text("First of all ...").end()
		.append("<p />").just().text("Secondly, ...").end()
		.append("<p />").just().text("At last, ...").end();

	print $div->asHTML();
~~~

More complex things, like building a real table:

~~~php
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
	print $table->asHtml();
~~~

And the result is quite predictable:

~~~html
<table class='table datagrid editable' id='table5' hidden='hidden'>
    <thead>
        <tr>
            <th class='sortable'>Task</th>
            <th class='sortable' align='right'>Completion</th>
            <th class='sortable'>Manager</th>
        </tr>
    </thead>
    <tbody>
        <tr id='0'>
            <td>Hire a good PHP dev</td>
            <td align='right'>15</td>
            <td>Peter</td>
        </tr>
        <tr id='1'>
            <td>Fix the printer</td>
            <td align='right'>100</td>
            <td>Josh</td>
        </tr>
        <tr id='2'>
            <td>Release v2.15</td>
            <td align='right'>89</td>
            <td>Peter</td>
        </tr>
        <tr id='3'>
            <td>Fix Bug #9</td>
            <td align='right'>0</td>
            <td>nobody</td>
        </tr>
    </tbody>
</table>
~~~

Note: this is not a parser, use other excellent libraries for that.