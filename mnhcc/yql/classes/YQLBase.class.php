<?php

namespace mnhcc\testing\yql;
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
        
        const PARAM_JSON = 18;
        
        const PARAM_XML = 19;
        
        protected static function cast($type, $variable) {
            switch (true) {
                case static::PARAM_NULL === $type:
                    if(is_null($variable)) {
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
                default:
                    return $variable;
                    break;
            }
        }
    }

}
