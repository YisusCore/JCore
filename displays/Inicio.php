<?php
namespace Display;
use Structure\General as Style;

class Inicio extends Style
{
	public function index()
	{
		APP()->Response->notice('Está leyendo la vista por defecto');
		add_inline_js ('Using("bootstrap")');
	}
}