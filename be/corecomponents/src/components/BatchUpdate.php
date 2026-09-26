<?php 

/**
 * @Author: Andri Amirul Sonjaya
 * @Date:   2022-02-23 18:07:00
 */

namespace Doco\components;

use Yii;

class BatchUpdate {

    protected $tableName;
    protected $queryString;
    protected $condition;
    protected $update;
    protected $tableSchemaColumns;

    public function __construct($tableName, Callable $callback)
    {
        self::update($tableName);
        $connection = Yii::$app->db;
        $this->tableSchemaColumns = $connection->getTableSchema($tableName)->columns;
        $callback($this);
    }

    public function update($tableName)
    {
        return $this->update = 'UPDATE '.$tableName.'';
    }

    public function where($condition = null)
    {   
        $this->condition = '';
        if($condition !== null){
            $this->condition = '
            WHERE '.$condition;
        }
        return $this->condition;
    }

    public function set($fieldUpdate, $whereCondition, $index)
    {
        $keys = array_keys($fieldUpdate);
        foreach($keys as $key => $value){
            switch($this->tableSchemaColumns[$value]->type) {
                case "string":
                case "text":
                    $data = '\''.$fieldUpdate[$value].'\'';
                    break;
                
                case "timestamp":
                    $data = '\''.$fieldUpdate[$value].'\''.'::timestamp';
                    break;
                
                default:
                    $data = $fieldUpdate[$value];
                    break;
            }

            $this->queryString[$value][$index] = ' WHEN '.$whereCondition.' THEN '.$data.'';
        }
        return $this;
    }

    public function execute() 
    {   
        $queryString = '';
        $where = '';
        if($this->queryString !== null){
            $keys = array_keys($this->queryString);
            $index = [];
            $queryString .= ' SET ';
            foreach($keys as $key => $value){
                $index = array_keys($this->queryString[$value]);
                $queryString .= ''.$value.' = 
                ( CASE ';
                foreach($index as $indexes => $val){
                    $queryString .= ''.$this->queryString[$value][$val].'
                    ';
                }
                $queryString .= ' END ),';
            }
            $queryString = rtrim($queryString, ',');
            if($this->condition !== null){
                $update = $this->update;
                $where = $this->condition;
                return Yii::$app->db->createCommand($update.$queryString.$where)->execute();
            }
        }
    }

    public function setInt($data, $key) {
        return isset($data[$key]) ? $data[$key] : 'NULL::int';
    }

    public function setTimestamp($data, $key) {
        return isset($data[$key]) ? $data[$key] : 'NULL::TIMESTAMP';
    }

    public function setText($data, $key) {
        return isset($data[$key]) ? $data[$key] : NULL;
    }

    public function setIntValue($condition, $value) {
        return $condition ? $value : 'NULL::int';
    }

    public function setFloatValue($condition, $value) {
        return $condition ? (float) $value : 'NULL::float';
    }
}