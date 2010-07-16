<?php

$alerts = array();
$invalid = array();

abstract class Form
{
	public static function init()
	{
		global $fields;
		
		/* Ingest query parameters */
		foreach($fields as $k => $v)
		{
			if(isset($_REQUEST[$k]) && !is_array($_REQUEST[$k]))
			{
				$v = trim($_REQUEST[$k]);
				if(get_magic_quotes_gpc())
				{
					$v = stripslashes($v);
				}
				$fields[$k] = $v;
			}
		}
	}
	
	public static function prepareOutput()
	{
		global $fields;
		
		/* Output */
		
		foreach($fields as $k => $v)
		{
			$fields[$k] = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
		}	
	}
	
	public static function alerts()
	{
		global $alerts;
		
		if(count($alerts))
		{
			echo "\t\t" . '<ul class="alerts">' . "\n";
			foreach($alerts as $msg)
			{
				echo "\t\t\t" . '<li>' . $msg . '</li>' . "\n";
			}
			echo "\t\t" . '</ul>' . "\n";
		}
	
	}
	
	public static function input($name, $label = null, $type = 'text', $class = 'field', $value = null)
	{
		global $fields, $invalid;
		
		if(!strlen($label))
		{
			$label = $name . ':';
		}
		if(in_array($name, $invalid))
		{
			$class .= ' error';
		}
		if(strlen($fields[$name]))
		{
			$value = $fields[$name];
		}
		else
		{
			$value = htmlspecialchars($value);
		}
		echo "\t\t\t" . '<dl class="' . $class . '" id="f-' . $name . '">' . "\n";
		echo "\t\t\t\t" . '<dt><label for="' . $name . '">' . $label . '</label></dt>' . "\n";
		echo "\t\t\t\t" . '<dd><input type="' . $type . '" name="' . $name . '" id="' . $name . '" value="' . $value . '"></dd>' . "\n";
		echo "\t\t\t" . '</dl>' . "\n";
	}

}

Form::init();
