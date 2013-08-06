<?php

namespace mnhcc\yql\classes;
{

    /**
     * Description of YQLBase
     *
     * @author carschrotter
     */
    class YQLBase {  
        /**
         * Represents the SQL NULL data type.
         * (integer) 0
         */
        const PARAM_NULL = 0;

        /**
         * Represents the SQL INTEGER data type.
         * (integer) 1
         */
        const PARAM_INT = 1;

        /**
         * Represents the SQL CHAR, VARCHAR, or other string data type.
         * (integer) 2
         */
        const PARAM_STR = 2;
        
        /**
         * Represents a boolean data type.
         * (integer) 5
         */
        const PARAM_BOOL = 5;
        
        const PARAM_JSON = 6;
        
        const PARAM_XML = 7;
        
        protected static function cast($type, $variable) {
            switch (true) {
                case static::PARAM_NULL === $type:
                    if(is_null($variable) || !$variable) {
                        return 'NULL';
                    } else {
                        return $variable;
                    }
                    break;
                case static::PARAM_INT === $type:
                    return (int) $variable;
                    break;
                case static::PARAM_STR === $type:
                    return (string) $variable;
                    break;
                case static::PARAM_BOOL === $type:
                    return (bool) $variable;
                    break;
                case static::PARAM_JSON === $type:
                    if(is_string($variable)){
                        $pcre_regex = '
  /
  (?(DEFINE)
     (?<number>   -? (?= [1-9]|0(?!\d) ) \d+ (\.\d+)? ([eE] [+-]? \d+)? )    
     (?<boolean>   true | false | null )
     (?<string>    " ([^"\\\\]* | \\\\ ["\\\\bfnrt\/] | \\\\ u [0-9a-f]{4} )* " )
     (?<array>     \[  (?:  (?&json)  (?: , (?&json)  )*  )?  \s* \] )
     (?<pair>      \s* (?&string) \s* : (?&json)  )
     (?<object>    \{  (?:  (?&pair)  (?: , (?&pair)  )*  )?  \s* \} )
     (?<json>   \s* (?: (?&number) | (?&boolean) | (?&string) | (?&array) | (?&object) ) \s* )
  )
  \A (?&json) \Z
  /six   
';
                        /**
                         * @toDo implement json check
                         */
                        preg_match_all($pcre_regex, $variable);
                        return $variable;
                    } else {
                        return json_encode($value);
                    }
                        
                    break;
                default:
                    return $variable;
                    break;
            }
        }
    }

}
