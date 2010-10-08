<?php

require_once(dirname(__FILE__) . '/../platform/lib/common.php');

uses('rdf');

$src = null;
$out = null;
if(isset($_POST['src']))
{
	$src = $_POST['src'];
}


?>
<!DOCTYPE html>
<html>
<head>
<title>RDF/XML Parser</title>
</head>
<body>
<h1>RDF/XML Parser</h1>
<form method="post" action="">
	<textarea name="src" cols="80" rows="20"><?php e($src); ?></textarea>
	<input type="submit" name="go" value="Go">
</form>
<?php

if(strlen($src))
{
	$doc = simplexml_load_string($src);
	if(is_object($doc))
	{
		$dom = dom_import_simplexml($doc);
		$out = new RDFDocument();
		$out->fromDOM($dom);
	}
}


?>
<p><pre><?php
	ob_start();
if($out)
{
	$result = $out->asXML();
	if(is_array($result))
	{
		$result = implode("\n", $result);
	}
	echo $result . "\n\n";
}
print_r($out);
$buf = ob_get_clean();
e($buf);
?></pre></p>
</body>
</html>