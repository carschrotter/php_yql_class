<?php

namespace example; {

    /**
     * Helper for the php_yql_class examples 
     *
     * @author carschrotter
     */
    class Helper {
        

        /**
         * 
         * @param string $str (Youtube feed with <table>)
         * @return string
         */
        public static function undoYouTubeMarkupCrimes($str) {
            $cleaner = '';
            $result = preg_match_all('~<tr[^>]*>(.*?)</tr>~s', $str, $matches);
            if($result && count($matches) > 1) {
                foreach($matches[1] as $i => $tr) {
                    $key = $i ? 'footer' : 'content';
                    $cleaner .= '<div class="youtube row_'.$key.'">';
                    preg_match_all ('~<td[^>]*>(.*?)</td>~s', $tr, $matches_td);
                    foreach($matches_td[1] as $j => $td) {
                        $cleaner .= '<div class="youtube col_'.$j.'">'.$td.'</div>';
                    }
                    $cleaner .= '</div>';
                }
                return $cleaner;
            }
            return $str;
        }
    }

}
