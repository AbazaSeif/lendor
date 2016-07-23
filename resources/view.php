<?php

// Custom view middleware
class View extends \Slim\View {

	// Twig loader
	protected $loader;
	// Twig renderer
	protected $twig;

	// Main constructor
	public function __construct ($path) {
		// Call the parent constructor
		parent::__construct();
		// Initialize the twig loader
		$this->loader = new Twig_Loader_Filesystem($path);
		// Initialize the twig renderer
		$this->twig = new Twig_Environment($this->loader);
	}

	// Render a template
	public function render ($template, $data=[]) {
		//print_r($this->data["title"]);
		// Have twig render the template, make sure data passed is an array and not null
		return $this->twig->render($template, $this->data);
	}
}
