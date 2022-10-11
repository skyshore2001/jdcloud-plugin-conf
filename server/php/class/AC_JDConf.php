<?php
class JDConf
{
	static $confArr;
	static function export($confArr) {
		self::$confArr = $confArr;
		foreach ($confArr as $name) {
			@include("php/$name.php");
		}
	}
}

class AC0_JDConf extends JDApiBase
{
}

class AC2_JDConf extends AC0_JDConf
{
	function api_get() {
		$name = mparam("name");
		if (!in_array($name, JDConf::$confArr))
			jdRet(E_FORBIDDEN, "conf `$name` is not exported");
		return getConf($name);
	}

	function api_set() {
		$name = mparam("name", "G");
		if (!in_array($name, JDConf::$confArr))
			jdRet(E_FORBIDDEN, "conf `$name` is not exported");
		$f = "php/$name.php";
		file_put_contents($f, "<?php
\$GLOBALS['$name'] = " . var_export($_POST, true) . ";\n");
	}
}

