<?php

$currentParentNode = "   ";
$filexml = 'input.xml';
$headerElementNames = "";
$headerElementValues = "";
$lineElementNames = "";
$lineElementValues = "";
if (file_exists($filexml)) {
    $xml = simplexml_load_file($filexml);
    $f = fopen('output.csv', 'w');
    createCsv($xml, $f);
    fclose($f);
}
$lines = array();
if (($handle = fopen("output.csv", "r")) !== false) {
    while (($data = fgetcsv($handle, 8192, ",")) !== false) {
        $line = join(",", $data);
        if (isset($lines[$line])) {
            continue;
        }

        $lines[$line] = true;
    }
    fclose($handle);
}

$contents = '';
foreach ($lines as $line => $bool) {
    $contents .= $line . "\r\n";
}


file_put_contents("output.csv", $contents);


function createCsv($xml, $f)
{
    foreach ($xml->children() as $item) {
        if ((count($item->children()) > 0)) {
            $hasChild = true;
        } else {
            $hasChild = false;
        }


        if (!$hasChild) {
            if ($GLOBALS['$currentParentNode'] == "header") {
                $GLOBALS['$headerElementNames'] = $GLOBALS['$headerElementNames'] . ";" . $item->getName();
                $GLOBALS['$headerElementValues'] = $GLOBALS['$headerElementValues'] . ";" . $item;
            } else {
                if ($GLOBALS['$currentParentNode'] == "line") {
                    $GLOBALS['$lineElementNames'] = $GLOBALS['$lineElementNames'] . ";" . $item->getName();
                    $GLOBALS['$lineElementValues'] = $GLOBALS['$lineElementValues'] . ";" . $item;
                }
            }
        } else {
            $GLOBALS['$currentParentNode'] = $item->getName();

            $GLOBALS['$headerElementNames'] = "";
            $GLOBALS['$headerElementValues'] = "";
            $GLOBALS['$lineElementNames'] = "";
            $GLOBALS['$lineElementValues'] = "";

            createCsv($item, $f);
            echo $GLOBALS['$headerElementNames'] . "\n";
            echo $GLOBALS['$headerElementValues'] . "\n";

            echo $GLOBALS['$lineElementNames'] . "\n";
            echo $GLOBALS['$lineElementValues'] . "\n";


            fputcsv($f, explode(';', substr($GLOBALS['$headerElementNames'], 1)), ";");
            fputcsv($f, explode(';', substr($GLOBALS['$headerElementValues'], 1)), ";");
            fputcsv($f, explode(';', substr($GLOBALS['$lineElementNames'], 1)), ";");
            fputcsv($f, explode(';', substr($GLOBALS['$lineElementValues'], 1)), ";");
        }
    }
}



