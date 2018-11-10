<?php
/**
 * mngr.vrbls.php
 * 
 * El archivo `mngr.vrbls` permite ...
 *
 * Copyright (c) 2018 - 2023, JYS Perú
 *
 * Se otorga permiso, de forma gratuita, a cualquier persona que obtenga una copia de este software 
 * y archivos de documentación asociados (el "Software"), para tratar el Software sin restricciones, 
 * incluidos, entre otros, los derechos de uso, copia, modificación y fusión. , publicar, distribuir, 
 * sublicenciar y / o vender copias del Software, y permitir a las personas a quienes se les 
 * proporciona el Software que lo hagan, sujeto a las siguientes condiciones:
 *
 * El aviso de copyright anterior y este aviso de permiso se incluirán en todas las copias o 
 * porciones sustanciales del software.
 *
 * EL SOFTWARE SE PROPORCIONA "TAL CUAL", SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O IMPLÍCITA, INCLUIDAS,
 * ENTRE OTRAS, LAS GARANTÍAS DE COMERCIABILIDAD, IDONEIDAD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN.
 * EN NINGÚN CASO LOS AUTORES O PROPIETARIOS DE DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER RECLAMO, 
 * DAÑO O CUALQUIER OTRO TIPO DE RESPONSABILIDAD, YA SEA EN UNA ACCIÓN CONTRACTUAL, AGRAVIO U OTRO, 
 * DERIVADOS, FUERA DEL USO DEL SOFTWARE O EL USO U OTRAS DISPOSICIONES DEL SOFTWARE.
 *
 * @package		JCore\Functions
 * @author		YisusCore
 * @link		https://jcore.jys.pe/functions/mngr.vrbls
 * @version		1.0.0
 * @copyright	Copyright (c) 2018 - 2023, JYS Perú (https://www.jys.pe/)
 * @filesource
 */

defined('ABSPATH') or exit('Acceso directo al archivo no autorizado');

/**
 * DIRECTORY_SEPARATOR
 *
 * Separador de Directorios para el sistema operativo de ejecución
 *
 * @global
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);


//====================================================================
// ARRAYs
//====================================================================

if ( ! function_exists('grouping'))
{
	function grouping($array, $opts=[]){
		$opts = array_merge([
			'prefix' => [NULL, NULL, NULL],//Singular, Plural, Zero
			'suffix' => [NULL, NULL, NULL],//Singular, Plural, Zero
			'union' => [', ', ' y '],//normal, last
		], $opts);
		if(is_string($array)){$array=[$array];}
		$array = array_unique($array);

		$r = '';
		$c = count($array);
		$t = 2;//Zero
			if($c==0){$t=2;}
		elseif($c==1){$t=0;}
		elseif($c>=2){$t=1;}

		if(is_string($opts['prefix'])) $opts['prefix'] = [$opts['prefix']];
		if(!isset($opts['prefix'][2]) or is_null($opts['prefix'][2])){$opts['prefix'][2] = $opts['prefix'][0];}
		if(!isset($opts['prefix'][1]) or is_null($opts['prefix'][1])){$opts['prefix'][1] = $opts['prefix'][0];}

		if(is_string($opts['suffix'])) $opts['suffix'] = [$opts['suffix']];
		if(!isset($opts['suffix'][2]) or is_null($opts['suffix'][2])){$opts['suffix'][2] = $opts['suffix'][0];}
		if(!isset($opts['suffix'][1]) or is_null($opts['suffix'][1])){$opts['suffix'][1] = $opts['suffix'][0];}

		if(is_string($opts['union'])) $opts['union'] = [$opts['union']];
		if(is_null($opts['union'][0])) $opts['union'][0] = ' ';
		if(!isset($opts['union'][1]) or is_null($opts['union'][1])){$opts['union'][1] = $opts['union'][0];}

		$r.=$opts['prefix'][$t];

			if($c==0){}
		elseif($c==1){$r.=$array[0];}
		elseif($c>=2){
			$last = array_pop($array);
			$r.=implode($opts['union'][0], $array);
			$r.=$opts['union'][1].$last;
		}

		$r.=$opts['suffix'][$t];
		return $r;
	}
}

if ( ! function_exists('array_search2'))
{
	function array_search2($array, $filter_val, $filter_field = NULL, $return_field = NULL){
		$obj = [];

		if (is_null($filter_field))
		{
			$obj = array_search($filter_val, $array);
		}
		else
		{
			foreach($array as $arr)
			{
				if ($arr[$filter_field] == $filter_val)
				{
					$obj = $arr;
				}
			}
		}

		if (is_null($return_field))
		{
			return $obj;
		}

		isset($obj[$return_field]) or $obj[$return_field] = NULL;
		return $obj[$return_field];
	}
}

//====================================================================
// DATEs
//====================================================================

if ( ! function_exists('date_default_timezone_getUTC'))
{
	function date_default_timezone_getUTC(){
		trigger_error('FUNCTION WAS DEPRECATED, Use function `getUTC`', E_USER_DEPRECATED);
		return getUTC();
	}
}




function timeinstant(int $fecha, $now = NULL){
	if(is_null($now)) $now = time();
	$t = date("d/m/Y", $fecha);
	if($t==date("d/m/Y", $now)){
		$t=diffdates($fecha, $now);
		if($t<30){
			$t='Instante';
		}else{
			$t=explode(' ', convertir_tiempo($t, 'reducido'));
			$t=sprintf('%02d', $t[0]).' '.substr($t[1], 0, 3).'s';
		}
	}
	return $t;
}

$DATE_LANG = isset($_COOKIE['lang'])?$_COOKIE['lang']:'ES';
function date_iso8601($fecha_hora=NULL){
    preg_match('/(([0-9]{2,4})\-([0-9]{1,2})\-([0-9]{1,2}))*(\ )*(([0-9]{1,2})\:([0-9]{1,2})\:([0-9]{1,2}))*/', (string)$fecha_hora, $m);
    
    $m[2] = $m[2]*1;
    $m[3] = $m[3]*1;
    $m[4] = $m[4]*1;
    $m[7] = $m[7]*1;
    $m[8] = $m[8]*1;
    $m[9] = $m[9]*1;
    
    $t = '';
    if($m[2]>0 or $m[3]>0 or $m[4]>0 or $m[7]>0 or $m[8]>0 or $m[9]>0) $t.='P';
    if($m[2]>0) $t.=$m[2].'Y';
    if($m[3]>0) $t.=$m[3].'M';
    if($m[4]>0) $t.=$m[4].'D';
    if($m[7]>0 or $m[8]>0 or $m[9]>0) $t.='T';
    if($m[7]>0) $t.=$m[7].'H';
    if($m[8]>0) $t.=$m[8].'M';
    if($m[9]>0) $t.=$m[9].'S';
    return $t;
}

