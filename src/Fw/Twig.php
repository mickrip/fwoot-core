<?php
namespace Fw;

class Twig
{
	var $data = array();
	static $global_data = array();

	static $filters = array();

	var $cache_dir = "/tmp";

	function __construct($template_file, $inject = array())
	{
		$this->template_file = $template_file;

		$this->data = (array)$inject;

		return $this;
	}


	public static function global_data($key, $val)
	{
		self::$global_data[$key] = $val;
	}

	public function data($as, $name)
	{
		$this->data[$as] = $name;
		return $this;
	}

	public static function factory($template_path, $inject = array())
	{
		return new \Fw\Twig($template_path, $inject);
	}

	static function add_filter($filter)
	{
		self::$filters[] = $filter;
	}

	public function render()
	{


		// Merge with Global Data Static array
		$this->data = array_merge(self::$global_data, $this->data);

		// Inject "fw" object
		$this->data["fw"] = new \Fw\Twig\Extend();

		// Determine Template Path
		if (!$template_path = \Fw\Find::in("views", $this->template_file)) {
			throw new \Exception("Template File (" . $this->template_file . ") not found. Looked in " . \Fw\Debug::get());
		}
		$template_path_info = pathinfo($template_path);

		// Init Twig Loader. Search paths are the Fw00t view paths.
		$loader = new \Twig_Loader_Filesystem(array_merge(array($template_path_info["dirname"]), \Fw\Find::get_paths("views")));

		// Init Twig Renderer Environment

		if (\Fw\Config::get("env") == \Fw\Config::PRODUCTION) {
			$twig = new \Twig_Environment($loader, array(
				'cache' => $this->cache_dir
			));
		} else {
			$twig = new \Twig_Environment($loader, array());
		}

		foreach (self::$filters as $filter) {
			$twig->addFilter($filter);
		}

		// Commence Twigging
		$template = $twig->loadTemplate($template_path_info["basename"]);
		return $template->render($this->data);
	}


}