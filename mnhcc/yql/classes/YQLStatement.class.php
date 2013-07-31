<?php

namespace mnhcc\yql\classes; {

    /**
     * Description of YQL
     *
     * @author carschrotter
     */
    class YQLStatement extends YQLBase implements \Iterator{
        
        /**
         * YQL base url
         *
         * Base URL to which all YQL calls are made
         */
        const yqlUrl = 'http://query.yahooapis.com/v1/public/yql';

        protected $_args = [
            'number' => [],
            'sprintf' => [],
            'keyword' => []
        ];
        
        protected $_query = '';
        protected $_format = 'json';
        protected $_position;
        protected $_data;
        protected $_isDiagnostics;
        
        public function isDiagnostics() {
            if (\func_num_args() > 0)
                $this->_isDiagnostics = \func_get_arg (0);
            else return $this->_isDiagnostics;
        }

        public function __construct($statement, $diagnostics = true, $env = 'store://datatables.org/alltableswithkeys', $format = 'json') {
            $this->_position = 0;
            $this->isDiagnostics($diagnostics);
            $this->env = $env;
            $this->_format = $format;
            $this->addToQery($statement, true);
        }

        /**
         * Perform a YQL query
         * 
         * @access public
         * @param string $query
         * @param array $args. (default: array())
         * @param bool $diagnostics (default: false)
         * @return mixed $response
         * Returns the raw results of the YQL query
         * Should be delegated to by other methods in child classes
         */
        protected function _execute($query, $args = array()) {
            //$this->addToQery($query, true);
            $this->queryUrl = $this->getQueryUrl(self::encodeQuery($query, $args));
            $ch = curl_init($this->queryUrl);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            $this->_data = new YQLData($result, $this->_format, $this);
            return $this->_data->isSuccess();
        }

        public function getQueryUrl($query, $diagnostics = null) {
            if (is_null($diagnostics))
                $diagnostics =  $this->isDiagnostics();
            return self::yqlUrl . '?q=' . urlencode($query) . '&format=' . $this->_format . ( $this->env ? '&env=' . urlencode($this->env) : '') . ( ($diagnostics) ? '&diagnostics=true' : '' );
        }

        public function prepare($statement) {
            $this->addToQery($statement, true);
            return $this;
        }

        /**
         *  Execute the prepared statement. If the prepared statement included parameter markers, you must either:
         * <ul><li>call YQLStatement::bindParam() to bind PHP variables to the parameter markers: 
         * bound variables pass their value as input and receive the output value, if any, of their associated parameter markers</li>
         * <li>or pass an array of input-only parameter values</li></ul>
         * @param array $input_parameters
         * @return bool Returns TRUE on success or FALSE on failure. 
         */
        public function execute(array $input_parameters = []) {
            $this->_workingArgs = array_merge($this->_args, $input_parameters);
            $query = $this->solveParameters($this->_query);
            return $this->_execute($query, $this->_workingArgs['sprintf']);
        }

        protected function solveParameters($statement) {
            $statement = preg_replace_callback("~(.)(\?)~", [$this, 'parmQuestionMark'], $statement);
            foreach ($this->_workingArgs['keyword'] as $key => $value) {
                $variable = self::cast($value['filter'], $value['data']);
                $statement = str_replace($key, $variable, $statement);
            }
            return $statement;
        }

