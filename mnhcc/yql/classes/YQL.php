<?php

namespace mnhcc\yql;

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
     * @return \mnhcc\yql\classes\YQLStatement
     */
    public function prepare($statement, $type = self::QUERY_TYPE_DEFAULT) {
        return new YQLStatement($statement, $this->diagnostics, $this->env, $this->_format, $type);
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
