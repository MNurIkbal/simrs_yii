<?php

namespace app\components;

use Yii;

class BedahComponent
{

    // nyontek dari ApotekComponent, di GudangComponent juga ada
    public static function updateMultiple($tableName, $dataUpdate = [], $conditions = []) {
        try {
            $connection = Yii::$app->db;
            $sqlTableName = "UPDATE ".$tableName." ";
            $sqlSet = "SET ";
            $sqlSelect = "SELECT ";
            $sqlWhere = "WHERE ";

            $tableSchemaColumns = $connection->getTableSchema($tableName)->columns;

            $numItems = count($conditions);
            $i = 0;
            foreach ($conditions as $key => $value) {
                if(in_array(null, $value)){
                    $sqlSelect .= "unnest(array[";
                    foreach ($value as $value_) {
                        if(is_null($value_)){
                            $sqlSelect .= 'null::'.@$tableSchemaColumns[$key]->type.',';
                        }else{
                            $sqlSelect .= $value_.'::'.@$tableSchemaColumns[$key]->type.',';
                        }
                    }
                    if(substr($sqlSelect, -1) == ','){
                        $sqlSelect = rtrim($sqlSelect,",");
                    }
                    $sqlSelect .= "]) as ".$key.", ";
                }else{
                    $sqlSelect .= "unnest(array[".implode(',', $value)."]) as ".$key.", ";
                }
                $sqlWhere .= $tableName.".".$key." = data_table.".$key;

                if(++$i !== $numItems) {
                    $sqlWhere .= " AND ";
                }
            }

            $numItems = count($dataUpdate);
            $i = 0;
            foreach ($dataUpdate as $key => $value) {
                $sqlSet .= $key." = "."data_table.".$key;

                if(in_array(null, $value, true)) {
                    foreach($value as $arrKey => $arrVal) {
                        if(is_null($arrVal)) {
                            $value[$arrKey] = "NULL::int";
                        }
                    }
                }
                
                if(is_string($value[0]) && $value[0] != "NULL::int"){
                    $sqlSelect .= "unnest(array['".implode(',', $value)."']) as ".$key;
                } else if(is_bool($value[0])) {
                    foreach ($value as $index => $bool_value) {
                        $value[$index] = (int)$bool_value."::boolean";
                    }

                    $sqlSelect .= "unnest(array[".implode(',', $value)."]) as ".$key;
                } else if(is_null($value[0])) {
                    foreach ($value as $index => $val) {
                        if(is_null($val)) {
                            $value[$index] = "NULL::int";
                        } else {
                            $value[$index] = $val;
                        }
                    }

                    $sqlSelect .= "unnest(array[".implode(',', $value)."]) as ".$key;
                } else {
                    $sqlSelect .= "unnest(array[".implode(',', $value)."]) as ".$key;
                }

                if(++$i !== $numItems) {
                    $sqlSet .= ", ";
                    $sqlSelect .= ", ";
                }
            }

            $sql = $sqlTableName.$sqlSet." FROM (".$sqlSelect.") as data_table ".$sqlWhere;
            $update = $connection->createCommand($sql)->execute();

            if($update) {
                return true;
            } else {
                return false;
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch(\Exception $e){
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}