        protected function parmQuestionMark($matches) {
            static $i;
            if(!($i)) $i = 1;
            $replace = $matches[0];
            if(!$matches[1] == '\\') {
                if(key_exists($i, $this->_workingArgs['number'])) {
                    $variable = self::cast($this->_workingArgs['number'][$i]['filter'], $this->_workingArgs['number'][$i]['data']);
                    $replace = $matches[1].$variable;
                } 
            }else {
                $replace = $matches[2];
            }
            $i++;
            
            return $replace;
        }
        /**
         * <b>Binds a value to a parameter</b>
         * Binds a value to a corresponding named or question mark placeholder in the YQL statement that was used to prepare the statement. 
         * @param mixed $parameter int|string Parameter identifier. <p>For a prepared statement using named placeholders, 
         * this will be a parameter name of the form :name. 
         * For a prepared statement using question mark (?) placeholders, 
         * this will be the 1-indexed position of the parameter. <br>
         * Masking question mark with <b>\?<b> <br>
         * Example: <br><code><?php $qery = 'select description from rss(5)
         *  where url = "http://example.com/rss\\?parameter=value'; ?></code></p>
         * @param type $variable Name of the PHP variable to bind to the YQL statement parameter.
         * @param int $data_type Explicit data type for the parameter using the YQL::PARAM_* constants. 
         * @return boolean
         */
        public function bindParam($parameter, &$variable, $data_type = null) {
            if (is_numeric($parameter)) {
                $this->_args['number'][$parameter]['data'] = &$variable;
                $this->_args['number'][$parameter]['filter'] = $data_type;
                return true;
            } elseif (is_string($parameter)) {
                if (strpos($parameter, '%') === 0) {
                    $this->_args['sprintf'][] = &$variable;
                } elseif (strpos($parameter, ':') === 0) {
                    $this->_args['keyword'][$parameter]['data'] = &$variable;
                    $this->_args['keyword'][$parameter]['filter'] = $data_type;
                }
                return true;
            }
            return false;
        }

        /**
         * <b>Binds a value to a parameter</b>
         * Binds a value to a corresponding named or question mark placeholder in the YQL statement that was used to prepare the statement. 
         * @param mixed $parameter int|string Parameter identifier. <p>For a prepared statement using named placeholders, 
         * this will be a parameter name of the form :name. 
         * For a prepared statement using question mark (?) placeholders, 
         * this will be the 1-indexed position of the parameter. <br>
         * Masking question mark with <b>\?<b> <br>
         * Example: <br><code><?php $qery = 'select description from rss(5)
         *  where url = "http://example.com/rss\\?parameter=value'; ?></code></p>
         * @param type $value The value to bind to the parameter. 
         * @param int $data_type Explicit data type for the parameter using the YQL::PARAM_* constants. 
         * @return boolean
         */
        public function bindValue($parameter, $value, $data_type = null) {
            if (is_numeric($parameter)) {
                $this->_args['number'][$parameter]['data'] = $value;
                $this->_args['number'][$parameter]['filter'] = $data_type;
                return true;
            } elseif (is_string($parameter)) {
                if (strpos($parameter, '%') === 0) {
                    $this->_args['sprintf'][] = $value;
                } elseif (strpos($parameter, ':') === 0) {
                    $this->_args['keyword'][$parameter]['data'] = $value;
                    $this->_args['keyword'][$parameter]['filter'] = $data_type;
                }
                return true;
            }
            return false;
        }

        public function addToQery($statement, $override = false) {
            if (strpos($statement, ';', strlen($statement) - 3) < strlen($statement) - 3) {
                $statement .= ';';
            }
            if ($this->_query) {
                $this->_query .= PHP_EOL;
            }
            if($override)
                $this->_query = $statement;
            else
                $this->_query .= $statement;
        }
        
                /**
         * Generates the query with optional arguments
         * 
         * @access public
         * @param string $queryFormat
         * @param array $args
         * @return string
         * Uses sprintf syntax to generate a query.
         * Fills in placeholders using values in the $args array
         */
        function encodeQuery($queryFormat, $args) {
            foreach ($args as &$arg) {
                $arg = addslashes($arg);
            }
            $query = vsprintf($queryFormat, $args);

            return $query;
        }

        function rewind() {
            $this->_position = 0;
        }

        function current() {
            return $this->_data[$this->_position];
        }

        function key() {
            return $this->_position;
        }

        function next() {
            ++$this->_position;
        }

        function valid() {
            return isset($this->_data[$this->_position]);
        }
        
        public function __toString() {
            return (string) $this->_data;
        }
    }

}
