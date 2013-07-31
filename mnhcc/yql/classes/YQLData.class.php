<?php

namespace mnhcc\testing\yql; {

    /**
     * Description of YQLData
     *
     * @author carschrotter
     */
    class YQLData extends YQLBase implements \IteratorAggregate, \ArrayAccess, \Serializable, \Countable {

        protected $_data;
        protected $_oriData;
        protected $_type;
        protected $_error = null;
        protected $_results = null;
        protected $_caller = null;
        protected $_preferException = false;
        protected $_isSuccess = false;

        public function count() {
            return count($this->getData());
        }

        public function getIterator() {
            return (count($this->getData()) > 0);
        }

        public function offsetExists($offset) {
            return key_exists($offset, $this->getData());
        }

        public function offsetGet($offset) {
            return $this->getData()[$offset];
        }

        /**
         * 
         * @param type $offset
         * @param type $value
         * @throws \Exception
         */
        public function offsetSet($offset, $value) {
            throw new \Exception(__CLASS__.'::'.__METHOD__.'() not callable');
        }

        /**
         * 
         * @param type $offset
         * @throws \Exception
         */
        public function offsetUnset($offset) {
            throw new \Exception(__CLASS__.'::'.__METHOD__.'() not callable');
        }

        public function serialize() {
            ;
        }

        public function unserialize($serialized) {
            ;
        }
        
        public function isSuccess() {
            if (\func_num_args() > 0)
                $this->_isSuccess = \func_get_arg (0);
            else return $this->_isSuccess;
        }

        /**
         * 
         * @return array|null
         */
        protected function getData() {
            static $results;
            if($this->_results && !$results) {
                $results = [];
                switch (true) {
                    case \property_exists($this->_results, 'item'):
                        $results = (array) $this->_results->item;
                        break;
                    
                    case \property_exists($this->_results, 'results'):
                        foreach ((array) $this->_results->results as $number => $result) {
                            $results[$number] = new static($result, 'data');
                        }
                        break;
                        
                    default:
                        break;
                }
            } 
            return $results;
        }

        public function __construct($data, $type, YQLStatement $caller = null) {
            $this->_oriData = $data;
            $this->_type = $type;
            $this->_caller = $caller;
            $this->_processData();
            
        }

        protected function _processData() {
            switch ($this->_type) {
                case 'json':
                    $this->_data = json_decode($this->_oriData);
                    break;
                case 'data':
                    $this->_data = $this->_oriData;
                default:
                    $this->_data = null;
                    break;
            }
            if( $this->_data) {
                var_dump(\property_exists($this->_data, 'query'));
                if(\property_exists($this->_data, 'query')) {
                    $this->_results = $this->_data->query->results;
                }
                if(\property_exists($this->_data, 'error')) {
                    $this->_error = $this->_data->error;
                }
            }
            if($this->_caller) {
                
            }
            $this->isSuccess( ( !is_null($this->_data) || is_null($this->_error) ) );
            return $this->isSuccess(); 
        }

        public function __toString() {
            return $this->_oriData;
        }

    }

}
