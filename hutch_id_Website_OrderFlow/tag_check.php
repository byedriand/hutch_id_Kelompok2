<?php
$html = file_get_contents('resources/views/admin/dashboard.blade.php');
preg_match_all('@<(/?)([a-zA-Z0-9]+)(\\s|>)@', $html, $m, PREG_SET_ORDER);
$stack = array();
$errors = array();
$void = array('area','base','br','col','embed','hr','img','input','link','meta','param','source','track','wbr');
foreach ($m as $tag) {
    $close = ($tag[1] === '/');
    $name = strtolower($tag[2]);
    if (!$close) {
        if (!in_array($name, $void)) {
            $stack[] = $name;
        }
    } else {
        if ($stack && end($stack) === $name) {
            array_pop($stack);
        } else {
            $errors[] = $name;
        }
    }
}
echo 'remaining=' . json_encode($stack) . "\n";
echo 'errors=' . json_encode($errors) . "\n";
