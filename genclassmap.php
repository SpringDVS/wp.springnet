#!/bin/php
<?php
/*
 * This is here because the Zend one has dependencies on Zend framework
 */

$root = __DIR__."/./netlib";
$map = array();
$dirs = array_filter(glob($root.'/*'), 'is_dir');
foreach($dirs as $d) {
	$files = array_filter(glob($d.'/*'), 'is_file');
	foreach($files as $f) {
		$split = explode("./netlib/", $f);
		if(!isset($split[1])) continue;
		$rdir = $split[1];
		if(strlen($rdir) > 5 && substr($rdir,0,7) == "testing" ) continue;
		if(strlen($rdir) > 5 && substr($rdir,0,6) == "vendor" ) continue;

		$class = basename($rdir, '.php');
		$map[$class] = $rdir;
	}
}

$fp = fopen(__DIR__."/autoload.php", 'w');

ob_start();
echo "<?php\n";
echo "\$Gcm__ = array(\n";
	foreach($map as $k => $v) {
		echo "\t'SpringDvs\\$k'=>'$v',\n";
	}
echo ");";

echo "
spl_autoload_register(function (\$class) { 
	global \$Gcm__;
	if(!isset(\$Gcm__[\$class])) return; 
	include __DIR__.'/netlib/' . \$Gcm__[\$class];
});\n";

fwrite($fp, ob_get_clean());
fclose($fp);

