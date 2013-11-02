<?php
namespace PurpleKiwi;

/**
 * Namespaced version of the Purple Kiwi enumration extention.
 * 
 * @abstract
 * @access public
 * @author Emil Carlsson <info@code-monkey.se>
 * @package PurpleKiwi
 * @version 1.0-beta
 */
abstract class Enum {
	/**
	 * Returns all constants as an array where the value is constant name and name is the corresponding number.
	 * @access public
	 * @static
	 * @return array
	 */
	public static function GetNames() {
		$reflection = new \ReflectionClass(get_called_class());
		
		$result = $reflection->getConstants();
		$keys = array();
		asort($result);
		foreach ($result as $name => $number) {
			$keys[$number] = $name;
		}
		return $keys;
	}
	
	/**
	 * Return a specific number associated with name.
	 * @access public
	 * @static
	 * @param string $name The string part of an enumeration.
	 * @param bool $casesensitive If the search should be preformed case sensitive. Default value true.
	 * @return int
	 */
	public static function GetNumber($name, $casesensitive = true) {
		$reflection = new \ReflectionClass(get_called_class());
		
		if ($reflection->hasConstant($name))
			return $reflection->getConstant($name);
		elseif (!$casesensitive && $reflection->hasConstant(strtolower($name)))
			return $reflection->getConstant(strtolower($name));
		elseif (!$casesensitive && $reflection->hasConstant(strtoupper($name)))
			return $reflection->getConstant(strtoupper($name));	
		
		
		/**
		 * @throws
		 */
		throw new Exception("Key $name not found");
	}
	
	/**
	 * Return a specific name by number.
	 * @access public
	 * @static
	 * @param int $number The value to compare against.
	 * @return string
	 */
	public static function GetName($number) {
		$reflection = new \ReflectionClass(get_called_class());
		
		$result = $reflection->getConstants();
		
		foreach ($result as $k => $v) {
			if ($v == $number)
				return $k;
		}
		
		/**
		 * @throws
		 */
		throw new \Exception("Value $number not found");
	}
	
	/**
	 * Return the enumeration as an array where key is name and corresponding number is value.
	 * @access public
	 * @static
	 * @return array
	 */
	public static function GetNameNumberPair() {
		$reflection = new \ReflectionClass(get_called_class());
		
		$return = $reflection->getConstants();
		asort($return);
		
		return $return;
	}
	
	/**
	 * To see if a value exists.
	 * @access public
	 * @static
	 * @param int $number Number to look for.
	 * @return bool
	 */
	public static function HasNumber($number) {
		$reflection = new \ReflectionClass(get_called_class());
		
		$result = $reflection->GetConstants();
		
		foreach($result as $v) {
			if ($v == $number)
				return true;
		}
		
		return false;
	}
	
	/**
	 * To see if a name exists
	 * @access public
	 * @static
	 * @param string $name The name to look for (case sensitive).
	 * @return bool
	 */
	public static function HasName($name) {
		$reflection = new \ReflectionClass(get_called_class());
		
		return $reflection->hasConstant($name);
	}
	
	/**
	 * To get the highest number in the enumeration
	 * @access public
	 * @static
	 * @return int
	 */
	public static function HighestEnumeration() {
		$reflection = new \ReflectionClass(get_called_class());
		
		$result = $reflection->getConstants();
		asort($result);
		
		return end($result);
	}
	
	/**
	 * To get the lowest number in the enumeration
	 * @access public
	 * @static
	 * @return int
	 */
	public static function LowestEnumeration() {
		$reflection = new \ReflectionClass(get_called_class());
		
		$result = $reflection->getConstants();
		asort($result);
		$result = array_values($result);
		return array_shift($result);
	}
}