function date_str($str, $timestamp=NULL){
	if(is_array($str)) return array_map(function($v){return date_str($v);}, $str);
	if(is_null($timestamp)) $timestamp = time();
	elseif(!is_numeric($timestamp)) $timestamp = strtotime($timestamp);

	$ts = NULL;
	switch($str){
		//Palabras de StrToTime
		case 'this week':$ts = strtotime('this week');break;

		//Force date as now
		case 'now':case 'ahora':case 'today':case 'hoy':   $ts = time();                                     break;
		case 'tomorrow':case 'mañana':                     $ts = strtotime(date('Y-m-d H:i:s').' + 1 Day');  break;
		case 'yesterday':case 'ayer':                      $ts = strtotime(date('Y-m-d H:i:s').' - 1 Day');  break;
		case 'now start':case 'now_start':case 'now-start':$ts = strtotime(date('Y-m-d 00:00:00'));break;
		case 'now end':case 'now_end':case 'now-end':      $ts = strtotime(date('Y-m-d 23:59:59'));break;

		case 'this_week':case 'esta_semana':               
			$d = date('w');
			$fis = ($d==0?'':($d==1?' - 1 Day':(' - '.$d.' Days')));
			$ffs = ($d==6?'':($d==5?' + 1 Day':(' + '.(6-$d).' Days')));
			$ts = array(strtotime(date('Y-m-d 00:00:00').$fis), strtotime(date('Y-m-d 23:59:59').$ffs));
			break;
		case 'this_week_time':                             
			$d = date('w');
			$fis = ($d==0?'':($d==1?' - 1 Day':(' - '.$d.' Days')));
			$ffs = ($d==6?'':($d==5?' + 1 Day':(' + '.(6-$d).' Days')));
			$ts = array(strtotime(date('Y-m-d H:i:s').$fis), strtotime(date('Y-m-d H:i:s').$ffs));
			break;
		case 'this_week_str':                              
			$ini = strtotime(date('Y-m-d 00:00:00', strtotime('this week')));
			$ts = array($ini, strtotime(date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', $ini).' + 7 Days')).' - 1 Second'));
			break;
		case 'this_week_str_time':                         
			$ini = strtotime('this week');
			$ts = array($ini, strtotime(date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', $ini).' + 7 Days')).' - 1 Second'));
			break;
		case 'this_month':case 'este_mes':                 
			$ts = array(strtotime(date('Y-m-01 00:00:00')), strtotime(date('Y-m-'.date('t').' 23:59:59')));
			break;
		case 'last_month':case 'mes_pasado':               
			$ts = array(strtotime(date('Y-m-01 00:00:00', strtotime(date('Y-m-d').' - 1 Month'))), strtotime(date('Y-m-'.date('t', strtotime(date('Y-m-d').' - 1 Month')).' 23:59:59', strtotime(date('Y-m-d').' - 1 Month'))));
			break;
		case 'this_year':case 'este_año':                  
			$ts = array(strtotime(date('Y-01-01 00:00:00')), strtotime(date('Y-12-31 23:59:59')));
			break;
		case 'last_year':case 'año_pasado':                
			$ts = array(strtotime(date('Y-01-01 00:00:00', strtotime(date('Y-m-d').' - 1 Year'))), strtotime(date('Y-12-31 23:59:59', strtotime(date('Y-m-d').' - 1 Year'))));
			break;

		//The dateFrom
		case 'timestamp':case 'hora':                      $ts = $timestamp;                                  break;
		case 'day_start':                                  $ts = strtotime(date('Y-m-d 00:00:00', $timestamp));break;
		case 'day_end':                                    $ts = strtotime(date('Y-m-d 23:59:59', $timestamp));break;

		case 'that_week':                                  
			$d = date('w', $timestamp);
			$fis = ($d==0?'':($d==1?' - 1 Day':(' - '.$d.' Days')));
			$ffs = ($d==6?'':($d==5?' + 1 Day':(' + '.(6-$d).' Days')));
			$ts = array(strtotime(date('Y-m-d 00:00:00', $timestamp).$fis), strtotime(date('Y-m-d 23:59:59', $timestamp).$ffs));
			break;
		case 'that_week_time':                             
			$d = date('w', $timestamp);
			$fis = ($d==0?'':($d==1?' - 1 Day':(' - '.$d.' Days')));
			$ffs = ($d==6?'':($d==5?' + 1 Day':(' + '.(6-$d).' Days')));
			$ts = array(strtotime(date('Y-m-d H:i:s', $timestamp).$fis), strtotime(date('Y-m-d H:i:s', $timestamp).$ffs));
			break;
		case 'that_week_str':                              
			$ini = strtotime(date('Y-m-d 00:00:00', strtotime('this week', $timestamp)));
			$ts = array($ini, strtotime(date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', $ini).' + 7 Days')).' - 1 Second'));
			break;
		case 'that_week_str_time':                         
			$ini = strtotime('this week', $timestamp);
			$ts = array($ini, strtotime(date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', $ini).' + 7 Days')).' - 1 Second'));
			break;
		case 'that_month':                                 
			$ts = array(strtotime(date('Y-m-01 00:00:00', $timestamp)), strtotime(date('Y-m-'.date('t', $timestamp).' 23:59:59', $timestamp)));
			break;
		case 'that_year':                                  
			$ts = array(strtotime(date('Y-01-01 00:00:00', $timestamp)), strtotime(date('Y-12-31 23:59:59', $timestamp)));
			break;

		default:
			if(preg_match('/^(\-|\+)\=([\ ]*)([0-9]*)([\ ]*)(Second|Minute|Hour|Day|Week|Month|Year)(s){0,1}/i', $str, $matchs)){
				if($matchs[3]*1==1) $matchs[6] = ''; else $matchs[6] = 's';
				$ts = strtotime(date('Y-m-d H:i:s', $timestamp).' '.$matchs[1].' '.strtocapitalize($matchs[3]).' '.$matchs[5].$matchs[6]);
			}
			else
			if(preg_match('/^last([\ \_]+)([0-9]*)([\ \_]+)(Second|Minute|Hour|Day|Week|Month|Year)(s){0,1}([\ \_]*)(wt)*/i', $str, $matchs)){
				if(trim($matchs[7])=='') $timestamp = strtotime(date('Y-m-d 23:59:59', $timestamp));//die_array($timestamp);
				if($matchs[2]*1==1) $matchs[5] = ''; else $matchs[5] = 's';
				$ts = array(strtotime(date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', $timestamp).' - '.$matchs[2].' '.strtocapitalize($matchs[4]).$matchs[5])).' + 1 Second'), $timestamp);
			}
			else
			if(is_numeric($str)){
				$ts = $str;
			}
//			else
//			{
//				$ts = strtotime($str);
//			}
			break;
	}
	return $ts;
}

function date2($formato=NULL, $timestamp=NULL, $dateFrom=NULL){
	global $DATE_LANG;
	$fa = func_get_args();
	
	if(count($fa)==0){
		$formato=NULL;$timestamp=time();
	}elseif(count($fa)==1){
		$t0 = date_str($fa[0]);

			if( $t0){$formato=NULL;$timestamp=$t0;}
		elseif(!$t0){$formato=$fa[0];$timestamp=time();}

		unset($t0);
	}elseif(count($fa)==2){
		$t0 = date_str($fa[0]);
		$t1 = date_str($fa[1]);

		if( $fa[0]=='timestamp' and  $t1){$formato=$fa[0];if(is_array($t1))$t1=$t1[0];$timestamp=date_str($fa[0], $t1);$dateFrom=$t1;}
		elseif( $t0 and  $t1){$formato=NULL;if(is_array($t1))$t1=$t1[0];$timestamp=date_str($fa[0], $t1);$dateFrom=$t1;}
		elseif( $t0 and !$t1){$formato=$fa[1];$timestamp=$t0;}
		elseif(!$t0 and  $t1){$formato=$fa[0];$timestamp=$t1;}
		elseif(!$t0 and !$t1){$formato=$fa[0];$timestamp=time();}

		unset($t0);
		unset($t1);
	}elseif(count($fa)>=3){
		$t0 = date_str($fa[0]);
		$t1 = date_str($fa[1]);
		$t2 = date_str($fa[2]);

			if( $t0 and  $t1 and  $t2)
			{$formato=$fa[0];if(is_array($t2))$t2=$t2[0];$timestamp=date_str($fa[1], $t2);$dateFrom=$t2;}
		elseif( $t0 and  $t1 and !$t2)
			{$formato=$fa[2];if(is_array($t1))$t1=$t1[0];$timestamp=date_str($fa[0], $t1);$dateFrom=$t1;}
		elseif( $t0 and !$t1 and  $t2)
			{$formato=$fa[1];if(is_array($t2))$t2=$t2[0];$timestamp=date_str($fa[0], $t2);$dateFrom=$t2;}
		elseif( $t0 and !$t1 and !$t2)
			{$formato=$fa[1];$timestamp=$t0;}
		elseif(!$t0 and  $t1 and  $t2)
			{$formato=$fa[0];if(is_array($t2))$t2=$t2[0];$timestamp=date_str($fa[1], $t2);$dateFrom=$t2;}
		elseif(!$t0 and  $t1 and !$t2)
			{$formato=$fa[0];$timestamp=$t1;}
		elseif(!$t0 and !$t1 and  $t2)
			{$formato=$fa[0];$timestamp=$t2;}
		elseif(!$t0 and !$t1 and !$t2)
			{$formato=$fa[0];$timestamp=time();}

		unset($t0);
		unset($t1);
		unset($t2);
	}

	if(is_null($formato)){
		switch($timestamp){
			case 'this week':case 'now':case 'ahora':case 'now start':case 'now_start':case 'now-start':case 'now end':case 'now_end':case 'now-end':case 'this_week_time':case 'this_week_str_time':case 'day_start':case 'day_end':case 'that_week_time':case 'that_week_str_time':$formato = 'Y-m-d H:i:s';break;
			case 'today':case 'hoy':case 'tomorrow':case 'mañana':case 'yesterday':case 'ayer':case 'this_week':case 'esta_semana':case 'this_week_str':case 'this_month':case 'este_mes':case 'this_year':case 'este_año':case 'that_week':case 'that_week_str':case 'that_month':case 'that_year':case 'last_month':case 'mes_pasado':case 'last_year':case 'año_pasado':$formato = 'Y-m-d';break;
			case 'timestamp':$formato = 'timestamp';break;
			case 'hora':$formato = 'H:i:s';break;
			
			default:$formato = 'Y-m-d H:i:s';	break;
		}
	}
	switch($formato){
		case 'LL':$formato = 'd "de" vmn "de" Y';break;
	}
	
	if(is_array($timestamp)){foreach($timestamp as $x=>$y){$timestamp[$x] = date_($formato, $y);}return $timestamp;}
	elseif(strtolower($formato)=='timestamp'){return $timestamp;}
	elseif(strtolower($formato)=='iso8601'){return date_iso8601(date_('Y-m-d H:i:s', $timestamp));}
	else{
		$r = '';
		$s = str_split($formato);
		$d = '';
		for($x=0;$x<count($s);$x++){
			$c = $s[$x];
			if($c=='\\'){
				$r.=$s[$x+1];
				$x++;
			}elseif($c=='"' or $c=='\''){
				$x_ =1;
				$t = '';
				while($s[$x+$x_]<>$c){$t.=$s[$x+$x_];$x_++;}
				$r.=_t($t, $DATE_LANG);
				$x+=$x_;
			}elseif(preg_match('/[a-zA-Z]/', $c)){
				$d.=$c;
				if(!((count($s)-1)==$x) and preg_match('/[a-zA-Z]/', $s[$x+1])) continue;
				
				switch($d){
					case 'de' : $r.=_t('de', $DATE_LANG); break;
					case 'del': $r.=_t('del', $DATE_LANG); break;
					case 'vmn': $r.=mes(date('m', $timestamp), 'normal', $DATE_LANG); break;
					case 'vmm': $r.=mes(date('m', $timestamp), 'min'   , $DATE_LANG); break;
					case 'vdn': $r.=dia(date('w', $timestamp), 'normal', $DATE_LANG); break;
					case 'vdm': $r.=dia(date('w', $timestamp), 'min'   , $DATE_LANG); break;
					case 'vdmn':$r.=dia(date('w', $timestamp), 'vmin'  , $DATE_LANG); break;
					default: $r.=date($d, $timestamp); break;
				}
				$d='';
			}else{
				$r.=$c;
			}
		}
		return $r;
	}

}
function date_($formato=NULL, $timestamp=NULL, $dateFrom=NULL){return date2($formato, $timestamp, $dateFrom);}

function date_recognize($date, $returnFormat = NULL){
	if(is_empty($date)){
		return NULL;
	}
	
	if(preg_match('/^\d{4}[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/', $date)){
		$this_format = 'Y-m-d';
	}else
	if(preg_match('/^\d{4}[-](0[1-9]|1[012])[-]([1-9]|[12][0-9]|3[01])$/', $date)){
		$this_format = 'Y-m-j';
	}else
	if(preg_match('/^\d{4}[-]([1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/', $date)){
		$this_format = 'Y-n-d';
	}else
	if(preg_match('/^\d{4}[-]([1-9]|1[012])[-]([1-9]|[12][0-9]|3[01])$/', $date)){
		$this_format = 'Y-n-j';
	}else
	if(preg_match('/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/\d{4}$/', $date)){
		$this_format = 'd/m/Y';
	}else
	if(preg_match('/^(0[1-9]|[12][0-9]|3[01])\/(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\/\d{4}$/', $date)){
		$this_format = 'd/F/Y';
	}else
	if(preg_match('/^(0[1-9]|[12][0-9]|3[01])\/(January|February|March|April|May|June|July|August|September|October|November|December)\/\d{4}$/', $date)){
		$this_format = 'd/M/Y';
	}else
	{
		return NULL;//Formato no reconocido
	}
	
	$date = date_create_from_format($this_format, $date);
	
	if(is_null($returnFormat)){
		$returnFormat = $this_format;
	}
	return date_($returnFormat, strtotime($date->format('Y-m-d H:i:s')));
}

function diffdates($fecha_mayor='now_end', $fecha_menor='now', $possitive=true){
    $fecha_mayor=date_($fecha_mayor);
    $fecha_menor=date_($fecha_menor);
    
    $fecha_mayor = strtotime($fecha_mayor);
    $fecha_menor = strtotime($fecha_menor);
    
    if($possitive and $fecha_menor>$fecha_mayor){
        $fecha_temp = $fecha_mayor;
        $fecha_mayor = $fecha_menor;
        $fecha_menor = $fecha_temp;
        unset($fecha_temp);
    }
    
    $diff = $fecha_mayor - $fecha_menor;
    
    return $diff;
}

//Convertir Tiempo
//$return = array|completo|reducido
function  convertir_tiempo($seg, $return = 'array', $inverted = true, $txtplu = array('segundos', 'minutos', 'horas', 'dias', 'semanas', 'meses', 'años'), $txtsing = array('segundo', 'minuto', 'hora', 'dia', 'semana', 'mes', 'año') ){
    $r = array('sg'=>round($seg));
    
    $r['mi'] = floor($r['sg']/60); $r['sg'] -= $r['mi']*60;
    $r['ho'] = floor($r['mi']/60); $r['mi'] -= $r['ho']*60;
    $r['di'] = floor($r['ho']/24); $r['ho'] -= $r['di']*24;
    $r['se'] = floor($r['di']/7 ); $r['di'] -= $r['se']*7 ;
    $r['me'] = floor($r['se']/4 ); $r['se'] -= $r['me']*4 ;
    $r['añ'] = floor($r['me']/12); $r['me'] -= $r['añ']*12;
    
    $obl = false;
    
    if ($r['añ']<>0 or $obl) $obl = true;
    $r['añ'] = array($r['añ'], $r['añ']==1?$txtsing[6]:$txtplu[6], $obl);
    
    if ($r['me']<>0 or $obl) $obl = true;
    $r['me'] = array($r['me'], $r['me']==1?$txtsing[5]:$txtplu[5], $obl);
    
    if ($r['se']<>0 or $obl) $obl = true;
    $r['se'] = array($r['se'], $r['se']==1?$txtsing[4]:$txtplu[4], $obl);
    
    if ($r['di']<>0 or $obl) $obl = true;
    $r['di'] = array($r['di'], $r['di']==1?$txtsing[3]:$txtplu[3], $obl);
    
    if ($r['ho']<>0 or $obl) $obl = true;
    $r['ho'] = array($r['ho'], $r['ho']==1?$txtsing[2]:$txtplu[2], $obl);
    
    if ($r['mi']<>0 or $obl) $obl = true;
    $r['mi'] = array($r['mi'], $r['mi']==1?$txtsing[1]:$txtplu[1], $obl);
    
    if ($r['sg']<>0 or $obl) $obl = true;    
    $r['sg'] = array($r['sg'], $r['sg']==1?$txtsing[0]:$txtplu[0], $obl);
    
    if($inverted){
        $r = array_merge(array('añ'=>array(), 'me'=>array(), 'se'=>array(), 'di'=>array(), 'ho'=>array(), 'mi'=>array(), 'sg'=>array() ), $r);
    }
    
    if($return=='array'){
        return $r;
    }
    
    $s = '';
    foreach($r as $x=>$y){
        if(!$y[2] and $return=='reducido') continue;
        $s .= ($s==''?'':' ').$y[0].' '.$y[1];
    }
    
    return $s;
}


//====================================================================
// STRINGs
//====================================================================

if ( ! function_exists('remove_invisible_characters'))
{
	/**
	 * Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function remove_invisible_characters($str, $url_encoded = TRUE)
	{
		$non_displayables = array();

		// every control character except newline (dec 10),
		// carriage return (dec 13) and horizontal tab (dec 09)
		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/i';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/i';	// url encoded 16-31
			$non_displayables[] = '/%7f/i';	// url encoded 127
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}
}

if ( ! function_exists('utf8'))
{
	function utf8 ($str, $encoding_from = NULL, $iso = FALSE)
	{
		if ( ! defined('UTF8_ENABLED') OR UTF8_ENABLED === FALSE)
		{
			return $str;
		}
		
		$str_original = $str;
		
		if (is_null($encoding_from))
		{
			$encoding_from = mb_detect_encoding($str, 'auto');
		}
		
		$encoding_to = 'UTF-8';
		if ($iso)
		{
			$encoding_to = 'ISO-8859-1';
		}
		
		$str = mb_convert_encoding($str, $encoding_to, $encoding_from);
//		$str = @iconv($encoding_from, $encoding_to, $str);
		
		if (empty($str))
		{
			$str = $str_original;
		}
		
		return $str;
	}
}

if ( ! function_exists('clean_str'))
{
	function clean_str($str)
	{
		if ( ! defined('UTF8_ENABLED') OR UTF8_ENABLED === FALSE)
		{
			return $str;
		}
		
		if ( ! is_ascii($str))
		{
			if (MB_ENABLED)
			{
				$str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
			}
			elseif (ICONV_ENABLED)
			{
				$str = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
			}
		}

		return $str;
	}
}

if ( ! function_exists('replace_tildes'))
{
	function replace_tildes($str, $numbers_to_letters = FALSE)
	{
		$foreign_characters = [
			'/ä|æ|ǽ/' => 'ae',
			'/ö|œ/' => 'oe',
			'/ü/' => 'ue',
			'/Ä/' => 'Ae',
			'/Ü/' => 'Ue',
			'/Ö/' => 'Oe',
			'/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|Α|Ά|Ả|Ạ|Ầ|Ẫ|Ẩ|Ậ|Ằ|Ắ|Ẵ|Ẳ|Ặ|А/' => 'A',
			'/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|α|ά|ả|ạ|ầ|ấ|ẫ|ẩ|ậ|ằ|ắ|ẵ|ẳ|ặ|а/' => 'a',
			'/Б/' => 'B',
			'/б/' => 'b',
			'/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
			'/ç|ć|ĉ|ċ|č/' => 'c',
			'/Д/' => 'D',
			'/д/' => 'd',
			'/Ð|Ď|Đ|Δ/' => 'Dj',
			'/ð|ď|đ|δ/' => 'dj',
			'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Ε|Έ|Ẽ|Ẻ|Ẹ|Ề|Ế|Ễ|Ể|Ệ|Е|Э/' => 'E',
			'/è|é|ê|ë|ē|ĕ|ė|ę|ě|έ|ε|ẽ|ẻ|ẹ|ề|ế|ễ|ể|ệ|е|э/' => 'e',
			'/Ф/' => 'F',
			'/ф/' => 'f',
			'/Ĝ|Ğ|Ġ|Ģ|Γ|Г|Ґ/' => 'G',
			'/ĝ|ğ|ġ|ģ|γ|г|ґ/' => 'g',
			'/Ĥ|Ħ/' => 'H',
			'/ĥ|ħ/' => 'h',
			'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|Η|Ή|Ί|Ι|Ϊ|Ỉ|Ị|И|Ы/' => 'I',
			'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|η|ή|ί|ι|ϊ|ỉ|ị|и|ы|ї/' => 'i',
			'/Ĵ/' => 'J',
			'/ĵ/' => 'j',
			'/Ķ|Κ|К/' => 'K',
			'/ķ|κ|к/' => 'k',
			'/Ĺ|Ļ|Ľ|Ŀ|Ł|Λ|Л/' => 'L',
			'/ĺ|ļ|ľ|ŀ|ł|λ|л/' => 'l',
			'/М/' => 'M',
			'/м/' => 'm',
			'/Ñ|Ń|Ņ|Ň|Ν|Н/' => 'N',
			'/ñ|ń|ņ|ň|ŉ|ν|н/' => 'n',
			'/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|Ο|Ό|Ω|Ώ|Ỏ|Ọ|Ồ|Ố|Ỗ|Ổ|Ộ|Ờ|Ớ|Ỡ|Ở|Ợ|О/' => 'O',
			'/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|ο|ό|ω|ώ|ỏ|ọ|ồ|ố|ỗ|ổ|ộ|ờ|ớ|ỡ|ở|ợ|о/' => 'o',
			'/П/' => 'P',
			'/п/' => 'p',
			'/Ŕ|Ŗ|Ř|Ρ|Р/' => 'R',
			'/ŕ|ŗ|ř|ρ|р/' => 'r',
			'/Ś|Ŝ|Ş|Ș|Š|Σ|С/' => 'S',
			'/ś|ŝ|ş|ș|š|ſ|σ|ς|с/' => 's',
			'/Ț|Ţ|Ť|Ŧ|τ|Т/' => 'T',
			'/ț|ţ|ť|ŧ|т/' => 't',
			'/Þ|þ/' => 'th',
			'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|Ũ|Ủ|Ụ|Ừ|Ứ|Ữ|Ử|Ự|У/' => 'U',
			'/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|υ|ύ|ϋ|ủ|ụ|ừ|ứ|ữ|ử|ự|у/' => 'u',
			'/Ƴ|Ɏ|Ỵ|Ẏ|Ӳ|Ӯ|Ў|Ý|Ÿ|Ŷ|Υ|Ύ|Ϋ|Ỳ|Ỹ|Ỷ|Ỵ|Й/' => 'Y',
			'/ẙ|ʏ|ƴ|ɏ|ỵ|ẏ|ӳ|ӯ|ў|ý|ÿ|ŷ|ỳ|ỹ|ỷ|ỵ|й/' => 'y',
			'/В/' => 'V',
			'/в/' => 'v',
			'/Ŵ/' => 'W',
			'/ŵ/' => 'w',
			'/×/' => 'x',
			'/Ź|Ż|Ž|Ζ|З/' => 'Z',
			'/ź|ż|ž|ζ|з/' => 'z',
			'/Æ|Ǽ/' => 'AE',
			'/ß/' => 'ss',
			'/Ĳ/' => 'IJ',
			'/ĳ/' => 'ij',
			'/Œ/' => 'OE',
			'/ƒ/' => 'f',
			'/ξ/' => 'ks',
			'/π/' => 'p',
			'/β/' => 'v',
			'/μ/' => 'm',
			'/ψ/' => 'ps',
			'/Ё/' => 'Yo',
			'/ё/' => 'yo',
			'/Є/' => 'Ye',
			'/є/' => 'ye',
			'/Ї/' => 'Yi',
			'/Ж/' => 'Zh',
			'/ж/' => 'zh',
			'/Х/' => 'Kh',
			'/х/' => 'kh',
			'/Ц/' => 'Ts',
			'/ц/' => 'ts',
			'/Ч/' => 'Ch',
			'/ч/' => 'ch',
			'/Ш/' => 'Sh',
			'/ш/' => 'sh',
			'/Щ/' => 'Shch',
			'/щ/' => 'shch',
			'/Ъ|ъ|Ь|ь/' => '',
			'/Ю/' => 'Yu',
			'/ю/' => 'yu',
			'/Я/' => 'Ya',
			'/я/' => 'ya',
			
			'/@/' => 'a',
			'/¢|©/' => 'c',
			'/€|£/' => 'E',
			'/ⁿ/' => 'n',
			'/°/' => 'o',
			'/¶|₧/' => 'P',
			'/®/' => 'R',
			'/\$/' => 'S',
			'/§/' => 's',
			'/¥/' => 'Y',
			'/&/' => 'y',
			
			'/¹/' => $numbers_to_letters ? 'I' : '1',
			'/²/' => $numbers_to_letters ? 'S' : '2',
			'/³/' => $numbers_to_letters ? 'E' : '3'
		];

		if ($numbers_to_letters)
		{
			$foreign_characters['/1/'] = 'I';
			$foreign_characters['/2/'] = 'S';
			$foreign_characters['/3/'] = 'E';
			$foreign_characters['/4/'] = 'A';
			$foreign_characters['/5/'] = 'S';
			$foreign_characters['/6/'] = 'G';
			$foreign_characters['/7/'] = 'T';
			$foreign_characters['/8/'] = 'B';
			$foreign_characters['/9/'] = 'g';
			$foreign_characters['/0/'] = 'O';
		}

		$array_from = array_keys($foreign_characters);
		$array_to   = array_values($foreign_characters);

		return preg_replace($array_from, $array_to, $str);
	}
}

if ( ! function_exists('quotes_to_entities'))
{
	/**
	 * Quotes to Entities
	 *
	 * Converts single and double quotes to entities
	 *
	 * @param	string
	 * @return	string
	 */
	function quotes_to_entities($str)
	{
		return str_replace(["\'","\"","'",'"'], ["&#39;","&quot;","&#39;","&quot;"], $str);
	}
}

if ( ! function_exists('reduce_double_slashes'))
{
	/**
	 * Reduce Double Slashes
	 *
	 * Converts double slashes in a string to a single slash,
	 * except those found in http://
	 *
	 * http://www.some-site.com//index.php
	 *
	 * becomes:
	 *
	 * http://www.some-site.com/index.php
	 *
	 * @param	string
	 * @return	string
	 */
	function reduce_double_slashes($str)
	{
		return preg_replace('#(^|[^:])//+#', '\\1/', $str);
	}
}

if ( ! function_exists('reduce_multiples'))
{
	/**
	 * Reduce Multiples
	 *
	 * Reduces multiple instances of a particular character.  Example:
	 *
	 * Fred, Bill,, Joe, Jimmy
	 *
	 * becomes:
	 *
	 * Fred, Bill, Joe, Jimmy
	 *
	 * @param	string
	 * @param	string	the character you wish to reduce
	 * @param	bool	TRUE/FALSE - whether to trim the character from the beginning/end
	 * @return	string
	 */
	function reduce_multiples($str, $character = ',', $trim = FALSE)
	{
		$str = preg_replace('#' . preg_quote($character, '#') . '{2,}#', $character, $str);
		return ($trim === TRUE) ? trim($str, $character) : $str;
	}
}

if ( ! function_exists('strtoslug'))
{
	function strtoslug ($str, $separator = '-', $_allows = ['.', '-', '_']){
		$slug = $str;

		if(is_empty($_allows))
		{
			$_allows = [];
		}

		$_allows = (array)$_allows;
		$_allows[] = $separator;

		$slug = replace_tildes($slug);
		foreach((array)simbolos(NULL, TRUE) as $char)
		{
			$slug = reduce_multiples($slug, $char);
		}

		if (UTF8_ENABLED)
		{
			$slug = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
		}

		$slug = mb_strtolower($slug);

		$_regex = '[^a-z0-9' . implode('', array_map('regex', $_allows)) . ']';

		$slug = preg_replace('#' . $_regex . '#', $separator, $slug);

		foreach($_allows as $char)
		{
			$slug = reduce_multiples($slug, $char);
			$slug = trim($slug, $char);
		}

		return $slug;
	}
}

if ( ! function_exists('strtocapitalize'))
{
	function strtocapitalize ($str = '')
	{
		return ucwords(mb_strtolower($str));
	}
}

if ( ! function_exists('strtobool'))
{
	function strtobool ($str = '', $empty = FALSE)
	{
		if (is_empty($str))
		{
			return $empty;
		}
		
		if (is_bool($str))
		{
			return $str;
		}
		
		$str = (string)$str;
		
		if (preg_match('/^(s|y|v|t|1)/i', $str))
		{
			return TRUE;
		}
		
		if (preg_match('/^(n|f|0)/i', $str))
		{
			return FALSE;
		}
		
		return !$empty;
	}
}

if ( ! function_exists('strtonumber'))
{
	function strtonumber ($str = '')
	{
		$str = (string)$str;
		$str = preg_replace('/[^0-9\.]/i', '', $str);
		
		$str = (double)$str;
		return $str;
	}
}






//Transformar Tamaño
function transform_size( $tam ){
    $tam = round($tam);
    $tb  = floor($tam/1e12); $tam-=($tb*1e12);
    $gb  = floor($tam/1e9 ); $tam-=($gb*1e9);
    $mb  = floor($tam/1e6 ); $tam-=($mb*1e6);
    $kb  = floor($tam/1e3 ); $tam-=($kb*1e3);
    $b   = $tam;
    $r   = '';
    if ($tb<>0) $r .= ($r<>''?' ':'').$tb.' TB';
    if ($gb<>0 or $tb<>0) $r .= ($r<>''?' ':'').$gb.' GB';
    if ($mb<>0 or $gb<>0 or $tb<>0) $r .= ($r<>''?' ':'').$mb.' MB';
    if ($kb<>0 or $mb<>0 or $gb<>0 or $tb<>0) $r .= ($r<>''?' ':'').$kb.' KB';
    if ($b <>0 or $kb<>0 or $mb<>0 or $gb<>0 or $tb<>0) $r .= ($r<>''?' ':''). $b.' B';
    
    return $r;
}


function jys_rd($string, $array=array()){
	if(is_callable($array)) $array = $array($string);
	if(!is_array($array)){$array = (array)$array;}
	foreach($array as $i=>$v){
		$string = str_replace('{{'.$i.'}}', $v, $string);
	}
	if(preg_match('/\{\{([^}])\}\}/', $string)){//una penúltima pasada
		foreach($array as $i=>$v){
			$string = str_replace('{{'.$i.'}}', $v, $string);
		}
	}
	if(preg_match('/\{\{([^}])\}\}/', $string)){//una última pasada
		foreach($array as $i=>$v){
			$string = str_replace('{{'.$i.'}}', $v, $string);
		}
	}
	return $string;
}
