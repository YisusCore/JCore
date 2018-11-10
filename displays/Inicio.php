<?php
namespace Display;

class Inicio
{
	public function index()
	{
		APP()->Response->notice('Está leyendo la vista por defecto');
	}
}