<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-06 09:13:36
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-06 13:31:10
 */

namespace app\components;

use Yii;
use yii\db\QueryBuilder;

use app\modules\v1\models\StokBarang;

class GudangComponent {	

	public function getKonfig()
	{
		$connection = Yii::$app->db;
		$tanggalBerlaku = date('Y-m-d');
		$konfig = $connection->createCommand("
                    SELECT metodeantrian FROM konfiggudang_k
                    WHERE tglberlaku >= '{$tanggalBerlaku}'
                    AND is_active = true
                ")->queryOne();
		return isset($konfig['metodeantrian']) ? $konfig['metodeantrian'] : 'FIFO';
	}

	public function methodeLIFO($data = [],$tanggalPemakaian)
    {
        // Skip ketika tidak di temukan datanya
        if (!count($data)) return true;
        $request = Yii::$app->request;
        $ruangan_id = $request->post('ruangan_id');
        $listIdDetail = $listIdBarang = [];
        foreach ($data as $val) {
            $idBarang = $val['barang_id'];
            $listIdDetail[$idBarang] = $val;
            $listIdBarang[] = $idBarang;
        }
        $inCondition = "(" . implode(",", $listIdBarang) . ")";

        $query = Yii::$app->db->createCommand("
            SELECT barang_id,id_stok,
            SUM(sub_query.qtystok_in - sub_query.qtystok_out) as total_stok ,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in
            FROM (
                SELECT (CASE WHEN t.stokbarangasal_id IS NULL THEN t.stokbarang_id ELSE t.stokbarangasal_id END) as id_stok,
                t.barang_id,
                t.qtystok_in,
                (CASE WHEN t.tglstok_in IS NULL THEN child.tglstok_in ELSE t.tglstok_in END) as tglstok_in,
                t.qtystok_out, t.nobatch,t.harganetto,t.persendiscount,
                t.jmldiscount,t.persenppn,t.persenpph,t.persenmargin,t.jmlmargin  
                                 FROM stokbarang_t t
                 LEFT JOIN stokbarang_t child ON t.stokbarangasal_id = child.stokbarang_id
                WHERE t.barang_id IN {$inCondition} AND t.ruangan_id = {$ruangan_id}
                AND t.stokbarang_aktif = true
            ) as sub_query
            GROUP BY sub_query.id_stok ,barang_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in,tglstok_in
            ORDER BY sub_query.tglstok_in DESC

        ")->queryAll();

        $tmpInsert = $listOfOutStock = [];
        // Kuncinya jumlah_pakai selalu berkurang
        foreach ($query as $detail) {
            $idBarang = $detail['barang_id'];
            if (isset($listIdDetail[$idBarang])) {
                // ini stok permintaan yang di inputkan
                // contoh permintaan 250 sedangkan stok detail nya 100
                $stokItem = $listIdDetail[$idBarang]['jumlah_pakai'];
                // Lewati ketika stok sudah 0 
                if ($stokItem == 0) continue;
                $totalStok = $detail['total_stok'];
                if ($stokItem > $totalStok) {
                    $listOfOutStock[] = $detail['id_stok'];
                    // ini akan membuat row baru dengan catatan stok harus di update false
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarang,
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => $detail['harganetto'],
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $totalStok,
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idBarang]['satuankecil_id'],
                        'pemakaianbarangdetail_id' => $listIdDetail[$idBarang]['pemakaianbarangdetail_id'],
                        'stokbarang_aktif' => false,
                        'is_active' => true
                    ];
                    $listIdDetail[$idBarang]['jumlah_pakai'] = $stokItem - $totalStok;
                } else {
                    if ($listIdDetail[$idBarang]['jumlah_pakai'] == $totalStok) {
                        $listOfOutStock[] = $detail['id_stok'];
                    }
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarang,
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => $detail['harganetto'],
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $listIdDetail[$idBarang]['jumlah_pakai'],
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idBarang]['satuankecil_id'],
                        'pemakaianbarangdetail_id' => $listIdDetail[$idBarang]['pemakaianbarangdetail_id'],
                        'stokbarang_aktif' => true,
                        'is_active' => true
                    ];
                    $listIdDetail[$idBarang]['jumlah_pakai'] = 0;
                }
            }
        }

        // Insert
        if ($tmpInsert) {
            StokBarang::batchInsert($tmpInsert);
        }

        // Update Stok jadiin false
        if ($listOfOutStock) {
            $inCondition = "(" . implode(",", $listOfOutStock) . ")";
            Yii::$app->db->createCommand("
                UPDATE stokbarang_t SET stokbarang_aktif = false, is_active = false 
                WHERE (stokbarangasal_id IN {$inCondition} OR stokbarang_id IN {$inCondition}) 
                AND stokbarang_aktif = true
            ")->execute();
        }
        return true;

    }

    public function methodeFIFO($data = [],$tanggalPemakaian, $ruangan_id = null)
    {
        // Skip ketika tidak di temukan datanya
        if (!count($data)) return true;
        $request = Yii::$app->request;
        $ruangan_id = !empty($request->post('ruangan_id')) ? $request->post('ruangan_id') : $ruangan_id ;
        $listIdDetail = $listIdBarang = [];
        
        foreach ($data as $val) {
            $idBarang = $val['barang_id'];
            $listIdDetail[$idBarang] = $val;
            $listIdBarang[] = $idBarang;
        }
        $inCondition = "(" . implode(",", $listIdBarang) . ")";
        
        $query = Yii::$app->db->createCommand("
            SELECT barang_id,id_stok,
            SUM(sub_query.qtystok_in - sub_query.qtystok_out) as total_stok ,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in,tglkadaluarsa
            FROM (
                SELECT (CASE WHEN t.stokbarangasal_id IS NULL THEN t.stokbarang_id ELSE t.stokbarangasal_id END) as id_stok,
                t.barang_id,
                t.tglkadaluarsa,
                t.qtystok_in,
                (CASE WHEN t.tglstok_in IS NULL THEN child.tglstok_in ELSE t.tglstok_in END) as tglstok_in,
                t.qtystok_out, t.nobatch,t.harganetto,t.persendiscount,
                t.jmldiscount,t.persenppn,t.persenpph,t.persenmargin,t.jmlmargin  
                FROM stokbarang_t t
                LEFT JOIN stokbarang_t child ON t.stokbarangasal_id = child.stokbarang_id
                WHERE t.barang_id IN {$inCondition} AND t.ruangan_id = {$ruangan_id}
                AND t.stokbarang_aktif = true
            ) as sub_query
            GROUP BY sub_query.id_stok ,barang_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in,tglstok_in,tglkadaluarsa
            ORDER BY sub_query.tglstok_in ASC
        ")->queryAll();        
        $tmpInsert = $listOfOutStock = [];
        // Kuncinya jumlah_pakai selalu berkurang
        foreach ($query as $detail) {
            $idBarang = $detail['barang_id'];
            if (isset($listIdDetail[$idBarang])) {
                // ini stok permintaan yang di inputkan
                // contoh permintaan 250 sedangkan stok detail nya 100
                $stokItem = $listIdDetail[$idBarang]['jumlah_pakai'];
                // Lewati ketika stok sudah 0 
                if ($stokItem == 0) continue;
                $totalStok = $detail['total_stok'];
                if ($stokItem > $totalStok) {
                    $listOfOutStock[] = $detail['id_stok'];
                    // ini akan membuat row baru dengan catatan stok harus di update false
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarang,
                        'tglkadaluarsa' => $detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => $detail['harganetto'],
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'tglstok_in' => $detail['tglstok_in'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $totalStok,
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idBarang]['satuankecil_id'],
                        'pemakaianbarangdetail_id' => $listIdDetail[$idBarang]['pemakaianbarangdetail_id'],
                        'stokbarang_aktif' => false,                        
                        'is_active' => false
                    ];
                    $listIdDetail[$idBarang]['jumlah_pakai'] = $stokItem - $totalStok;
                } else {
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarang,
                        'tglkadaluarsa' => $detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => $detail['harganetto'],
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'tglstok_in' => $detail['tglstok_in'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $listIdDetail[$idBarang]['jumlah_pakai'],
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idBarang]['satuankecil_id'],
                        'pemakaianbarangdetail_id' => $listIdDetail[$idBarang]['pemakaianbarangdetail_id'],
                        'stokbarang_aktif' => true,                        
                        'is_active' => true
                    ];
                    $listIdDetail[$idBarang]['jumlah_pakai'] = 0;
                }
            }
        }
        
        // Insert
        if ($tmpInsert) {
            StokBarang::batchInsert($tmpInsert);
        }

        // Update Stok jadiin false
        if ($listOfOutStock) {
            $inCondition = "(" . implode(",", $listOfOutStock) . ")";
            Yii::$app->db->createCommand("
                UPDATE stokbarang_t SET stokbarang_aktif = false, is_active = false
                WHERE (stokbarangasal_id IN {$inCondition} OR stokbarang_id IN {$inCondition}) 
                AND stokbarang_aktif = true
            ")->execute();
        }
        return true;
    }

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
                    $sqlSelect .= "unnest(array['".implode("','", $value)."']) as ".$key;
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
            throw $e;
        } catch(\Exception $e){
            Yii::$app->response->statusCode = 500;
            throw $e;
        }
    }

}

