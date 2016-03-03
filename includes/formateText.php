<?php function formateText ($text)
{
	//$text = preg_replace('#(?<!\|)#isU', '', $text);

	
	$text = preg_replace('#(?<!\|)\(b\)([^<>]+)\(/b\)#isU', '<strong style="font-weight: bold;">$1</strong>', $text);
	$text = preg_replace('#(?<!\|)\(i\)([^<>]+)\(/i\)#isU', '<em style="font-style: italic;">$1</em>', $text);
	$text = preg_replace('#(?<!\|)\(u\)([^<>]+)\(/u\)#isU', '<em style="text-decoration: underline;">$1</em>', $text);
	$text = preg_replace('#(?<!\|)\(a (https?://[a-z0-9._\-/&\?^()]+)\)([^<>]+)\(/a\)#isU', '<a href="$1" style="color: #FF8D1C;">$2</a>', $text);
	$text = preg_replace('#(?<!\|)\(img (https?://[a-z0-9._\-/&\?^()]+)\)#isU', '<img src="$1" alt="No description"/>', $text);
	$text = preg_replace('#(?<!\|)\(c ([^<>]+)\)([^<>]+)\(/c\)#isU', '<span style="color: $1">$2</span>', $text);

	return $text;
}
?>
