<?php
namespace app\modules\v1\businessLogic;

use Yii;

use app\modules\v1\models\StokObatAlkes;

class BusinessLogicStockOut
{
	public function expire($data,$tanggal,$ruangan_id)
	{
        $idObatAlkes = [];
        $idObatAlkes = count($data) > 0 ? array_column($data, 'obatalkes_id') : [];
        $inCondition = "(" . implode(",", $idObatAlkes) . ")";
        $obatInStocks = Yii::$app->db->createCommand("
            SELECT
                id_stok,
                obatalkes_id,
                satuankecil_id,
                tglkadaluarsa,
                SUM(dadang.qtystok_in - dadang.qtystok_out) as total_stok,
                nobatch
            FROM
                (
                    SELECT
                        (
                            CASE
                            WHEN stokobatalkesasal_id IS NULL THEN
                                stokobatalkes_id
                            ELSE
                                stokobatalkesasal_id
                            END
                        ) AS id_stok,
                        obatalkes_id,
                        satuankecil_id,
                        tglkadaluarsa,
                        qtystok_in,
                        qtystok_out,
                        nobatch
                    FROM
                        stokobatalkes_t
                    WHERE
                        ruangan_id = {$ruangan_id}
                    AND obatalkes_id IN {$inCondition}
                    AND stokoa_aktif = TRUE
                ) AS dadang
            GROUP BY
                dadang.id_stok,
                tglkadaluarsa,
                obatalkes_id,
                satuankecil_id,
                nobatch
            ORDER BY
                obatalkes_id ASC,
                tglkadaluarsa ASC,
                id_stok ASC
        ")->queryAll();

        /**
        * Find harga netto average from master obat alkes
        * 1 Juli 2020
        */
        $dataMasterObatAlkes = [];
        if(count($idObatAlkes)>0){
            $queryMasterObatAlkes = "SELECT obatalkes_id,hargaratarata,satuankecil_id FROM obatalkes_m WHERE obatalkes_id IN {$inCondition}";
            $rawDataMasterObatAlkes = Yii::$app->db->createCommand($queryMasterObatAlkes)->queryAll();
            if(is_array($rawDataMasterObatAlkes) && count($rawDataMasterObatAlkes)>0){
                $dataMasterObatAlkes = array_column($rawDataMasterObatAlkes, 'hargaratarata','obatalkes_id');
            }

        }
        
        $tmpInsert = [];

        $obatOut = [];
        foreach ($data as $_obat) {
        	$obatOut[$_obat['obatalkes_id']][$_obat['tglkadaluarsa']] = $_obat;
        }

        foreach ($obatInStocks as $ois) {
        	if(isset($obatOut[$ois['obatalkes_id']][$ois['tglkadaluarsa']])){
        		$_obatKeluar = $obatOut[$ois['obatalkes_id']][$ois['tglkadaluarsa']];
        		$tmpInsert[] = [
        			'ruangan_id' => $ruangan_id,
        			'obatalkes_id' => $ois['obatalkes_id'],
					'tglkadaluarsa' => $ois['tglkadaluarsa'],
					'nobatch' => $ois['nobatch'],
					'satuankecil_id' => $_obatKeluar['satuan_id'],
        			'qtystok_out' => $_obatKeluar['qty'],
					'qtystok_in' => 0,
					'tglstok_out' => $tanggal,
					'tglstok_in' => null,
					'persendiscount' => 0,
					'jmldiscount' => 0,
					'persenmargin' => 0,
					'jmlmargin' => 0,
					'persenppn' => 0,
					'jmlppn' => 0,
					'stokoa_aktif' => false,
					'harganetto' => $ois['harganetto'],
					'stokobatalkesasal_id' => $ois['id_stok'],
					'harga_netto_avg' => isset($dataMasterObatAlkes[$ois['obatalkes_id']]) ? $dataMasterObatAlkes[$ois['obatalkes_id']] : 0,
					'stokopnamedetail_id' => isset($_obatKeluar['stokopnamedetail_id']) ? $_obatKeluar['stokopnamedetail_id'] : null,
					'pemusnahanobatdetail_id' => isset($_obatKeluar['pemusnahanobatdetail_id']) ? $_obatKeluar['pemusnahanobatdetail_id'] : null,
        		];
        		unset($obatOut[$ois['obatalkes_id']][$ois['tglkadaluarsa']]);
        	}
        }

        if(count($tmpInsert)>0){
	        StokObatAlkes::batchInsert($tmpInsert, false);
	    }

        return true;
	}
}