<?php

/**
 * Created by Inès Kapp on 2017/06/13
 * Solution for the Billboards problem
 * https://gist.github.com/matts2cant/3a80edcbe828ab6843fb
 */

include realpath('billboards_functions.php');

$file = isset($argv[1]) ? $argv[1] : "input";

if (is_readable($file) && ($handle = fopen($file, "r")) !== false) {
    $nb_test_cases = fgets($handle);
    if ($nb_test_cases !== false && $nb_test_cases > 0 && $nb_test_cases <= 20) {
        for ($case_nb = 1; $case_nb <= $nb_test_cases; $case_nb++) {
            if (($test_case = fgets($handle)) !== false 
                    && preg_match("#^(\d+) (\d+) ([[:alnum:]]+(?: [[:alnum:]]+)*)\r?$#", $test_case, $matches)) {
                $width = $matches[1];
                $height = $matches[2];
                $text = $matches[3];
                $font_size = findLargestFontSize($width, $height, $text);
                echo "Case #$case_nb: $font_size\n";
            }
            if (feof($handle)) {
                break;
            }
        }
    }
    fclose($handle);
}

?>

