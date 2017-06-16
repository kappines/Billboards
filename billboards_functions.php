<?php

/**
 * Returns the largest font size in which to print a text on a billboard of given dimensions.
 * 
 * @param type $width : width of billboard
 * @param type $height : height of the billboard
 * @param type $text : text to print on the billboard
 * @return int : the largest font size in which to print the text on the billboard
 */
function findLargestFontSize($width, $height, $text) {
    $font_size = 0;
    
    // verifying that the attributes match the given constraints
    if ($width > 0 && $width <= 1000 && $height > 0 && $height <= 1000 
            && preg_match("#^[[:alnum:]]+( [[:alnum:]]+)*$#", $text) && strlen($text) <= 1000) {
        $words = explode(" ", $text);
        $words_length = array_map('strlen', $words);
        $max_word_length = max($words_length);
        
        // if the longest word does not fit in the width of the billboard even with a font size of 1, then no solution exists
        if ($max_word_length <= $width) {
            
            /* the absolute theoretical maximum of lines for this text is reached
             * either when there are only one-word lines,
             * or when all lines are printed with a font size of 1
             */
            $nb_max_lines = min($height, count($words_length));
            for ($nb_lines = 1; $nb_lines <= $nb_max_lines; $nb_lines++) {
                $lines = makeShortLines($words_length, $nb_lines, $width + 1);
                if (!empty($lines)) {
                    $lines_length = array();
                    foreach ($lines as $line) {
                        $lines_length[] = array_sum($line) + (count($line) - 1);
                    }
                    $max_line_length = max($lines_length);

                    /*
                     * the largest font_size for the current number of lines is either 
                     * the largest width possible for the letters of the longest line, given the billboard width,
                     * or the largest height possible for the lines, given the billboard height.
                     */
                    $font_size = max($font_size, min(intdiv($width, $max_line_length), intdiv($height, $nb_lines)));
                }
                
                /*
                 * Adding a new line necessarily decreases the theoretical maximum height of each line.
                 * If the current font size is already larger than this value, we have found the maximum font size.
                 */
                if ($nb_lines < $nb_max_lines && intdiv($height, $nb_lines + 1) < $font_size) {
                    break;
                }
            }
        }
    }
    
    return $font_size;
}


/**
 * Divides a text in the desired number of lines
 * so that the lines are shorter than the set limit and are the shortest possible.
 * Returns the lines as an array of integer arrays.
 * Returns an empty array if dividing the text in the required number of lines is impossible.
 * 
 * @param type $words_length : array of integers which represent the length of the words
 * @param type $nb_lines : number of desired lines
 * @param type $line_length_upper_limit : length each line must be shorter than
 */
function makeShortLines($words_length, $nb_lines, $line_length_upper_limit) {
    $lines = array();
    
    /*  prendre itérativement dans les groupes jusqu'à ce qu'ajouter au groupe fasse sum > max 
     * ou jusqu'à ce qu'il reste autant d'élément à ajouter que de lignes restantes
     */
    do {
        $optimal_lines = $lines;
        $optimal_line_length = $line_length_upper_limit;
        $lines = array_fill(0, $nb_lines, array());

        $line_index = 0;
        foreach ($words_length as $word_index => $word_length) {
            $current_line_length = array_sum($lines[$line_index]) + count($lines[$line_index]) - 1;
            $new_line_length = ($current_line_length > 0) ? $current_line_length + $word_length + 1 : $word_length;
            
            /* do not add the word in the line if the resulting length is higher than the maximum 
             * or if it does not leave enough words to fill the remaining lines
             */
            if (($new_line_length < $optimal_line_length) 
                    && (($nb_lines - $line_index) <= (count($words_length) - $word_index))) {
                $lines[$line_index][] = $word_length;
            } elseif (++$line_index < $nb_lines) {
                $lines[$line_index][] = $word_length;
            } else {
                break 2;
            }
        }
        $lines_length = array();
        foreach ($lines as $line) {
            $lines_length[] = array_sum($line) + (count($line) - 1);
        }
        $line_length_upper_limit = max($lines_length);
    } while ($line_length_upper_limit < $optimal_line_length);

    return $optimal_lines;
    
}

?>

