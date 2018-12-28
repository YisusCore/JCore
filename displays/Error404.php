<?php
namespace Display;
use Structure\Error as Style;

class Error404 extends Style
{
	public function index()
	{
		APP()->Response->error('Página no encontrada', 404);
		http_code(404);
		add_inline_js ('Using("bootstrap")');
	}
}