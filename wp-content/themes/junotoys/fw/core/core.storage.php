<?php
/**
 * Juno Toys Framework: theme variables storage
 *
 * @package	junotoys
 * @since	junotoys 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Get theme variable
if (!function_exists('junotoys_storage_get')) {
	function junotoys_storage_get($var_name, $default='') {
		global $JUNOTOYS_STORAGE;
		return isset($JUNOTOYS_STORAGE[$var_name]) ? $JUNOTOYS_STORAGE[$var_name] : $default;
	}
}

// Set theme variable
if (!function_exists('junotoys_storage_set')) {
	function junotoys_storage_set($var_name, $value) {
		global $JUNOTOYS_STORAGE;
		$JUNOTOYS_STORAGE[$var_name] = $value;
	}
}

// Check if theme variable is empty
if (!function_exists('junotoys_storage_empty')) {
	function junotoys_storage_empty($var_name, $key='', $key2='') {
		global $JUNOTOYS_STORAGE;
		if (!empty($key) && !empty($key2))
			return empty($JUNOTOYS_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return empty($JUNOTOYS_STORAGE[$var_name][$key]);
		else
			return empty($JUNOTOYS_STORAGE[$var_name]);
	}
}

// Check if theme variable is set
if (!function_exists('junotoys_storage_isset')) {
	function junotoys_storage_isset($var_name, $key='', $key2='') {
		global $JUNOTOYS_STORAGE;
		if (!empty($key) && !empty($key2))
			return isset($JUNOTOYS_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return isset($JUNOTOYS_STORAGE[$var_name][$key]);
		else
			return isset($JUNOTOYS_STORAGE[$var_name]);
	}
}

// Inc/Dec theme variable with specified value
if (!function_exists('junotoys_storage_inc')) {
	function junotoys_storage_inc($var_name, $value=1) {
		global $JUNOTOYS_STORAGE;
		if (empty($JUNOTOYS_STORAGE[$var_name])) $JUNOTOYS_STORAGE[$var_name] = 0;
		$JUNOTOYS_STORAGE[$var_name] += $value;
	}
}

// Concatenate theme variable with specified value
if (!function_exists('junotoys_storage_concat')) {
	function junotoys_storage_concat($var_name, $value) {
		global $JUNOTOYS_STORAGE;
		if (empty($JUNOTOYS_STORAGE[$var_name])) $JUNOTOYS_STORAGE[$var_name] = '';
		$JUNOTOYS_STORAGE[$var_name] .= $value;
	}
}

// Get array (one or two dim) element
if (!function_exists('junotoys_storage_get_array')) {
	function junotoys_storage_get_array($var_name, $key, $key2='', $default='') {
		global $JUNOTOYS_STORAGE;
		if (empty($key2))
			return !empty($var_name) && !empty($key) && isset($JUNOTOYS_STORAGE[$var_name][$key]) ? $JUNOTOYS_STORAGE[$var_name][$key] : $default;
		else
			return !empty($var_name) && !empty($key) && isset($JUNOTOYS_STORAGE[$var_name][$key][$key2]) ? $JUNOTOYS_STORAGE[$var_name][$key][$key2] : $default;
	}
}

// Set array element
if (!function_exists('junotoys_storage_set_array')) {
	function junotoys_storage_set_array($var_name, $key, $value) {
		global $JUNOTOYS_STORAGE;
		if (!isset($JUNOTOYS_STORAGE[$var_name])) $JUNOTOYS_STORAGE[$var_name] = array();
		if ($key==='')
			$JUNOTOYS_STORAGE[$var_name][] = $value;
		else
			$JUNOTOYS_STORAGE[$var_name][$key] = $value;
	}
}

// Set two-dim array element
if (!function_exists('junotoys_storage_set_array2')) {
	function junotoys_storage_set_array2($var_name, $key, $key2, $value) {
		global $JUNOTOYS_STORAGE;
		if (!isset($JUNOTOYS_STORAGE[$var_name])) $JUNOTOYS_STORAGE[$var_name] = array();
		if (!isset($JUNOTOYS_STORAGE[$var_name][$key])) $JUNOTOYS_STORAGE[$var_name][$key] = array();
		if ($key2==='')
			$JUNOTOYS_STORAGE[$var_name][$key][] = $value;
		else
			$JUNOTOYS_STORAGE[$var_name][$key][$key2] = $value;
	}
}

// Add array element after the key
if (!function_exists('junotoys_storage_set_array_after')) {
	function junotoys_storage_set_array_after($var_name, $after, $key, $value='') {
		global $JUNOTOYS_STORAGE;
		if (!isset($JUNOTOYS_STORAGE[$var_name])) $JUNOTOYS_STORAGE[$var_name] = array();
		if (is_array($key))
			junotoys_array_insert_after($JUNOTOYS_STORAGE[$var_name], $after, $key);
		else
			junotoys_array_insert_after($JUNOTOYS_STORAGE[$var_name], $after, array($key=>$value));
	}
}

// Add array element before the key
if (!function_exists('junotoys_storage_set_array_before')) {
	function junotoys_storage_set_array_before($var_name, $before, $key, $value='') {
		global $JUNOTOYS_STORAGE;
		if (!isset($JUNOTOYS_STORAGE[$var_name])) $JUNOTOYS_STORAGE[$var_name] = array();
		if (is_array($key))
			junotoys_array_insert_before($JUNOTOYS_STORAGE[$var_name], $before, $key);
		else
			junotoys_array_insert_before($JUNOTOYS_STORAGE[$var_name], $before, array($key=>$value));
	}
}

// Push element into array
if (!function_exists('junotoys_storage_push_array')) {
	function junotoys_storage_push_array($var_name, $key, $value) {
		global $JUNOTOYS_STORAGE;
		if (!isset($JUNOTOYS_STORAGE[$var_name])) $JUNOTOYS_STORAGE[$var_name] = array();
		if ($key==='')
			array_push($JUNOTOYS_STORAGE[$var_name], $value);
		else {
			if (!isset($JUNOTOYS_STORAGE[$var_name][$key])) $JUNOTOYS_STORAGE[$var_name][$key] = array();
			array_push($JUNOTOYS_STORAGE[$var_name][$key], $value);
		}
	}
}

// Pop element from array
if (!function_exists('junotoys_storage_pop_array')) {
	function junotoys_storage_pop_array($var_name, $key='', $defa='') {
		global $JUNOTOYS_STORAGE;
		$rez = $defa;
		if ($key==='') {
			if (isset($JUNOTOYS_STORAGE[$var_name]) && is_array($JUNOTOYS_STORAGE[$var_name]) && count($JUNOTOYS_STORAGE[$var_name]) > 0)
				$rez = array_pop($JUNOTOYS_STORAGE[$var_name]);
		} else {
			if (isset($JUNOTOYS_STORAGE[$var_name][$key]) && is_array($JUNOTOYS_STORAGE[$var_name][$key]) && count($JUNOTOYS_STORAGE[$var_name][$key]) > 0)
				$rez = array_pop($JUNOTOYS_STORAGE[$var_name][$key]);
		}
		return $rez;
	}
}

// Inc/Dec array element with specified value
if (!function_exists('junotoys_storage_inc_array')) {
	function junotoys_storage_inc_array($var_name, $key, $value=1) {
		global $JUNOTOYS_STORAGE;
		if (!isset($JUNOTOYS_STORAGE[$var_name])) $JUNOTOYS_STORAGE[$var_name] = array();
		if (empty($JUNOTOYS_STORAGE[$var_name][$key])) $JUNOTOYS_STORAGE[$var_name][$key] = 0;
		$JUNOTOYS_STORAGE[$var_name][$key] += $value;
	}
}

// Concatenate array element with specified value
if (!function_exists('junotoys_storage_concat_array')) {
	function junotoys_storage_concat_array($var_name, $key, $value) {
		global $JUNOTOYS_STORAGE;
		if (!isset($JUNOTOYS_STORAGE[$var_name])) $JUNOTOYS_STORAGE[$var_name] = array();
		if (empty($JUNOTOYS_STORAGE[$var_name][$key])) $JUNOTOYS_STORAGE[$var_name][$key] = '';
		$JUNOTOYS_STORAGE[$var_name][$key] .= $value;
	}
}

// Call object's method
if (!function_exists('junotoys_storage_call_obj_method')) {
	function junotoys_storage_call_obj_method($var_name, $method, $param=null) {
		global $JUNOTOYS_STORAGE;
		if ($param===null)
			return !empty($var_name) && !empty($method) && isset($JUNOTOYS_STORAGE[$var_name]) ? $JUNOTOYS_STORAGE[$var_name]->$method(): '';
		else
			return !empty($var_name) && !empty($method) && isset($JUNOTOYS_STORAGE[$var_name]) ? $JUNOTOYS_STORAGE[$var_name]->$method($param): '';
	}
}

// Get object's property
if (!function_exists('junotoys_storage_get_obj_property')) {
	function junotoys_storage_get_obj_property($var_name, $prop, $default='') {
		global $JUNOTOYS_STORAGE;
		return !empty($var_name) && !empty($prop) && isset($JUNOTOYS_STORAGE[$var_name]->$prop) ? $JUNOTOYS_STORAGE[$var_name]->$prop : $default;
	}
}

// Merge two-dim array element
if (!function_exists('junotoys_storage_merge_array')) {
    function junotoys_storage_merge_array($var_name, $key, $arr) {
        global $JUNOTOYS_STORAGE;
        if (!isset($JUNOTOYS_STORAGE[$var_name])) $JUNOTOYS_STORAGE[$var_name] = array();
        if (!isset($JUNOTOYS_STORAGE[$var_name][$key])) $JUNOTOYS_STORAGE[$var_name][$key] = array();
        $JUNOTOYS_STORAGE[$var_name][$key] = array_merge($JUNOTOYS_STORAGE[$var_name][$key], $arr);
    }
}
?>