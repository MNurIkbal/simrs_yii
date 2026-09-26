<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components;

use yii;
use yii\helpers\ArrayHelper;

class PengadaanComponent {
    /**
     * @todo update multiple data
     * @author Novia Sukmasari Putri <novia.putri@docotel.com>
     * @param
     * $tableName: table name to be updated
     * $dataUpdate: mapping array column_name => array_value; data type of array_value must have same data type as in database
     * $condition: mapping column_name => array_value_id
     * example usage: InfObatAlkesKeluarController(actionEditPemesanan)
     * usage example: actions/PurchaseRequisition/CancelPRAction.php
     * $opCondition: send this params if operand condition is not '=' (example: SetPoExpiredAction.php)
    */
    public static function batchUpdate($tableName, $dataUpdate = [], $conditions = [], $opCondition = [], $isPoExpired = false) {
        try {
            $connection = Yii::$app->db;
            $sqlTableName = "UPDATE ".$tableName." ";
            $sqlSet = "SET ";
            $sqlSelect = "SELECT ";
            $sqlWhere = "WHERE ";
            $modified = [];
            $numItems = count($conditions);
            $user = ArrayHelper::getValue(Yii::$app, 'user.identity.pegawai_id', 999);

            for($j=0; $j<=$numItems; $j++){
                $modified['modified_count'][] = 1;
                $modified['last_modified_date'][] = "";
                $modified['last_modified_by'][] = $user;
            }

            $dataUpdate = array_merge($dataUpdate, $modified);

            $i = 0;
            foreach ($conditions as $key => $value) {
                $sqlSelect .= "unnest(array[".implode(',', $value)."]) as ".$key.", ";
                if(count($opCondition) > 0 && isset($opCondition[$key]['operand'])) {
                    $compare = $opCondition[$key]['operand'];
                } else {
                    $compare = "=";
                }

                if($isPoExpired && isset($opCondition[$key]) && $opCondition[$key]['is_date']) {
                    $sqlWhere = self::conditionsPoExpired($sqlWhere, $compare, $key);
                } else {
                    if(isset($opCondition[$key]) && $opCondition[$key]['is_date']) {
                        $sqlWhere .= $tableName.".".$key."::date ".$compare." data_table.".$key."::date";
                    } else if(isset($opCondition[$key]) && !$opCondition[$key]['is_bool']) {
                        $sqlWhere .= $tableName.".".$key."::boolean ".$compare." data_table.".$key."::boolean";
                    } else {
                        $sqlWhere .= $tableName.".".$key." ".$compare." data_table.".$key;
                    }
                }

                if(++$i !== $numItems) {
                    $sqlWhere .= " AND ";
                }
            }

            $i = 0;
            $numItems = count($dataUpdate);
            foreach ($dataUpdate as $key => $value) {
                if($key == "modified_count"){
                    $sqlSet .= "modified_count = ".$tableName.".modified_count + data_table.modified_count";
                } else {
                    $sqlSet .= $key." = "."data_table.".$key;
                }

                if(is_string($value[0])){
                    if($key == "last_modified_date") {
                        foreach ($value as $index => $val) {
                            $value[$index] = "NOW()";
                        }

                        $sqlSelect .= "unnest(array[".implode(',', $value)."]) as ".$key;
                    } else {
                        $sqlSelect .= "unnest(array['".implode("','", $value)."']) as ".$key;
                    }
                } else if(is_bool($value[0])) {
                    foreach ($value as $index => $bool_value) {
                        $value[$index] = (int)$bool_value."::boolean";
                    }

                    $sqlSelect .= "unnest(array[".implode(',', $value)."]) as ".$key;
                } else if(is_null($value[0])) {
                    foreach ($value as $index => $val) {
                        $value[$index] = "NULL::int";
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

            if($update !== false) {
                return true;
            } else {
                return false;
            }
        } catch (\yii\db\Exception $e) {
            throw $e;
        } catch(\Exception $e){
            throw $e;
        }
    }

    private function conditionsPoExpired($sqlWhere, $compare, $key) {
        $sqlWhere .= "now()::date ".$compare." data_table.".$key."::date";
        return $sqlWhere;
    }

    /**
     * @param
     * $tableName: table name to be updated
     * $conditions: mapping column_name => array_value_id
     * usage example: actions/InfoPurchaseOrder/SaveAction.php
    */
    public static function batchDelete($tableName, $conditions = []) {
        $isActive = false;
        if(array_key_exists('is_active', $conditions) === true) {
            $isActive = true;
        }
        try {
            $connection = Yii::$app->db;
            $sqlTableName = "UPDATE ".$tableName." ";
            $sqlSelect = "SELECT ";
            $sqlWhere = "WHERE ";
            $sqlSet = "SET is_deleted = data_table.is_deleted, deleted_date = data_table.deleted_date, deleted_by = data_table.deleted_by";
            $sqlSet .= ($isActive) ? ", is_active = data_table.is_active" : "";
            $numItems = count(current($conditions));
            $i = 0;
            $sqlIsDeleted = $sqlDeletedDate = $sqlDeletedBy = "unnest(array[";
            $sqlIsActive = ($isActive) ? "unnest(array[" : "";
            for($j = 1; $j <= $numItems; $j++) {
                $sqlIsActive .= ($isActive) ? "0::boolean" : "";
                $sqlIsDeleted .= "1::boolean";
                $sqlDeletedDate .= "NOW()";
                $sqlDeletedBy .= Yii::$app->user->identity->pegawai_id;
                if($j !== $numItems){
                    $sqlIsActive .= ($isActive) ? "," : "";
                    $sqlIsDeleted .= ",";
                    $sqlDeletedDate .= ",";
                    $sqlDeletedBy .= ",";
                }
            }
            $sqlIsActive .= ($isActive) ? "]) as is_active, " : "";
            $sqlIsDeleted .= "]) as is_deleted, ";
            $sqlDeletedDate .= "]) as deleted_date, ";
            $sqlDeletedBy .= "]) as deleted_by";

            if(array_key_exists("is_active", $conditions)) {
                unset($conditions["is_active"]);
            }

            foreach ($conditions as $key => $value) {
                $sqlSelect .= "unnest(array[".implode(',', $value)."]) as ".$key.", ";
                $sqlWhere .= $tableName.".".$key." = data_table.".$key;

                if(++$i !== (count($conditions))) {
                    $sqlWhere .= " AND ";
                }
            }

            $sqlSelect = ($isActive) ? $sqlSelect.$sqlIsActive.$sqlIsDeleted.$sqlDeletedDate.$sqlDeletedBy : $sqlSelect.$sqlIsDeleted.$sqlDeletedDate.$sqlDeletedBy;
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

    /**
     * @todo update multiple data
     * @author Novia Sukmasari Putri <novia.putri@docotel.com>
     * @param
     * $tableName: table name to be updated
     * $dataUpdate: mapping array column_name => array_value; data type of array_value must have same data type as in database
     * $condition: mapping column_name => array_value_id
     * example usage: InfObatAlkesKeluarController(actionEditPemesanan)
     * Added by Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
     * Added_date: 2021-08-05 22:44:00
    */
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
