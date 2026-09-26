<?php

namespace app\modules\v1\actions\TransaksiRetur;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\modules\v1\models\InfoResepDetailView;
use app\modules\v1\models\InfoResepView;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\ReturResep;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\LogActivityR;
use app\components\ApotekComponent;
use SirsCore\businessLogic\ReturResep as BusinessLogicReturResep;
use Doco\models\Pendaftaran;

/**
 *
 */
class ReturAction extends Action
{
    private $user_id;
    private $ruangan_id;
    private $user_name;

    public function run($no_resep = null)
    {
        $this->user_id = Yii::$app->jwt->user->loginpemakai_id;
        $this->ruangan_id = Yii::$app->jwt->ruangan_id;
        $this->user_name = Yii::$app->jwt->user->nama_pemakai;

        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $detail_retur = $post['detail'];
        $total_retur = 0;
        try {
            if ( $this->returLimitValidasi($no_resep)) {
                throw new \Exception("Resep Sedang Dalam Proses Retur Oleh User Lain!", 1);
            }
            $user_login = Yii::$app->jwt->user->loginpemakai_id;
            $info_resep = InfoResepView::find()
                ->select("
                    penjualanresep_id,
                    instalasi_resep_id,
                    ruangan_id,
                    pendaftaran_id,
                    status_reseptur_id,
                    biayaadministrasi,
                    totalhargajual,
                    totaltagihan,
                    status_bayar,
                    jenispenjualan_id,
                    status_worklist,
                    pendaftaran_id
                ")
                ->where(['no_resep' => $no_resep])
                ->asArray()->one();

            $pendaftaranId = ArrayHelper::getValue($info_resep, 'pendaftaran_id');
            if(!empty($pendaftaranId)) {
                $dataPendaftaran = Pendaftaran::findOne($pendaftaranId);
                $isCloseBill = ArrayHelper::getValue($dataPendaftaran, 'is_close_bill', false);
                if($isCloseBill) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => "Pasien sudah dilakukan proses Lock Bill."
                    ];
                }
            }
    
            if (is_null($info_resep))
                throw new \Exception("Resep tidak ditemukan", 1);

            if ($info_resep['status_reseptur_id'] != DocoConstants::RESEPTUR_DISERAHKAN)
                throw new \Exception("Resep belum diserahkan", 1);
                
            /**
             * Handling retur resep per nomer pendaftaran.
             */
            if(! empty($info_resep['pendaftaran_id'])) {
                $validasiReturPendaftaran = BusinessLogicReturResep::validasiReturPendaftaran($info_resep['pendaftaran_id']);
                if(! empty($validasiReturPendaftaran)) {
                    throw new \Exception("Transaksi ini sudah pernah dilakukan dengan No.". $validasiReturPendaftaran['no_returresep']." !", 1);
                }
            }

            $detail_resep = InfoResepDetailView::find()
                ->select("
                    reseptur_id,
                    obatalkespasien_id,
                    obatalkes_id,
                    racikan_id,
                    satuankecil_id,
                    qty_oa,
                    qty_konversi,
                    det,
                    det_transaksi,
                    det_konversi,
                    nilai_konversi,
                    hargajual_satuan,
                    harga_jual,
                    hargasatuan_oa,
                    harga_konversi,
                    harganetto,
                    disc,
                    margin,
                    ppn,
                    additional_data
                ")
                ->where(['penjualanresep_id' => $info_resep['penjualanresep_id']])
                ->andWhere(['racikan_id' => DocoConstants::ID_NON_RACIKAN])
                ->orderBy('obatalkes_id')
                ->indexBy("obatalkespasien_id")
                ->asArray()->all();

            $detail_retur_resep = $retur_details = $arr_update = $arr_update_reseptur = [];
            foreach ($detail_retur as $key => $item_retur) {
                $id = $item_retur['identifier'];
                if (array_key_exists($id, $detail_resep)) {
                    $data = $detail_resep[$id];

                    if ($item_retur['qty_retur'] <= 0 || $item_retur['qty_retur'] > $data['qty_oa']) {
                        continue;
                    }

                    $label_column = is_null($data['det']) ? 'qty_oa' : 'det';
                    $label_column_medis = is_null($data['det_transaksi']) ? 'qty_medis' : 'det_medis';
                    $label_column_konversi = is_null($data['det']) ? 'qty_konversi' : 'det_konversi';
                    $qty_update = $data[$label_column] - $item_retur['qty_retur'];
                    $total_retur += ($data['hargasatuan_oa'] * $item_retur['qty_retur']);

                    $label_column_reseptur = is_null($data['det']) ? 'qty_reseptur' : 'det';
                    $label_column_konversi_reseptur = is_null($data['det']) ? 'qty_konversi' : 'det_konversi';

                    $arr_update['set'][$label_column][] = $qty_update;
                    $arr_update['set'][$label_column_medis][] = $qty_update;
                    $arr_update['set'][$label_column_konversi][] = $qty_update * $data['nilai_konversi'];
                    $arr_update['set']['hargajual_oa'][] = $qty_update * $data['hargasatuan_oa'];
                    $arr_update['set']['is_deleted'][] = ($qty_update == 0) ? true : false;
                    $arr_update['set']['is_retur'][] = true;

                    $arr_update['conditions']['obatalkespasien_id'][] = $data['obatalkespasien_id'];

                    if($data['reseptur_id'] != null) {
                        $arr_update_reseptur['set'][$label_column_reseptur][]               = $qty_update;
                        $arr_update_reseptur['set'][$label_column_konversi_reseptur][]      = $qty_update * $data['nilai_konversi'];
                        $arr_update_reseptur['set']['hargajual_reseptur'][]                 = $qty_update * $data['hargasatuan_oa'];
                        $arr_update_reseptur['set']['is_retur'][]                           = true;

                        $arr_update_reseptur['conditions']['reseptur_id'][] = $data['reseptur_id'];
                        $arr_update_reseptur['conditions']['obatalkes_id'][] = $data['obatalkes_id'];
                        $arr_update_reseptur['conditions']['racikan_id'][] = $data['racikan_id'];
                    }

                    $detail_retur_resep[$key] = [
                        'obatalkespasien_id' => $data['obatalkespasien_id'],
                        'hargasatuan'        => $data['hargasatuan_oa'],
                        'qty_retur'          => $item_retur['qty_retur'],
                        'created_by'         => $user_login
                    ];

                    $retur_details[$key] = $detail_retur_resep[$key];
                    $retur_details[$key]['qty_potong'] = $item_retur['qty_retur'] * $data['nilai_konversi'];
                    $retur_details[$key]['satuankecil_id'] = $data['satuankecil_id'];
                    $retur_details[$key]['harganetto'] = $data['harganetto'];
                    $retur_details[$key]['disc'] = $data['disc'];
                    $retur_details[$key]['margin'] = $data['margin'];
                    $retur_details[$key]['ppn'] = $data['ppn'];
                }
            }

            // Insert to Retur
            if (count($detail_retur_resep) <= 0)
                throw new \Exception("Tidak ada data yang di retur", 1);

            $model_retur = new ReturResep;
            $model_retur->ruangan_id           = $info_resep['ruangan_id'];
            $model_retur->penjualanresep_id    = $info_resep['penjualanresep_id'];
            $model_retur->tgl_retur            = date('Y-m-d H:i:s');
            $model_retur->pendaftaran_id       = $info_resep['pendaftaran_id'];
            $model_retur->total_retur          = $total_retur;
            $model_retur->status_retur         = DocoConstants::RETUR_VERIFIKASI;
            $model_retur->tgl_verif            = date('Y-m-d H:i:s');
            $model_retur->user_verifikator     = $this->user_id;

            if (!$model_retur->save())
                throw new \Exception("Gagal simpan retur resep", 1);

            // Simpan ke logActivity
            $model_log_retur = new LogActivityR;
            $model_log_retur->tgl           = date('Y-m-d H:i:s');
            $model_log_retur->tipe          = 'RETUR';
            $model_log_retur->aksi          = 'Tambah';
            $model_log_retur->keterangan    =  $this->user_name;
            $model_log_retur->created_by    =  $this->user_id;
            $model_log_retur->transaksi_id  =  $model_retur->returresep_id;
            $model_log_retur->save();

            $returresep_id = $model_retur->returresep_id;

            foreach ($detail_retur_resep as $index => $row) {
                $row['returresep_id'] = $returresep_id;
                $detail_retur_resep[$index] = $row;
            }
            ApotekComponent::insertMultiple('returresepdetail_t', $detail_retur_resep);

            // Update Obat Alkes Pasien
            if (count($arr_update) <=0)
                throw new \Exception("Tidak ada data yg di retur", 1);
            ApotekComponent::updateMultiple('obatalkespasien_t', $arr_update['set'], $arr_update['conditions']);

            // Update Reseptur Detail
            if (count($arr_update_reseptur) > 0) {
                ApotekComponent::updateMultiple('resepturdetail_t', $arr_update_reseptur['set'], $arr_update_reseptur['conditions']);
            }

            $update_add_data = $connection->createCommand("
                SELECT ot.*
                FROM obatalkespasien_t ot
                JOIN penjualanresep_t pt on ot.penjualanresep_id = pt.penjualanresep_id
                WHERE pt.noresep = '{$no_resep}'
                AND ot.is_deleted = false
            ")->queryAll();

            foreach ($update_add_data as $val_ot) {
                $ot = ObatAlkesPasien::find()
                    ->where(['obatalkespasien_id' =>$val_ot['obatalkespasien_id']])
                    ->one();
                $add_ot = json_decode($ot->additional_data, true);
                $add_ot['qty_input'] = (string) $ot->qty_oa;
                $ot->additional_data = json_encode($add_ot);
                $ot->save();
            }

            $penjualanResep = PenjualanResep::find()
                ->where(['penjualanresep_id' => $info_resep['penjualanresep_id']])->one();
            $penjualanResep->totalhargajual -= $total_retur;
            if ($penjualanResep->totalhargajual <=0) {
                $penjualanResep->biayaadministrasi = 0;
                $penjualanResep->is_deleted = true;
            }

            if (!$penjualanResep->save())
                throw new \Exception("Gagal update total tagihan", 1);

            /**
            **deprecated
            ** change to mengembalikanStok
            $retur_stok = $this->StokReturResep($returresep_id);
            if (!$retur_stok['is_success'])
                throw new \Exception("Stok tidak dapat di proses", 1);

            **/
            $this->mengembalikanStok($penjualanResep->penjualanresep_id, $retur_details, $returresep_id);

            $transaction->commit();

            // comment this slow query and not usefull queries
            // $data_detail_penjualan = ApotekComponent::getDetailReturByPenjualan($info_resep['penjualanresep_id']);
            $data_retur = $connection->createCommand("
                SELECT
                    no_returresep
                FROM returresep_t
                WHERE returresep_id = {$returresep_id}
            ")->queryOne();

            return [
                'message' => 'Data Berhasil di simpan',
                'id_retur' => DocoHelpers::encrypt($returresep_id),
                'data_retur' => [],
                'no_returresep' => $data_retur['no_returresep']
            ];

        } catch (\yii\db\Exception $e) {
            $this->controller->logError($e);
            $transaction->rollBack();
            return [
                'status' => 500,
                'text' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $this->controller->logError($e);
            $transaction->rollBack();
            return [
                'status' => 500,
                'text' => $e->getMessage()
            ];
        }
    }

    public function mengembalikanStok($penjualanresep_id,$detail_retur, $returresep_id)
    {
        $currentDate = date('Y-m-d H:i:s');
        $data_stok = $this->getDataStokResep($penjualanresep_id);
        $detail_retur = $this->getReturDetailId($detail_retur, $returresep_id);
        $data_stok_retur = ArrayHelper::index($data_stok,null,'obatalkespasien_id');
        $insert_stokobatalkes = [];
        foreach ($detail_retur as $item_retur) {
            $logstokobat = $data_stok_retur[$item_retur['obatalkespasien_id']];
            $qtysisaretur = $item_retur['qty_potong'];
            foreach ($logstokobat as $logstok_) {
                if($qtysisaretur <= 0) {continue;}
                $qtyIn = $qtysisaretur < $logstok_['qty'] ? $qtysisaretur : $logstok_['qty'];
                $qtysisaretur = $qtysisaretur - $logstok_['qty'];
                $insert_stokobatalkes[] = [
                    'ruangan_id'            => $this->ruangan_id,
                    'created_by'            => $this->user_id,
                    'tglstok_in'            => $currentDate,
                    'stokoa_aktif'          => true,
                    'qtystok_out'           => 0,
                    'qtystok_in'            => $qtyIn,
                    'returresepdetail_id'   => $item_retur['returresepdetail_id'],
                    'obatalkes_id'          => $logstok_['obatalkes_id'],
                    'tglkadaluarsa'         => $logstok_['tglkadaluarsa'],
                    'satuankecil_id'        => $item_retur['satuankecil_id'],
                    'harganetto'            => $item_retur['harganetto'],
                    'persendiscount'        => isset($item_retur['margin']) && $item_retur['margin'] >0 && isset($item_retur['harganetto']) && $item_retur['harganetto'] >0 && isset($item_retur['disc']) && $item_retur['disc'] >0 ? $item_retur['disc'] / ($item_retur['harganetto'] + $item_retur['margin']) * 100 : null,
                    'jmldiscount'           => $item_retur['disc'],
                    'persenppn'             => isset($item_retur['margin']) && $item_retur['margin'] >0 && isset($item_retur['harganetto']) && $item_retur['harganetto'] >0 && isset($item_retur['ppn']) && $item_retur['ppn'] >0 ? $item_retur['ppn'] / ($item_retur['harganetto'] + $item_retur['margin']) * 100 : null,
                    'jmlppn'                => $item_retur['ppn'],
                    'persenmargin'          => isset($item_retur['margin']) && $item_retur['margin'] >0 && isset($item_retur['harganetto']) && $item_retur['harganetto'] >0 ? $item_retur['margin'] / $item_retur['harganetto'] * 100 : null,
                    'jmlmargin'             => $item_retur['margin'],
                ];

            }
        }
        StokObatAlkes::batchInsert($insert_stokobatalkes);
    }

    private function getReturDetailId($detail_retur_, $returresep_id)
    {
        $listoa = ArrayHelper::map($detail_retur_,'obatalkespasien_id','obatalkespasien_id');
        $listoa = array_filter($listoa);
        $list_obatalkespasien = implode($listoa, ',');
        $query = "
            SELECT obatalkespasien_id, returresepdetail_id
            FROM returresepdetail_t
            WHERE obatalkespasien_id IN ($list_obatalkespasien) AND returresep_id = {$returresep_id}
        ";
        $returdetail = Yii::$app->db->createCommand($query)->queryAll();
        $returdetail = ArrayHelper::map($returdetail,'obatalkespasien_id','returresepdetail_id');
        $new_detail_retur_ = [];
        foreach ($detail_retur_ as $key => $item_retur_) {
            $new_detail_retur_[$key] = $item_retur_;
            $new_detail_retur_[$key]['returresepdetail_id'] = $returdetail[$item_retur_['obatalkespasien_id']];
        }
        return $new_detail_retur_;
    }

    private function getDataStokResep($penjualanresep_id)
    {
        $query = "
            SELECT
                stok.obatalkes_id,
                SUM(stok.qtystok_out) AS qty_out_resep,
                COALESCE(hist_retur.qty_retur_sebelumnya,0) AS qty_retur_sebelumnya,
                (SUM(stok.qtystok_out) - COALESCE(hist_retur.qty_retur_sebelumnya,0)) AS qty,
                stok.tglkadaluarsa,
                stok.obatalkespasien_id
            FROM stokobatalkes_t stok
            LEFT JOIN obatalkespasien_t oap ON oap.obatalkespasien_id = stok.obatalkespasien_id
            LEFT JOIN penjualanresep_t resep ON resep.penjualanresep_id = oap.penjualanresep_id
            LEFT JOIN(
                SELECT st.obatalkes_id, retur.penjualanresep_id, SUM(st.qtystok_in) as qty_retur_sebelumnya,st.tglkadaluarsa
                FROM stokobatalkes_t st
                LEFT JOIN returresepdetail_t retur_detail ON retur_detail.returresepdetail_id = st.returresepdetail_id
                LEFT JOIN returresep_t retur ON retur.returresep_id = retur_detail.returresep_id
                GROUP BY retur.penjualanresep_id,st.tglkadaluarsa, st.obatalkes_id
            ) hist_retur ON hist_retur.penjualanresep_id = resep.penjualanresep_id AND hist_retur.tglkadaluarsa = stok.tglkadaluarsa AND hist_retur.obatalkes_id = stok.obatalkes_id
            WHERE resep.penjualanresep_id = {$penjualanresep_id}
            GROUP BY
                stok.obatalkes_id,
                stok.tglkadaluarsa,
                stok.obatalkespasien_id,
                hist_retur.qty_retur_sebelumnya
        ";
        return Yii::$app->db->createCommand($query)->queryAll();
    }

    private function getDataRetur($returresep_id)
    {
        $query = "
            SELECT
                detail.returresepdetail_id, detail.obatalkespasien_id ,detail.qty_retur,
                CASE
                    WHEN pasien.qty_oa = 0 THEN 1
                    ELSE pasien.qty_konversi / pasien.qty_oa
                END AS nilai_konversi,
                CASE
                    WHEN pasien.qty_oa = 0 THEN detail.qty_retur
                    ELSE detail.qty_retur * (pasien.qty_konversi / pasien.qty_oa)
                END AS qty_konversi
            FROM returresepdetail_t detail
            JOIN obatalkespasien_t pasien ON detail.obatalkespasien_id = pasien.obatalkespasien_id
            WHERE returresep_id = {$returresep_id}
        ";
        return Yii::$app->db->createCommand($query)->queryAll();
    }

    public function StokReturResep($returresep_id = null, $data_retur = [])
    {
        try {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $data_stok = $connection->createCommand("
                SELECT
                    stokobatalkesasal_id , stokobatalkes_id ,
                    ruangan_id, tglkadaluarsa, nobatch, harganetto, persendiscount, jmldiscount,
                    persenppn, jmlppn, persenmargin, jmlmargin,
                    obatalkespasien_id , obatalkes_id , qtystok_in ,
                    qtystok_out , satuankecil_id
                FROM stokobatalkes_t st
                WHERE obatalkespasien_id IN (
                    SELECT obatalkespasien_id
                    FROM returresepdetail_t rt WHERE returresep_id = $returresep_id
                )
                ORDER BY st.tglkadaluarsa ASC, st.tglstok_in ASC
            ")
            ->queryAll();
            $data_stok = ArrayHelper::index($data_stok, 'stokobatalkesasal_id', 'obatalkespasien_id');

            $data_retur = $connection->createCommand("
                SELECT
                    detail.returresepdetail_id, detail.obatalkespasien_id ,detail.qty_retur,
                    CASE
                        WHEN pasien.qty_oa = 0 THEN 1
                        ELSE pasien.qty_konversi / pasien.qty_oa
                    END AS nilai_konversi,
                    CASE
                        WHEN pasien.qty_oa = 0 THEN detail.qty_retur
                        ELSE detail.qty_retur * (pasien.qty_konversi / pasien.qty_oa)
                    END AS qty_konversi
                FROM returresepdetail_t detail
                JOIN obatalkespasien_t pasien ON detail.obatalkespasien_id = pasien.obatalkespasien_id
                WHERE returresep_id = {$returresep_id}
            ")->queryAll();

            $insert_stokobatalkes = [];
            foreach ($data_retur as $item_retur) {
                $obatalkespasien_id = $item_retur['obatalkespasien_id'];
                $data_obat = $data_stok[$obatalkespasien_id];
                $qty_sisa_retur = $item_retur['qty_konversi'];
                foreach ($data_obat as $obat) {
                    $qty_in = $item_retur['qty_konversi'];
                    if ($obat['qtystok_out'] < $item_retur['qty_konversi']) {
                        $qty_in = $obat['qtystok_out'];
                        $qty_sisa_retur -= $obat['qtystok_out'];
                    }

                    $insert_stokobatalkes[] = [
                        'ruangan_id'          => $this->ruangan_id,   'created_by'   => $this->user_id,
                        'tglstok_in'          => date('Y-m-d H:m:s'), 'stokoa_aktif' => true,
                        'qtystok_out'         => 0,
                        'obatalkespasien_id'  => $obat['stokobatalkes_id'],
                        'obatalkes_id'        => $obat['obatalkes_id'],
                        'tglkadaluarsa'       => $obat['tglkadaluarsa'],
                        'nobatch'             => $obat['nobatch'],
                        'qtystok_in'          => $qty_in,
                        'satuankecil_id'      => $obat['satuankecil_id'],
                        'returresepdetail_id' => $item_retur['returresepdetail_id'],
                        'harganetto'          => $obat['harganetto'],
                        'persendiscount'      => $obat['persendiscount'],
                        'jmldiscount'         => $obat['jmldiscount'],
                        'persenppn'           => $obat['persenppn'],
                        'jmlppn'              => $obat['jmlppn'],
                        'persenmargin'        => $obat['persenmargin'],
                        'jmlmargin'           => $obat['jmlmargin'],
                    ];
                }
            }

            StokObatAlkes::batchInsert($insert_stokobatalkes);
            $transaction->commit();
            return [
                'is_success' => true,
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'is_success' => false,
                'status' => 500,
                'text' => $e->getMessage()
            ];
        }
    }

    public function returLimitValidasi($no_resep = null)
    {
        $cache = Yii::$app->cache;
        if ( is_null($no_resep)) {
            return false;
        }
        $cacheName = 'retur-validator-'.$no_resep;
        if ( !empty($cache->get($cacheName)) ) {
            return true;
        } else {
            $cache->set($cacheName, $no_resep, 15);
        }

        return false;
    }
}
