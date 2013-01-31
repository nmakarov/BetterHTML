<?php

/**
 * Class represents an HTML element and holds the tree of underlying elements. 
 */
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

	/**
	 * Get/set the element's attribute.
	 * 
	 * @param  [type] $key   [description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function attr($key,$value=NULL)
	{
		if (is_null($value))
		{
			// getter
			return isset($this->attrs[$key]) ? $this->attrs[$key] : NULL;
		}

		$this->attrs[$key] = $value;

		return $this;
	}

	/**
	 * Add class to the current element.
	 * 
	 * @param [type] $value [description]
	 */
	public function addClass($value)
	{
		if ( ! in_array($value, $this->classes))
			$this->classes[] = $value;

		return $this;
	}

	/**
	 * Remove class of the current element.
	 * 
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function removeClass($value)
	{
		if ($key = array_search($value, $this->classes) !== FALSE)
			unset($this->classes[$key]);

		return $this;
	}

	/**
	 * Return the element's parent element.
	 * 
	 * @return Object parent element reference or an empty element if no parent found.
	 */
	public function parent()
	{
		return $this->parent ? $this->parent : new self();
	}

	/**
	 * Insert `$selector` after the current element.
	 * 
	 * @param  [type]  $selector   [description]
	 * @param  boolean $return_new [description]
	 * @return [type]              [description]
	 */
	public function after($selector, $return_new=FALSE)
	{
		// parse `selector` and stick it to the children array
		$sibling = new self($selector, $this);
		$this->parent->children[] = $sibling;

		return $return_new ? $sibling : $this;
	}


	/**
	 * Insert `$selector` as the last child of the current element.
	 * 
	 * @param  [type]  $selector   [description]
	 * @param  boolean $return_new [description]
	 * @return [type]              [description]
	 */
	public function append($selector, $return_new=FALSE)
	{

		if (is_object($selector) && get_class($selector) === get_class($this))
			$child = $selector;
		else
			$child = new self ($selector, $this);

		$this->children[] = $child;

		$this->lastInsertedElement = $child;

		return $return_new ? $child : $this;
	}

	/**
	 * Return the reference to last inserted element.
	 * 
	 * @return [type] [description]
	 */
	public function just()
	{
		$this->lastInsertedElement->previousElement = $this;
		return $this->lastInsertedElement;
	}

	/**
	 * Return the reference to the previously active element.
	 * 
	 * @return [type] [description]
	 */
	public function end()
	{
		return $this->previousElement;
	}

	/**
	 * Get element's property.
	 *
	 * Supported properties:
	 * - tagName
	 * 
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function prop($value)
	{
		if ($value === 'tagName')
			return strtoupper($this->tag);
	}

	/**
	 * Add a text element to the current element or return all text elements
	 * 
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
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

	/**
	 * Get the first child of the element.
	 * 
	 * @return [type] [description]
	 */
	public function firstChild()
	{
		$child = $this->children[0];
		return $child;
	}

	/**
	 * Tells if the element is empty.
	 * 
	 * @return boolean [description]
	 */
	public function isEmpty()
	{
		return $this->tag === 'empty';
	}

	/**
	 * Return the next sibling of the current element's parent.
	 * 
	 * @return Object if no next element found, return an empty one.
	 */
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

	/**
	 * Find the first element which tag matches `$selector`.
	 * 
	 * @param  mixed $selector A tag to look for
	 * @return object  First element matches selector or an empty object
	 */
	public function find($selector)
	{
		// just tag name supported for now
		
		// this empty element will be returned if nothing is found
		$found = new self();

		foreach ($this->children as $child)
		{
			// look at the immediate child for match
			if ($child->tag == $selector)
			{
				$found = $child;
				break;
			}

			// now look at child's children
			$found = $child->find($selector);
			if ( ! $found->isEmpty())
				break;
		}

		return $found;
	}

	/**
	 * Convert internal structure into HTML.
	 * 
	 * @param  boolean $pretty Whenever pretty HTML with line breaks is needed
	 * @return string          HTML chunk
	 */
	public function asHtml($pretty=TRUE)
	{
		if ($this->isEmpty())
			return '';

		if ($this->tag === 'text')
			return $this->text;

		$html = "<$this->tag";

		// append attrs and classes as needed
		if ($classes = implode(' ', $this->classes))
			$html .= " class='$classes'";

		foreach ($this->attrs as $attr=>$value)
			$html .= " $attr='$value'";

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


