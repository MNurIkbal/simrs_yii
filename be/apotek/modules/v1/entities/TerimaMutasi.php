<?php

/**
 * @author : Ardi Pratama Septiadi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\entities;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\StokObatAlkesR;
use app\modules\v1\models\KetersediaanObatView;
use app\modules\v1\models\TerimaMutasiObatDetail;
use SirsCore\businessLogic\StokObatAlkes as LogicStokObatAlkes;

class TerimaMutasi
{
	public static function ByExpire($data){
		$currentDate = date('Y-m-d H:i:s');
        $jwtRuangan = Yii::$app->jwt->ruangan_id;
        $ruangan_id = isset($_POST['ruangan_id']) ? $_POST['ruangan_id'] : $jwtRuangan;
		$idObatAlkes = array_column($data, 'obatalkes_id');
		$kadaluarsa = array_column($data, 'kadaluarsa');
		$listKadaluarsa = "('" . implode("','", $kadaluarsa) . "')";
		$listObat = "(" . implode(",", $idObatAlkes) . ")";
		$command = "
			SELECT 
            st.stokobatalkes_id AS id_stok,
            st.obatalkes_id,
            st.tglkadaluarsa,
            st.nobatch,
            st.harganetto,
            st.persendiscount,
            st.jmldiscount,
            st.persenppn,
            st.persenpph,
            st.persenmargin,
            st.jmlmargin,
            st.jmlppn,
            CASE
              WHEN (tmp.stokobatalkesasal_id is null) THEN st.qtystok_in
              ELSE (st.qtystok_in - tmp.qtystok_out)
            END AS total_stok
            FROM stokobatalkes_t st
            LEFT JOIN (
              SELECT
              stokobatalkes_t.stokobatalkesasal_id,
              SUM(COALESCE(stokobatalkes_t.qtystok_out,0)) AS qtystok_out,
              stokobatalkes_t.tglkadaluarsa
              FROM stokobatalkes_t
              GROUP BY stokobatalkes_t.stokobatalkesasal_id, stokobatalkes_t.tglkadaluarsa
              ) tmp ON st.stokobatalkes_id = tmp.stokobatalkesasal_id
            WHERE st.ruangan_id = {$ruangan_id} AND st.obatalkes_id IN {$listObat} AND st.tglkadaluarsa IN {$listKadaluarsa} AND st.stokoa_aktif = TRUE
            ORDER BY st.created_date ASC
		";
		$query = Yii::$app->db->createCommand($command)->queryAll();

		$listIdDetail = [];
		foreach ($data as $val) {
			$listIdDetail[$val['obatalkes_id']][$val['kadaluarsa']][] = $val;
		}

		foreach ($query as $detail) {
            $idObat = $detail['obatalkes_id'];
            $expireObat = $detail['tglkadaluarsa'];

            if (isset($listIdDetail[$idObat][$expireObat])) {
                $totalStok = $detail['total_stok'];

                $stokItem = $listIdDetail[$idObat][$expireObat];
                foreach ($stokItem as $key => $obatAlkes) {
                    $currentStok = $listIdDetail[$idObat][$expireObat][$key]['qty_satuanpakai'];

                    if ($currentStok == 0 || $totalStok == 0) continue;

                    $row = [
                        'ruangan_id'             => $ruangan_id,
                        'obatalkes_id'           => $idObat,
                        'tglkadaluarsa'          => $detail['tglkadaluarsa'],
                        'nobatch'                => $detail['nobatch'],
                        'harganetto'             => isset($obatAlkes['harganetto']) ? $obatAlkes['harganetto'] : $detail['harganetto'],
                        'persendiscount'         => isset($obatAlkes['persendiscount']) ? $obatAlkes['persendiscount'] : $detail['persendiscount'],
                        'jmldiscount'            => isset($obatAlkes['jmldiscount']) ? $obatAlkes['jmldiscount'] : $detail['jmldiscount'],
                        'persenppn'              => isset($obatAlkes['persenppn']) ? $obatAlkes['persenppn'] : $detail['persenppn'],
                        'persenpph'              => isset($obatAlkes['persenpph']) ? $obatAlkes['persenpph'] : $detail['persenpph'],
                        'persenmargin'           => isset($obatAlkes['persenmargin']) ? $obatAlkes['persenmargin'] : $detail['persenmargin'],
                        'jmlmargin'              => isset($obatAlkes['jmlmargin']) ? $obatAlkes['jmlmargin'] : $detail['jmlmargin'],
                        'jmlppn'                 => isset($obatAlkes['jmlppn']) ? $obatAlkes['jmlppn'] : $detail['jmlppn'],
                        'obatalkespasien_id'     => isset($obatAlkes['obatalkespasien_id']) ? $obatAlkes['obatalkespasien_id'] : null,
                        'satuankecil_id'         => isset($obatAlkes['satuankecil_id']) ? $obatAlkes['satuankecil_id'] : null,
                        'pemakaianobatdetail_id' => isset($obatAlkes['pemakaianobatdetail_id']) ? $obatAlkes['pemakaianobatdetail_id'] : null,
                        'mutasiobatdetail_id'    => isset($obatAlkes['mutasiobatdetail_id']) ? $obatAlkes['mutasiobatdetail_id'] : null,
                        'adjusmenobatkeluar_id'  => isset($obatAlkes['adjusmenobatkeluar_id']) ? $obatAlkes['adjusmenobatkeluar_id'] : null,
                        'additional_data'        => isset($obatAlkes['additional_data']) ? $obatAlkes['additional_data'] : null,
                        'adjusmenobatkeluar_id'  => isset($obatAlkes['adjusmenobatkeluar_id']) ? $obatAlkes['adjusmenobatkeluar_id'] : null,
                        'tglstok_out'            => $currentDate,
                        'stokobatalkesasal_id'   => $detail['id_stok'],
                        'qtystok_in'             => 0,
                        'stokoa_aktif'           => false,
                        'is_active'              => true,
                    ];

                    /*
                    * Habiskan Stok sebelumnya terlebih dahulu,
                    * sebelum berpindah ke stok selanjutnya
                    */
                    if ($currentStok >= $totalStok) {
                        $row['qtystok_out'] = $totalStok;
                        $listOfOutStock[] = $detail['id_stok'];
                        $listIdDetail[$idObat][$expireObat][$key]['qty_satuanpakai'] = $currentStok - $totalStok;
                        $totalStok = 0;
                    } else {
                        $row['qtystok_out'] = $obatAlkes['qty_satuanpakai'];
                        $listIdDetail[$idObat][$expireObat][$key]['qty_satuanpakai'] = 0;
                        $totalStok -= $currentStok;
                    }

                    $tmpInsert[] = $row;
                    $idTerima = isset($obatAlkes['terimamutasidetail_id']) ? $obatAlkes['terimamutasidetail_id'] : null;
                    $tmpInsert[] = self::generateMutasi($row, $idTerima);
                }
            }
        }
        if ($tmpInsert) {
            StokObatAlkes::batchInsert($tmpInsert,false);
        }
        return true;
	}

	public static function generateMutasi($row, $idTerima)
    {
        $row['qtystok_in'] = $row['qtystok_out'];
        $row['ruangan_id'] = $_POST['ruangan_penerima_id'];
        $row['tglstok_in'] = $row['tglstok_out'];
        $row['qtystok_out'] = 0;
        $row['terimamutasidetail_id'] = $idTerima;
        $row['stokoa_aktif'] = true;
        unset($row['tglstok_out']);
        unset($row['stokobatalkesasal_id']);
        unset($row['mutasiobatdetail_id']);
        return $row;
    }

    public static function langsung($data)
    {
        $tanggalBerlaku = date('Y-m-d H:i:s');
        // Mencari Metode
        $konfig = Yii::$app->db->createCommand("
            SELECT metodeantrian FROM konfigfarmasi_k
            WHERE tglberlaku >= '{$tanggalBerlaku}'
            AND konfigfarmasi_aktif = true
            AND is_active = true
        ")->queryOne();
        // Mencari Metode dengan nilai default FEFO
        $currentMetode = LogicStokObatAlkes::FEFO;
        if ($konfig) {
            $currentMetode = isset($konfig['metodeantrian'])
                                ? strtoupper($konfig['metodeantrian']) : LogicStokObatAlkes::FEFO;
        }

        if ($currentMetode === LogicStokObatAlkes::FEFO) {
           $methode = LogicStokObatAlkes::methodeFEFO($data,$tanggalBerlaku,true);
        } else {
           $methode = LogicStokObatAlkes::methodeFIFO($data,$tanggalBerlaku,true);
        }
    }

    /*
    * check stok before insert kartu stok
    */
    public static function checkStockIn($ruanganasal_id,$stok_in)
    {
        $listObat = ArrayHelper::getColumn($stok_in,'obatalkes_id');
        $sisa = self::getSisaStok($ruanganasal_id,$listObat);
        $namaObat = ArrayHelper::map($sisa,'obatalkes_id','obatalkes_nama');

        $current_stok = ArrayHelper::map($sisa,'obatalkes_id','qty_sisa');
        $mutasi = ArrayHelper::map($stok_in,'obatalkes_id','qty_satuanpakai');

        $inEqual = [];
        foreach ($mutasi as $obat_id => $qty_mutasi) {
            if(isset($current_stok[$obat_id])){
                if((float)$qty_mutasi > (float)$current_stok[$obat_id]){
                    $inEqual[] = [
                        'obatalkes_id' => $obat_id,
                        'mutasi' => $qty_mutasi,
                        'sisa' => $current_stok[$obat_id],
                        'obatalkes_nama' => $namaObat[$obat_id]
                    ];
                }
            }
        }

        return $inEqual;
    }

    public static function getSisaStok($ruangan_id,$obatalkes_id)
    {
        $query = KetersediaanObatView::find()
            ->select(['obatalkes_id', 'qty_dipesan', 'qty_tersedia', 'qty_stok as qty_sisa', 'obatalkes_nama'])
            ->where([
                'ruangan_id' => $ruangan_id,
                'obatalkes_id' => $obatalkes_id
            ])->asArray()->all();

        return $query;
    }

    /*
    * verify after insert kartu stok
    */
    public static function verifyStock($listTerima)
    {
        $result = TerimaMutasiObatDetail::find()
        ->select([
            'stokobatalkes_t.terimamutasidetail_id',
            'terimamutasiobatdetail_t.jmlterima',
            'sum(COALESCE(qtystok_in,0)) AS qtystok_in'
        ])
        ->rightJoin('stokobatalkes_t','stokobatalkes_t.terimamutasidetail_id = terimamutasiobatdetail_t.terimamutasiobatdetail_id')
        ->where(['terimamutasiobatdetail_id'=>$listTerima])
        ->groupBy('stokobatalkes_t.terimamutasidetail_id,terimamutasiobatdetail_t.jmlterima')
        ->asArray(
        )->all();

        if(count($result) <= 0){
            throw new \yii\base\Exception("Stok Gagal Tersimpan", 1);
        }

        $terima = ArrayHelper::map($result,'terimamutasidetail_id','jmlterima');
        $kartustok = ArrayHelper::map($result,'terimamutasidetail_id','qtystok_in');
        $diff = array_diff($terima,$kartustok);
        if (count($diff) > 0){
            throw new \yii\base\Exception("Stok Tidak Tersimpan", 1);
        }
            
    }
}
