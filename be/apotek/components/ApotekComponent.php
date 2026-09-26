<?php
/**
 * @author Randy Vianda Putra
 * @todo Apotek Component
 * @copyright 23 January 2018 aweutist
 */

namespace app\components;

use yii\db\QueryBuilder;
use app\components\QueryApotek;
use yii;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\InfoResepturDetailView;
use app\modules\v1\models\StokObatPasien;
class ApotekComponent
{
    /**
     * @todo Inserting data
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param string table, array column, array params
     */
    public static function insert($table, array $column)
    {
        try {
            $connection = Yii::$app->db;
            return $connection->createCommand()
                ->insert($table, $column)
                ->execute();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo Inserting multiple data
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param string table, array objArr
     */
    public static function insertMultiple($table, array $objArr)
    {
        try {
            $connection = Yii::$app->db;
            $key_insert = [];
            foreach ($objArr as $key => $value) {
                $key_data = [];
                foreach ($value as $k => $v) {
                    $key_data[] = $k;
                }
                $key_insert[] = $key_data;
            }
            if (!empty($key_data)) {
                return $connection->createCommand()
                    ->batchInsert($table, $key_data, $objArr)
                    ->execute();
            }
            return false;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo select one data from table
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param string table, array column, array conditions, string order by
     */
    public static function selectOne($table, array $column, array $conditions = [], $order = null)
    {
        try {
            $connection = Yii::$app->db;
            $columnSelect = implode(', ', $column);
            if ($conditions) {
                $bind_values = [];
                $bind = [];
                $temp_conditions = [];
                foreach ($conditions as $key => $value) {
                    $bind_values[':'.$key] = $value;
                    $temp_conditions[] = $key ." = :". $key;
                }
                $join_conditions = implode(' AND ', $temp_conditions);
                if ($order) {
                    $sql = "SELECT ". $columnSelect ." FROM ". $table ." WHERE ". $join_conditions . $order;
                } else {
                    $sql = "SELECT ". $columnSelect ." FROM ". $table ." WHERE ". $join_conditions;
                }
                $data = $connection->createCommand($sql)
                            ->bindValues($bind_values)
                            ->queryOne();
            } else {
                $sql = "SELECT ". $columnSelect ." FROM " .$table;
                $data = $connection->createCommand($sql)->queryOne();
            }

            return $data;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo select all data from table
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param string table, array column, array conditions
     */
    public static function selectAll($table, array $column, array $conditions = [])
    {
        try {
            $connection = Yii::$app->db;
            $columnSelect = implode(', ', $column);
            if ($conditions) {
                $bind_values = [];
                $bind = [];
                $temp_conditions = [];
                foreach ($conditions as $key => $value) {
                    $bind_values[':' . $key] = $value;
                    $temp_conditions[] = $key . " = :" . $key;
                }
                $join_conditions = implode(' AND ', $temp_conditions);
                $sql = "SELECT " . $columnSelect . " FROM " . $table . " WHERE " . $join_conditions;
                $data = $connection->createCommand($sql)
                    ->bindValues($bind_values)
                    ->queryAll();
            } else {
                $sql = "SELECT " . $columnSelect . " FROM " . $table;
                $data = $connection->createCommand($sql)->queryAll();
            }

            return $data;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo updating data
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param string table, array objArr, array conditions
     */
    public static function update($table, array $objArr, array $conditions = [])
    {
        try {
            $value_data = [];
            $bind_values = [];
            foreach ($conditions as $key => $value) {
                $bind_values[] = $key . " = :" . $key;
                if ($value == NULL) {
                    $value_data[':' . $key] . ' is null';
                } else {
                    $value_data[':' . $key] = $value;

                }
            }
            $join_bind = implode(' AND ', $bind_values);
            $connection = Yii::$app->db;
            $a = $connection->createCommand()
                ->update($table, $objArr, $join_bind, $value_data)
                ->getRawSql();
            var_dump($value_data);exit;
            return $connection->createCommand()
                ->update($table, $objArr, $join_bind, $value_data)
                ->getRawSql();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo generate no resep
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public static function getNoResep()
    {

        $columnSelect = ['last_generate', 'last_number', 'prefix'];
        $conditions = [
            'penomoran_id' => 4 // transaksi resep
        ];
        $getNoResep = self::selectOne('penomoran_k', $columnSelect, $conditions);

        return $getNoResep;
    }

    /**
     * @todo get stok per ruangan
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param int ruangan_id, obatalkes_id
     */
    public static function getStokPerRuanganByIdBarang($ruangan_id, $obatalkes_id)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT
                    obatalkes_id,
                    penerimaanobatdetail_id,
                    SUM (qtystok_in) OVER (
                        PARTITION BY penerimaanobatdetail_id
                    ) AS qtystok_in,
                    SUM (qtystok_out) OVER (
                        PARTITION BY penerimaanobatdetail_id
                    ) AS qtystok_out,
                    stokobatalkes_id,
                    tglkadaluarsa,
                    obatalkespasien_id,
                    harganetto
                FROM
                    stokobatalkes_t
                WHERE
                    stokoa_aktif = TRUE
                AND ruangan_id = {$ruangan_id}
                AND obatalkes_id = {$obatalkes_id}
        ";

        $data = $connection->createCommand($sql)->queryAll();
        $data_stok = [];
        if (isset($data)) {
            foreach ($data as $key => $value) {
                if (empty($value['obatalkespasien_id'])) {
                    $data_stok[$value['penerimaanobatdetail_id']] = [
                        'stok' => ($value['qtystok_in'] - $value['qtystok_out']),
                        'kadaluarsa' => $value['tglkadaluarsa'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'stokobatalkes_id' => $value['stokobatalkes_id'],
                        'penerimaanobatdetail_id' => $value['penerimaanobatdetail_id']
                    ];
                }
            }
        }
        return $data_stok;
    }

    /**
     * @todo get stok per ruangan
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param int ruangan_id
     * @param string array data post
     */
    public static function updateStok($ruangan_id, array $data)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $user_login = Yii::$app->user->identity->pegawai_id;
            $obatalkes_id = $data['obatalkes_id'];
            $find_stok = self::getStokPerRuanganByIdBarang($ruangan_id, $obatalkes_id);
            $stok_request = $data['qty_oa'];
            $attributes = [];

            if (isset($find_stok)) {
                foreach ($find_stok as $key => $value) {
                    if (($value['stok'] - $stok_request) < 0) {
                        $stok_request = $stok_request - $value['stok'];
                        $attributes[] = [
                            'obatalkes_id' => $value['obatalkes_id'],
                            'ruangan_id' => $ruangan_id,
                            // 'penerimaanobatdetail_id' => $value['penerimaanobatdetail_id'],
                            'obatalkespasien_id' => $data['obatalkespasien_id'],
                            'tglkadaluarsa' => $value['kadaluarsa'],
                            'tglstok_out' => $now,
                            'qtystok_out' => $value['stok'],
                            'stokoa_aktif' => false,
                            'stokobatalkesasal_id' => $value['stokobatalkes_id'],
                            'created_by' => $user_login
                        ];
                    } else {
                        $sum_stok = ($value['stok'] - $stok_request);
                        $stok_aktif = ($sum_stok == 0) ? false : true;
                        $attributes[] = [
                            'obatalkes_id' => $value['obatalkes_id'],
                            'ruangan_id' => $ruangan_id,
                            // 'penerimaanobatdetail_id' => $value['penerimaanobatdetail_id'],
                            'obatalkespasien_id' => $data['obatalkespasien_id'],
                            'tglkadaluarsa' => $value['kadaluarsa'],
                            'tglstok_out' => $now,
                            'qtystok_out' => $stok_request,
                            'stokoa_aktif' => $stok_aktif,
                            'stokobatalkesasal_id' => $value['stokobatalkes_id'],
                            'created_by' => $user_login
                        ];
                        break;
                    }
                }
                self::insertMultiple('stokobatalkes_t', $attributes);
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo get detail retur per penjualan id
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param int penjualan_id
     */
    public static function getDetailReturByPenjualan($penjualan_id)
    {
        try {

            $penjualan = QueryApotek::queryGetPenjualan($penjualan_id);
            $obatalkes_id = $obatalkespasien_id = [];
            $ruangan_id = '';
            foreach ($penjualan as $key => $value) {
                $obatalkes_id[] = $value['obatalkes_id'];
                $obatalkespasien_id[] = $value['obatalkespasien_id'];
                $ruangan_id = $value['ruangan_id'];
            }
            $array = [];
            if ($obatalkes_id && $obatalkespasien_id && $ruangan_id) {
                $data = QueryApotek::queryDetailReturByPenjualan($obatalkes_id, $ruangan_id, $obatalkespasien_id);
                foreach ($data as $key => $value) {
                    $array[$value['obatalkespasien_id']] = [
                        'noresep' => $value['noresep'],
                        'tglpenjualan' => $value['tglpenjualan'],
                        'tglkadaluarsa' => date('d M Y', strtotime($value['tglkadaluarsa'])),
                        'jenispenjualan' => $value['jenispenjualan'],
                        'totalhargajual' => $value['totalhargajual'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'obatalkes_namalain' => $value['obatalkes_nama'],
                        'qty_oa' => $value['qty_oa'],
                        'signa_oa' => $value['signa_oa'],
                        'hargajual_oa' => $value['hargajual_oa'],
                        'harganetto_oa' => $value['harganetto_oa'],
                        'hargasatuan_oa' => $value['hargasatuan_oa'],
                        'obatalkespasien_id' => $value['obatalkespasien_id'],
                        'rke' => $value['rke'],
                        'satuankecil_id' => $value['satuankecil_id'],
                        'penjualanresep_id' => $value['penjualanresep_id'],
                        'stokobatalkesasal_id' => $value['stokobatalkesasal_id'],
                        'nobatch' => $value['nobatch'],
                        'harganetto' => $value['harganetto'],
                        'persendiscount' => $value['persendiscount'],
                        'jmldiscount' => $value['jmldiscount'],
                        'persenppn' => $value['persenppn'],
                        'jmlppn' => $value['jmlppn'],
                        'persenmargin' => $value['persenmargin'],
                        'jmlmargin' => $value['jmlmargin'],
                        'qty_retur' => 0,
                    ];
                }
            }

            return $array;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo update stok retur apotek
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param string array data post
     */
    public static function updateStokRetur(array $data)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $user_login = Yii::$app->user->identity->pegawai_id;
            $map_array = $attributes = $dataTransaction = [];
            if (isset($data['list_retur'])) {
                foreach ($data['list_retur'] as $key => $value) {
                    $attributes[] = [
                        'ruangan_id' => $data['ruangan_id'],
                        'obatalkespasien_id' => $value['obatalkespasien_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'tglkadaluarsa' => $value['tglkadaluarsa'],
                        'nobatch' => !empty($value['nobatch']) ? $value['nobatch'] : null,
                        'tglstok_in' => $now,
                        'qtystok_in' => $value['qty_retur'],
                        'stokoa_aktif' => true,
                        'stokobatalkesasal_id' => $value['stokobatalkesasal_id'],
                        'created_by' => $user_login
                    ];
                }
            }

            self::insertMultiple('stokobatalkes_t', $attributes);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    public static function checkStok(Array $oa = []){
        if(count($oa) > 0){
            $connection = Yii::$app->db;
            $ruangan_id = '';
            $dataObat = [];
            foreach ($oa as $key => $value) {
                if($ruangan_id == ''){
                    $ruangan_id = $value['ruangan_id'];
                }
                $newVal[] = $value['obatalkes_id'];
                $dataObat[$value['obatalkes_id']] = $value['qty_oa'];
            }
            $_POST['ruangan_id'] = $ruangan_id;
            $str = implode(',', $newVal);
            $sql = "SELECT obatalkes_id, qty_tersedia FROM stokobatalkes_r WHERE obatalkes_id IN({$str}) and ruangan_id = '{$ruangan_id}'";
            $stok = $connection->createCommand($sql)->queryAll();
            foreach ($stok as $key => $value) {
                if($value['qty_tersedia'] < $dataObat[$value['obatalkes_id']] || $value['qty_tersedia'] < 1){
                    return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Obat tidak tersedia',
                            'status' => 422
                        ];
                    // throw new \Exception("Obat tidak tersedia");
                }
            }
            return true;
        }
    }

    /**
     * @todo insert to stokobatpasien_r (ranap, igd)
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param string array data list_resepturdetail_id
     */
    public static function insertStokObatPasien(array $list_resepturdetail_id)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $user_login = Yii::$app->user->identity->pegawai_id;
            $detail_reseptur = InfoResepturDetailView::find()->where([
                'resepturdetail_id' => $list_resepturdetail_id
            ]);
            $data_reseptur = $detail_reseptur->all();
            $dataInsert = [];
            if (!empty($data_reseptur)) {
                foreach ($data_reseptur as $key => $value) {
                    $dataInsert[] = [
                        'obatalkespasien_id' => $value['obatalkespasien_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'nama_obat' => $value['obatalkes_nama'],
                        'satuan_kecil' => $value['satuan_kecil'],
                        'stok_layak' => $value['qty_reseptur'],
                        'stok_sisa' => $value['qty_reseptur'],
                        'stok_retur' => $value['qty_reseptur'],
                        'stok_retur_sisa' => $value['qty_reseptur'],
                        'stok_dipakai' => 0,
                        'stok_pending' => 0,
                        'is_retur' => false,
                    ];
                }

                StokObatPasien::batchInsert($dataInsert);
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
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
            throw $e;
        } catch(\Exception $e){
            throw $e;
        }
    }
}
