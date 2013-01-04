<?php

class BetterHTMLElement
{
	protected $tag = 'empty';
	protected $parent = NULL;
	protected $children = array();
	protected $text = '';
	protected $classes = array();
	protected $attrs = array();
	protected $lastInsertedElement = NULL;
	protected $previousElement = NULL;

	public function __construct($selector=NULL, $context=NULL)
	{
		if ( ! is_null($selector))
			$this->parseSelector($selector, $context);
	}

	public function after($selector, $return_new=FALSE)
	{
		// parse `selector` and stick it to the children array
		$sibling = new self($selector, $this);
		$this->parent->children[] = $sibling;

		return $return_new ? $sibling : $this;
	}

	public function addClass($value)
	{
		if ( ! in_array($value, $this->classes))
			$this->classes[] = $value;

		return $this;
	}

	public function removeClass($value)
	{
		if ($key = array_search($value, $this->classes) !== FALSE)
			unset($this->classes[$key]);

		return $this;
	}

	public function parent()
	{
		return $this->parent ? $this->parent : $this;
	}

	public function append($selector, $return_new=FALSE)
	{
		$child = new self ($selector, $this);
		$this->children[] = $child;

		$this->lastInsertedElement = $child;

		return $return_new ? $child : $this;
	}

	public function just()
	{
		$this->lastInsertedElement->previousElement = $this;
		return $this->lastInsertedElement;
	}

	public function end()
	{
		return $this->previousElement;
	}

	public function prop($value)
	{
		if ($value === 'tagName')
			return strtoupper($this->tag);
	}

	public function text($value=NULL)
	{
		if (is_null($value))
		{
			// getter
			$text = array();
			foreach ($this->children as $child)
				if ($child->prop('tagName') == 'TEXT')
					$text[] = $child->text;
			return implode(" ", $text);

		}

		$child = new self("text:$value", $this);
		$this->children[] = $child;
		return $this;
	}

	public function firstChild()
	{
		$child = $this->children[0];
		return $child;
	}

	public function isEmpty()
	{
		return $this->tag === 'empty';
	}

	public function next()
	{
		// find self in the parents children
		$self_id = -1;
		if (is_null($this->parent))
			return new self();

		foreach ($this->parent->children as $index=>$child)
		{
			if ($child === $this)
			{
				$self_id = $index;
				break;
			}
		}

		return ($index+1)<count($this->parent->children) 
			? $this->parent->children[$self_id+1]
			: new self();
	}

	public function asHtml($pretty=TRUE)
	{
		if ($this->isEmpty())
			return '';

		if ($this->tag === 'text')
			return $this->text;

		$html = "<$this->tag";

		if ($classes = implode(' ', $this->classes))
			$html .= " class='$classes'";
		// append attrs and classes as needed
		$html .= ">";
		if ($pretty)
			$html .= "\n";

		foreach ($this->children as $child)
			$html .= $child->asHtml($pretty);

		$html .= "</$this->tag>";
		if ($pretty)
			$html .= "\n";
		return $html;
		// return the HTML
	}

	protected function parseSelector($selector, $context=NULL)
	{
		if (is_object($selector) && get_class($selector) === get_class($this))
		{
			// $selector is an object, just stick it into the children array
		}
		else if (preg_match("/^</", $selector))
		{
			// simple case - something like `<p />`
			if (preg_match('#^<(\w+)\s*/>\s*$#', $selector, $matches))
				$this->tag = $matches[1];

			$this->parent = $context;
		}
		else if (preg_match("/^text:(.*)$/", $selector, $matches))
		{
			$this->tag = 'text';
			$this->text = $matches[1];
			$this->parent = $context;
		}
		else
		{
			// this is tag/class/id
		}
	}
}

function bh($selector=NULL)
{
	return new betterHTMLElement($selector);
}	


