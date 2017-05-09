<?php
//PipeMenuBuilder class -- It builds menus.
class PipeMenu {
	private $classNames = ["pipemenu"];
	private $entries = [];
	public function setClass($class) {
		$this->classNames = [$class];
	}
	public function addClass($class) {
		$this->classNames[] = $class;
	}
	public function removeClass($class) {
		foreach(array_keys($this->classNames, $class, true) as $k => $v) {
			unset($this->classNames[$k]);
		}
	}
	public function getClasses() {
		return $this->classNames;
	}
	public function add($entry) {
		$this->entries[] = $entry;
	}
	public function addStart($entry) {
		array_unshift($this->entries, $entry);
	}
	public function pop() {
		return array_pop($this->entries);
	}
	public function shift() {
		return array_shift($this->entries);
	}
	public function build($style = 1) {
		if(!$this->entries || in_array('breadcrumbs', $this->classNames) && count($this->entries) === 1)
			return "";
		$html = "<ul class=\"" . implode(" ", $this->classNames) . "\">";
		foreach ($this->entries as $entry) {
			$html .= "<li>".$entry->build($style)."</li>";
		}
		$html .= "</ul>";
		return $html;
	}
}
interface PipeMenuEntry {
	public function build();
}
class PipeMenuLinkEntry implements PipeMenuEntry {
	private $label;
	private $action;
	private $id;
	private $args;
	private $icon;
	public $newtab = false;

	public function __construct($label, $action, $id = 0, $args = "", $icon="", $onclick="") {
		$this->label = $label;
		$this->action = $action;
		$this->id = $id;
		$this->args = $args;
		$this->icon = $icon;
		$this->onclick = $onclick;
	}
	public function build($style = 1) {
		$icontag = "";
		if($this->icon && $style != 1)
			$icontag = "<i class=\"icon-". $this->icon ."\"></i>";
		$label="";
		if($style != 2)
			$label = $this->label;
		if($icontag && $label)
			$icontag .= "&nbsp;";
		$tooltip = "";
		if($style == 2)
			$tooltip = "title=\"".htmlspecialchars($this->label)."\"";

		$target = "";
		if($this->newtab)
			$target="target=\"_blank\"";

		$onclick="";
		if($this->onclick)
			$onclick="onclick=\"".htmlspecialchars($this->onclick)."\"";

		return "<a href=\"" . htmlspecialchars($this->getLink()) . "\" $target $tooltip $onclick>$icontag$label</a>";
	}

	public function getText() {
		return $this->label;
	}
	public function getLink() {
		return actionLink($this->action, $this->id, $this->args);
	}

}
class PipeMenuAnyLinkEntry implements PipeMenuEntry {
	private $label;
	private $link;
	public function __construct($label, $link) {
		$this->label = $label;
		$this->link = $link;
	}
	public function build() {
		return "<a href=\"" . htmlspecialchars($this->link) . "\">" . $this->label . "</a>";
	}
	public function getText() {
		return $this->label;
	}
	public function getLink() {
		return $this->link;
	}
}
class PipeMenuTextEntry implements PipeMenuEntry {
	private $text;
	public function __construct($text) {
		$this->text = $text;
	}
	public function build() {
		return htmlspecialchars($this->text);
	}
	public function getText() {
		return $this->text;
	}
	public function getLink() {
		return "";
	}
}
class PipeMenuHtmlEntry implements PipeMenuEntry {
	private $html;
	public function __construct($html) {
		$this->html = $html;
	}
	public function build() {
		return $this->html;
	}
	public function getText() {
		if(!preg_match('@<a [^>]+>(.*)</a>@', $this->html, $match))
			return $html;
		return $match[1];
	}
	public function getLink() {
		preg_match('/href="([^"]*)"/', $this->html, $match);
		return html_entity_decode($match[1]);
	}
}