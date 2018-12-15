<?php
namespace Display;

class Error404 extends \Structure\Error
{
	public function index()
	{
		APP()->Response->error('Página no encontrada', 404);
		http_code(404);
	}
}