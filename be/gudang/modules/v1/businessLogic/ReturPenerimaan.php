<?php
namespace app\modules\v1\businessLogic;

use Yii;
use app\modules\v1\models\StokBarang;

class ReturPenerimaan
{
	public static function manual($ruangan_id, $data = [])
	{
		try{

			$currentDate = date('Y-m-d H:i:s');
			if (!count($data)){
				throw new \Exception("Data Not Found", 1);
			}
			$idBarang = $penerimaanManuals = [];
			foreach ($data as $val) {
	            $_idbarang = $val['barang_id'];
	            $listIdDetail[$_idbarang][] = $val;
	            $barangs[] = $_idbarang;
	            $arr_penerimaan[] = $val['penerimaansuppdetail_id'];
	        }

			if(count($barangs)<1){
				throw new \Exception("Id Barang Not Found", 1);	
			}
			$listBarang = "(" . implode(",", $barangs) . ")";
			$listPenerimaanManual = "(" . implode(",", $arr_penerimaan) . ")";
			$stokBarang = Yii::$app->db->createCommand("
	            SELECT 
					st.stokbarang_id ,
					st.barang_id ,
					st.ruangan_id ,
					st.qtystok_in as qty_masuk,
					coalesce(child.qty,0) as qty_keluar,
					st.qtystok_in - coalesce(child.qty,0) as qty_sisa,
					st.harganetto ,
					st.tglkadaluarsa ,
					st.nobatch ,
					st.persendiscount ,
					st.jmldiscount ,
					st.persenpph ,
					st.persenppn,
					st.persenmargin ,
					st.jmlmargin
				FROM stokbarang_t st
				LEFT JOIN (
					select 
					st2.stokbarangasal_id, 
					sum(st2.qtystok_out) qty
					from stokbarang_t st2 
					where st2.tglstok_out is not null
					group by st2.stokbarangasal_id 
				) child on child.stokbarangasal_id = st.stokbarang_id 
				WHERE st.penerimaansuppdetail_id in {$listPenerimaanManual} and st.ruangan_id = {$ruangan_id} and tglstok_in is not null
				ORDER BY st.tglstok_in 
	        ")->queryAll();

			$tmpInsert = [];
	        foreach ($stokBarang as $stok) {
	        	$idBarang = $stok['barang_id'];
	            if (isset($listIdDetail[$idBarang])) {
	                $totalStok = $stok['qty_sisa'];

	                $stokItem = $listIdDetail[$idBarang];
	                foreach ($stokItem as $key => $barang) {
	                    $currentStok = $listIdDetail[$idBarang][$key]['qty_satuanpakai'];

	                    if ($currentStok == 0 || $totalStok == 0) continue;

	                    $row = [
	                        'ruangan_id'	=> $ruangan_id,
	                        'barang_id'		=> $idBarang,
	                        'tglkadaluarsa' => $stok['tglkadaluarsa'],
	                        'nobatch'		=> $stok['nobatch'],
	                        'harganetto'	=> isset($barang['harganetto']) ? $barang['harganetto'] : $stok['harganetto'],
	                        'persendiscount'         => isset($barang['persendiscount']) ? $barang['persendiscount'] : $stok['persendiscount'],
	                        'jmldiscount'            => isset($barang['jmldiscount']) ? $barang['jmldiscount'] : $stok['jmldiscount'],
	                        'persenppn'              => isset($barang['persenppn']) ? $barang['persenppn'] : $stok['persenppn'],
	                        'persenpph'              => isset($barang['persenpph']) ? $barang['persenpph'] : $stok['persenpph'],
	                        'persenmargin'           => isset($barang['persenmargin']) ? $barang['persenmargin'] : $stok['persenmargin'],
	                        'jmlmargin'              => isset($barang['jmlmargin']) ? $barang['jmlmargin'] : $stok['jmlmargin'],
	                        'satuankecil_id'         => isset($barang['satuankecil_id']) ? $barang['satuankecil_id'] : null,
	                        'additional_data'        => isset($barang['additional_data']) ? $barang['additional_data'] : null,
	                        'tglstok_out'            => $currentDate,
	                        'stokbarangasal_id'		 => $stok['stokbarang_id'],
	                        'qtystok_in'             => 0,
	                        'stokbarang_aktif'       => false,
	                        'is_active'              => true,
	                        'returbarangdetail_id'  => isset($barang['returbarangdetail_id']) ? $barang['returbarangdetail_id'] : null
	                    ];

	                    /*
	                    * Habiskan Stok sebelumnya terlebih dahulu,
	                    * sebelum berpindah ke stok selanjutnya
	                    */
	                    if ($currentStok >= $totalStok) {
	                        $row['qtystok_out'] = $totalStok;
	                        // $listOfOutStock[] = $detail['id_stok'];
	                        $listIdDetail[$idBarang][$key]['qty_satuanpakai'] = $currentStok - $totalStok;
	                        $totalStok = 0;
	                    } else {
	                        $row['qtystok_out'] = $barang['qty_satuanpakai'];
	                        $listIdDetail[$idBarang][$key]['qty_satuanpakai'] = 0;
	                        $totalStok -= $currentStok;
	                    }

	                    $tmpInsert[] = $row;
	                }
	            }
	        }
	        if ($tmpInsert) {
	        	StokBarang::batchInsert($tmpInsert,false);
	        }else{
	        	throw new \Exception("Tidak ada stok", 1);
	        }
	    }catch(\Exception $e){
	    	throw $e;
	    }
	}
}