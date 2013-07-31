<?php

namespace mnhcc\yql\classes; {

    /**
     * Represents a connection between PHP and the YQL server.
     *
     * @author carschrotter
     */
    class YQL extends YQLBase{
        
        protected $_format = 'json';
        protected $_YQLStatement;
        public $diagnostics;
        public $env;
        
            
        public function __construct($diagnostics = true, $env = 'store://datatables.org/alltableswithkeys', $format = 'json') {
            $this->_YQLStatement = __NAMESPACE__.'\\YQLStatement';
            $this->diagnostics = $diagnostics;
            $this->env = $env;
            $this->_format = $format;
        }

        /**
         * 
         * @param type $statement
         * @return \mnhcc\yql\classes\YQLStatement
         */
        public function prepare($statement) {
            return new YQLStatement($statement, $this->diagnostics, $this->env, $this->_format);
        }

        /**
         * 
         * @param type $statement
         * @return \mnhcc\yql\classes\YQLStatement
         */
        public function query($statement) {
            $value = new YQLStatement($statement, $this->diagnostics, $this->env, $this->_format);
            $value->execute();
            return $value;
        }

    }

}
