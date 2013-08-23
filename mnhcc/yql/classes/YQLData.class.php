<?php

namespace mnhcc\yql\classes;
{

    /**
     * Description of YQLData
     *
     * @author carschrotter
     */
    class YQLData extends YQLBase implements \Iterator, \ArrayAccess, \Serializable, \Countable {

        protected $_data;
        protected $_type;
        protected $_error = null;
        protected $_results = null;
        protected $_caller = null;
        protected $_preferException = false;
        protected $_isSuccess = false;
        protected $_position = 0;
        public $_cache = ['getData' => null, 'oriData' => null];

        public function count() {
            return count($this->getData());
        }

        public function offsetExists($offset) {
            return $this->getData() && key_exists($offset, $this->getData());
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
            throw new \Exception(__CLASS__ . '::' . __METHOD__ . '() not callable');
        }

        /**
         * 
         * @param type $offset
         * @throws \Exception
         */
        public function offsetUnset($offset) {
            throw new \Exception(__CLASS__ . '::' . __METHOD__ . '() not callable');
        }

        public function serialize() {
            return serialize([ 0 => $this->_cache['oriData'], 1 => $this->_type]);
        }

        public function unserialize($serialized) {
            list($data, $type) = unserialize($serialized);
            return $this->__construct($data, $type);
        }

        public function isSuccess() {
            if (\func_num_args() > 0)
                $this->_isSuccess = \func_get_arg(0);
            else
                return $this->_isSuccess;
        }

        /**
         * 
         * @return array|null
         */
        public function getData() {
            if ($this->_results && !$this->_cache['getData']) {
                $this->_cache['getData'] = [];
                switch (true) {
                    case is_array($this->_results): //is 
                        $this->_cache['getData'] = $this->_results;
                        break;
                    
                    case \is_object($this->_results) && \property_exists($this->_results, 'item'):
                        $this->_cache['getData'] = (array) $this->_results->item;
                        break;
                    case \is_object($this->_results) && \property_exists($this->_results, 'results'):
                        if (\is_object($this->_results->results) && \property_exists($this->_results->results, 'item')) {
                            $this->_cache['getData'][] = new static($this->_results->results->item, 'data');
                        } else {
                            foreach (((array) $this->_results->results) as $key => $result) {
                                $this->_cache['getData'][] = new static($result, 'data');
                            }
                        }
                        break;

                    default:
                        break;
                }
            }
            return $this->_cache['getData'];
        }

        public function __construct($data, $type, YQLStatement $caller = null) {
            $this->_cache['oriData'] = $data;
            $this->_type = $type;
            $this->_caller = $caller;
            $this->_processData();
        }

        protected function _processData() {
            switch ($this->_type) {
                case 'json':
                    $this->_data = json_decode($this->_cache['oriData']);
                    break;
                case 'data': default: // is a datasource or a undefined type
                    $this->_data = $this->_cache['oriData'];
                    break;
            }
            if ($this->_data) {
                if (is_array($this->_data)) {
                    $this->_results = $this->_data;
                } elseif (is_object($this->_data)) {
                    if (\property_exists($this->_data, 'query')) { //default setter
                        $this->_results = $this->_data->query->results;
                    }elseif(\property_exists($this->_data, 'item')) { // set on QUERY_TYPE_MULTI
                        $this->_results = is_array($this->_data->item) ? $this->_data->item : [$this->_data->item];
                    }
                    if (\property_exists($this->_data, 'error')) { //on a error
                        $this->_error = $this->_data->error;
                    }
                }
            }
            if ($this->_caller) {
                
            }
            $this->isSuccess((!is_null($this->_data) || is_null($this->_error)));
            return $this->isSuccess();
        }

        public function __toString() {
            return $this->_cache['oriData'];
        }

        function rewind() {
            $this->_position = 0;
        }

        function current() {
            return $this[$this->_position];
        }

        function key() {
            return $this->_position;
        }

        function next() {
            ++$this->_position;
        }

        function valid() {
            return isset($this[$this->_position]);
        }

    }

}
