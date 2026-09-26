<?php 

/**
 * @Author: Andri Amirul Sonjaya
 * @Date:   2022-02-23 18:07:00
 */

namespace Integrasi\Components;

use Yii;

class BatchUpdate {

    protected $tableName;
    protected $queryString;
    protected $condition;
    protected $update;

    public function __construct($tableName, Callable $callback)
    {
        self::update($tableName);
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
            $this->queryString[$value][$index] = ' WHEN '.$whereCondition.' THEN '.$fieldUpdate[$value].'';
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
}