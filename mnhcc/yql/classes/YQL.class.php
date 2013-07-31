<?php

namespace mnhcc\testing\yql; {

    /**
     * Description of YQL
     *
     * @author carschrotter
     */
    class YQL extends YQLBase{
        
        protected $_format = 'json';
        public $diagnostics;
        public $env;
            
        public function __construct($diagnostics = true, $env = 'store://datatables.org/alltableswithkeys', $format = 'json') {
            $this->diagnostics = $diagnostics;
            $this->env = $env;
            $this->_format = $format;
        }

        /**
         * 
         * @param type $statement
         * @return \mnhcc\testing\yql\YQLStatement
         */
        public function prepare($statement) {
            return new YQLStatement($statement, $this->diagnostics, $this->env, $this->_format);
        }

        /**
         * 
         * @param type $statement
         * @return \mnhcc\testing\yql\YQLStatement
         */
        public function query($statement) {
            $value = new YQLStatement($statement, $this->diagnostics, $this->env, $this->_format);
            $value->execute();
            return $value;
        }

    }

}
