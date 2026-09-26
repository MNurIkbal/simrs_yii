<?php

namespace SirsCore\businessLogic;

/**
 * Function retur resep.
 * 
 * @author Novia. S
 */

use SirsCore\models\LogActivityR;
use SirsCore\models\ReturResep as ModelsReturResep;
use SirsCore\models\ReturResepDetail;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\BatchUpdate;
use Doco\models\ObatAlkesPasien;
use Doco\models\Pegawai;
use Doco\models\Pendaftaran;
use Doco\models\PenjualanResep;
use Doco\models\Reseptur;
use Doco\models\ResepturDetail;
use Exception;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class ReturResep
{
    protected $detailRetur;
    protected $pendaftaran_id;
    protected $ruangan_retur;
    protected $tanggal_retur;
    protected $returresep_id;
    protected $payloadLog;

    /**
     * Function save ke reseptur_t
     * 
     * @author Maulana Muhammad Rizky.
     * @return array
     */
    public static function createReturReseptur()
    {
        $user_login = Yii::$app->jwt->user->loginpemakai_id;
        $user_name = Yii::$app->jwt->user->nama_pemakai;

        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $total_retur = 0;
        $tmpObatAlkesId = [];
        try {
            $detailRetur = ArrayHelper::getValue($post, 'data_retur');
            $pendaftaran_id = ArrayHelper::getValue($post, 'pendaftaran_id');
            $ruangan_retur = ArrayHelper::getValue($post, 'ruangan_retur');
            $tanggal_retur = ArrayHelper::getValue($post, 'tanggal_retur');
            
            if (self::transcationValidator($pendaftaran_id)) {
                Yii::$app->response->statusCode = 422;
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Transaksi ini sedang proses oleh user lain, Silahkan coba beberapa saat lagi !',
                ];
            }

            $validasiInjection = self::validasiInjection($pendaftaran_id);
            if (!empty($validasiInjection)) {
                Yii::$app->cache->delete('transaction-retur-validator-' . $pendaftaran_id); // delete cache ketika pendaftaran gagal validasi
                Yii::$app->response->statusCode = 422;
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Transaksi ini sudah pernah dilakukan dengan No.' . $validasiInjection['no_returresep'] . ' !',
                ];
            }

            $dataOa = self::getDataObatAlkes($pendaftaran_id);
            $dataBmhp = self::getDataBmhp($pendaftaran_id);
            $detail_retur_resep = [];
            $tmpDataObatAlkes = [];
            $qtyReturObarAlkes = [];
            $qtyReturBmhp = [];

            // Simpan data sebelum nya buat komparasi.
            foreach ($detailRetur as $key => $valueRetur) {
                if (isset($valueRetur['qty_retur'])) {
                    $total = $valueRetur['harga_satuan'] * $valueRetur['qty_retur'];
                    $total_retur += $total;
                    $tmpDataObatAlkes[$valueRetur['obatalkes_id']] = [];

                    // Data resep.
                    if ($valueRetur['transaksi'] == 'resep') {
                        $qtyReturObarAlkes[$valueRetur['obatalkes_id']] = [
                            'qty_retur' => $valueRetur['qty_retur'],
                            'transaksi' => $valueRetur['transaksi']
                        ];
                    }

                    // Data bmhp.
                    if ($valueRetur['transaksi'] == 'bmhp') {
                        $qtyReturBmhp[$valueRetur['obatalkes_id']] =  [
                            'qty_retur' => $valueRetur['qty_retur'],
                            'transaksi' => $valueRetur['transaksi']
                        ];
                    }
                }
            }

            $pendaftaranData = Pendaftaran::find()
            ->select([
                'pasienadmisi_id',
                'pasien_id'
            ])
            ->where([
                'pendaftaran_id' => $pendaftaran_id
            ])->asArray()->one();
            
            $model_retur = new ModelsReturResep;
            $model_retur->ruangan_id       = $ruangan_retur;
            $model_retur->tgl_retur        = $tanggal_retur;
            $model_retur->total_retur      = $total_retur;
            $model_retur->status_retur     = DocoConstants::RETUR_BELUM_VERIFIKASI;
            $model_retur->pendaftaran_id   = $pendaftaran_id;
            $model_retur->pasien_id        = isset($pendaftaranData['pasien_id']) ? $pendaftaranData['pasien_id'] : null;
            $model_retur->pasienadmisi_id  = isset($pendaftaranData['pasienadmisi_id']) ? $pendaftaranData['pasienadmisi_id'] : null;
            $model_retur->save();

            $returresep_id = $model_retur->returresep_id;
            
            // Assign QTY Retur Obat ke setiap data.
            foreach ($dataOa as $key => $valueOa) {
                if (array_key_exists($valueOa['obatalkes_id'], $tmpDataObatAlkes)) {
                    if(isset($qtyReturObarAlkes[$valueOa['obatalkes_id']])) {
                        $mergeArray = array_merge($qtyReturObarAlkes[$valueOa['obatalkes_id']], $valueOa);
                        array_push($tmpDataObatAlkes[$valueOa['obatalkes_id']], $mergeArray);
                    }
                }
            }
            

            // Assign QTY Retur BMHP ke setiap data.
            foreach ($dataBmhp as $key => $valueBmhp) {
                if (array_key_exists($valueBmhp['obatalkes_id'], $tmpDataObatAlkes)) {
                    if(isset($qtyReturBmhp[$valueBmhp['obatalkes_id']])) {
                        $mergeArray = array_merge($qtyReturBmhp[$valueBmhp['obatalkes_id']], $valueBmhp);
                        array_push($tmpDataObatAlkes[$valueBmhp['obatalkes_id']], $mergeArray);
                    }
                }
            }

            $tmpObatPenampung = [];
            $bmhpPenampung = [];
            $validatePenampung = [];
            $validateBmhpPenampung = [];
            foreach ($tmpDataObatAlkes as $key => $value) {
                foreach ($value as $keyDetail => $valueDetail) {
                    if ($valueDetail['transaksi'] == 'resep') {
                        $_qtyObat = isset($valueDetail['det_konversi']) ? $valueDetail['det_konversi'] : $valueDetail['qty_konversi'];
                        $nilaiKonversi = (int) trim($valueDetail['nilai_konversi'], '"');
                        $qtyRetur = $valueDetail['qty_retur'];
                        $nilaiPengurang = isset($tmpObatPenampung[$valueDetail['obatalkes_id']]) ? $tmpObatPenampung[$valueDetail['obatalkes_id']] : $valueDetail['qty_retur'];

                        if (($_qtyObat - $nilaiPengurang) < 0 && $_qtyObat != 0) {
                            $tmpObatPenampung[$valueDetail['obatalkes_id']] = abs($_qtyObat - $valueDetail['qty_retur']);
                            $qtyRetur = $_qtyObat;
                            $valueDetail['qty_retur'] = abs($_qtyObat - $valueDetail['qty_retur']);
                            $_qtyObat = 0;
                        }

                        if (($_qtyObat - $nilaiPengurang) >= 0 && $_qtyObat != 0) {
                            // Cek kondisi apabila ada data obat yang kedua / obatnya di sum.
                            if (array_key_exists($valueDetail['obatalkes_id'], $tmpObatPenampung)) {
                                $qtyRetur = $tmpObatPenampung[$valueDetail['obatalkes_id']];
                                $_qtyObat -= $nilaiPengurang;
                            } else {
                                if (!in_array($valueDetail['obatalkes_id'], $validatePenampung)) {
                                    $qtyRetur = $valueDetail['qty_retur'];
                                    $_qtyObat -= $valueDetail['qty_retur'];
                                    $validatePenampung[] = $valueDetail['obatalkes_id'];
                                }
                            }
                        }

                        if ($_qtyObat != $valueDetail['qty_awal']) {
                            $detail_retur_resep[] =  [
                                'obatalkespasien_id' => $valueDetail['obatalkespasien_id'],
                                'hargasatuan'        => (int) $valueDetail['hargasatuan_oa'] / $nilaiKonversi,
                                'qty_retur'          => (int) $qtyRetur,
                                'created_by'         => $user_login,
                                'returresep_id'      => $returresep_id,
                            ];
                        }
                    }

                    if ($valueDetail['transaksi'] == 'bmhp') {
                        $qtyRetur = $valueDetail['qty_retur'];
                        $nilaiPengurang = isset($bmhpPenampung[$valueDetail['obatalkes_id']]) ? $bmhpPenampung[$valueDetail['obatalkes_id']] : $valueDetail['qty_retur'];

                        if (($valueDetail['qty_oa'] - $nilaiPengurang) < 0 && $valueDetail['qty_oa'] != 0) {
                            $bmhpPenampung[$valueDetail['obatalkes_id']] = abs($valueDetail['qty_oa'] - $valueDetail['qty_retur']);
                            $qtyRetur = $valueDetail['qty_oa'];
                            $valueDetail['qty_retur'] = abs($valueDetail['qty_oa'] - $valueDetail['qty_retur']);
                            $valueDetail['qty_oa'] = 0;
                        }

                        if (($valueDetail['qty_oa'] - $nilaiPengurang) >= 0 && $valueDetail['qty_oa'] != 0) {
                            // Cek kondisi apabila ada data obat yang kedua / obatnya di sum.
                            if (array_key_exists($valueDetail['obatalkes_id'], $bmhpPenampung)) {
                                $qtyRetur = $bmhpPenampung[$valueDetail['obatalkes_id']];
                                $valueDetail['qty_oa'] -= $nilaiPengurang;
                            } else {
                                if (!in_array($valueDetail['obatalkes_id'], $validateBmhpPenampung)) {
                                    $qtyRetur = $valueDetail['qty_retur'];
                                    $valueDetail['qty_oa'] -= $valueDetail['qty_retur'];
                                    $validateBmhpPenampung[] = $valueDetail['obatalkes_id'];
                                }
                            }
                        }

                        if ($valueDetail['qty_oa'] != $valueDetail['qty_awal']) {
                            $detail_retur_resep[] =  [
                                'obatalkespasien_id' => $valueDetail['obatalkespasien_id'],
                                'hargasatuan'        => (int) $valueDetail['hargasatuan_oa'],
                                'qty_retur'          => (int) $qtyRetur,
                                'created_by'         => $user_login,
                                'returresep_id'      => $returresep_id,
                            ];
                        }
                    }
                }
            }
            ReturResepDetail::batchInsert($detail_retur_resep);

            //Simpan ke logActivity
            $model_log_retur = new LogActivityR;
            $model_log_retur->tgl           = date('Y-m-d H:i:s');
            $model_log_retur->tipe          = 'RETUR';
            $model_log_retur->aksi          = 'Tambah';
            $model_log_retur->keterangan    =  $user_name;
            $model_log_retur->created_by    =  $user_login;
            $model_log_retur->transaksi_id  =  $returresep_id;
            $model_log_retur->save();

            $transaction->commit();

            $data_retur = $connection->createCommand("
                SELECT
                    no_returresep
                FROM returresep_t
                WHERE returresep_id = {$returresep_id}
            ")->queryOne();

            return [
                'message' => 'Data Berhasil di simpan',
                'id_retur' => DocoHelpers::encrypt($returresep_id),
                'no_returresep' => $data_retur['no_returresep']
            ];
        } catch (\yii\db\Exception $e) {
            Yii::$app->cache->delete('transaction-retur-validator-' . $pendaftaran_id);
            $transaction->rollBack();
            throw new Exception($e->getMessage());
        } catch (\Exception $e) {
            Yii::$app->cache->delete('transaction-retur-validator-' . $pendaftaran_id);
            $transaction->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function setPayload($data) {
        $this->detailRetur = ArrayHelper::getValue($data, 'data_retur');
        $this->pendaftaran_id = ArrayHelper::getValue($data, 'pendaftaran_id');
        $this->ruangan_retur = ArrayHelper::getValue($data, 'ruangan_retur');
        $this->tanggal_retur = ArrayHelper::getValue($data, 'tanggal_retur');
        $this->returresep_id = ArrayHelper::getValue($data, 'returresep_id');
    }

    public function setPayloadLog($data) {
        $verifikator = Pegawai::find()
            ->select(['pegawai_id', 'nama_pegawai'])
            ->where(['pegawai_id' => Yii::$app->user->identity->pegawai_id])
            ->asArray()->one();

        $this->payloadLog = [
            'tipe' => 'RETUR',
            'aksi' => ArrayHelper::getValue($data, 'type', 'Edit'),
            'alasan' => ArrayHelper::getValue($data, 'alasan'),
            'keterangan' => ArrayHelper::getValue($verifikator, 'nama_pegawai'),
            'transaksi_id' => ArrayHelper::getValue($data, 'returresep_id')
        ];
    }

    public function saveReturPendaftaran() {
        $user_login = Yii::$app->jwt->user->loginpemakai_id;
        $user_name = Yii::$app->jwt->user->nama_pemakai;

        $dataOa = self::getDataObatAlkes($this->pendaftaran_id);
        $dataBmhp = self::getDataBmhp($this->pendaftaran_id);
        $detail_retur_resep = [];
        $tmpDataObatAlkes = [];
        $qtyReturObarAlkes = [];
        $qtyReturBmhp = [];
        $total_retur = 0;
        $tmpObatAlkesId = [];

        // Simpan data sebelum nya buat komparasi.
        foreach ($this->detailRetur as $key => $valueRetur) {
            if (isset($valueRetur['qty_retur'])) {
                $total = $valueRetur['harga_satuan'] * $valueRetur['qty_retur'];
                $total_retur += $total;
                $tmpDataObatAlkes[$valueRetur['obatalkes_id']] = [];

                // Data resep.
                if ($valueRetur['transaksi'] == 'resep') {
                    $qtyReturObarAlkes[$valueRetur['obatalkes_id']] = [
                        'qty_retur' => $valueRetur['qty_retur'],
                        'transaksi' => $valueRetur['transaksi']
                    ];
                }

                // Data bmhp.
                if ($valueRetur['transaksi'] == 'bmhp') {
                    $qtyReturBmhp[$valueRetur['obatalkes_id']] =  [
                        'qty_retur' => $valueRetur['qty_retur'],
                        'transaksi' => $valueRetur['transaksi']
                    ];
                }
            }
        }

        $model_retur = ModelsReturResep::find()->where([
            'returresep_id' => $this->returresep_id,
            'tgl_verif' => NULL
        ])->one();
        $model_retur->ruangan_id = $this->ruangan_retur;
        $model_retur->tgl_retur  = $this->tanggal_retur;
        $model_retur->save();

        // Assign QTY Retur Obat ke setiap data.
        foreach ($dataOa as $key => $valueOa) {
            if (array_key_exists($valueOa['obatalkes_id'], $tmpDataObatAlkes)) {
                if(isset($qtyReturObarAlkes[$valueOa['obatalkes_id']])) {
                    $mergeArray = array_merge($qtyReturObarAlkes[$valueOa['obatalkes_id']], $valueOa);
                    array_push($tmpDataObatAlkes[$valueOa['obatalkes_id']], $mergeArray);
                }
            }
        }
        

        // Assign QTY Retur BMHP ke setiap data.
        foreach ($dataBmhp as $key => $valueBmhp) {
            if (array_key_exists($valueBmhp['obatalkes_id'], $tmpDataObatAlkes)) {
                if(isset($qtyReturBmhp[$valueBmhp['obatalkes_id']])) {
                    $mergeArray = array_merge($qtyReturBmhp[$valueBmhp['obatalkes_id']], $valueBmhp);
                    array_push($tmpDataObatAlkes[$valueBmhp['obatalkes_id']], $mergeArray);
                }
            }
        }

        $tmpObatPenampung = [];
        $bmhpPenampung = [];
        $validatePenampung = [];
        $validateBmhpPenampung = [];
        $totalRetur = 0;
        foreach ($tmpDataObatAlkes as $key => $value) {
            foreach ($value as $keyDetail => $valueDetail) {
                if ($valueDetail['transaksi'] == 'resep') {
                    $_qtyObat = isset($valueDetail['det_konversi']) ? $valueDetail['det_konversi'] : $valueDetail['qty_konversi'];
                    $nilaiKonversi = (int) trim($valueDetail['nilai_konversi'], '"');
                    $qtyRetur = $valueDetail['qty_retur'];
                    $nilaiPengurang = isset($tmpObatPenampung[$valueDetail['obatalkes_id']]) ? $tmpObatPenampung[$valueDetail['obatalkes_id']] : $valueDetail['qty_retur'];

                    if (($_qtyObat - $nilaiPengurang) < 0 && $_qtyObat != 0) {
                        $tmpObatPenampung[$valueDetail['obatalkes_id']] = abs($_qtyObat - $valueDetail['qty_retur']);
                        $qtyRetur = $_qtyObat;
                        $valueDetail['qty_retur'] = abs($_qtyObat - $valueDetail['qty_retur']);
                        $_qtyObat = 0;
                    }

                    if (($_qtyObat - $nilaiPengurang) >= 0 && $_qtyObat != 0) {
                        // Cek kondisi apabila ada data obat yang kedua / obatnya di sum.
                        if (array_key_exists($valueDetail['obatalkes_id'], $tmpObatPenampung)) {
                            $qtyRetur = $tmpObatPenampung[$valueDetail['obatalkes_id']];
                            $_qtyObat -= $nilaiPengurang;
                        } else {
                            if (!in_array($valueDetail['obatalkes_id'], $validatePenampung)) {
                                $qtyRetur = $valueDetail['qty_retur'];
                                $_qtyObat -= $valueDetail['qty_retur'];
                                $validatePenampung[] = $valueDetail['obatalkes_id'];
                            }
                        }
                    }

                    if ($_qtyObat != $valueDetail['qty_awal']) {
                        $detail_retur_resep[] =  [
                            'obatalkespasien_id' => $valueDetail['obatalkespasien_id'],
                            'hargasatuan'        => (int) $valueDetail['hargasatuan_oa'] / $nilaiKonversi,
                            'qty_retur'          => (int) $qtyRetur,
                            'created_by'         => $user_login,
                            'returresep_id'      => $this->returresep_id,
                        ];

                        $totalRetur += ((int) $valueDetail['hargasatuan_oa'] / $nilaiKonversi) * (int) $qtyRetur;
                    }
                }

                if ($valueDetail['transaksi'] == 'bmhp') {
                    $qtyRetur = $valueDetail['qty_retur'];
                    $nilaiPengurang = isset($bmhpPenampung[$valueDetail['obatalkes_id']]) ? $bmhpPenampung[$valueDetail['obatalkes_id']] : $valueDetail['qty_retur'];

                    if (($valueDetail['qty_oa'] - $nilaiPengurang) < 0 && $valueDetail['qty_oa'] != 0) {
                        $bmhpPenampung[$valueDetail['obatalkes_id']] = abs($valueDetail['qty_oa'] - $valueDetail['qty_retur']);
                        $qtyRetur = $valueDetail['qty_oa'];
                        $valueDetail['qty_retur'] = abs($valueDetail['qty_oa'] - $valueDetail['qty_retur']);
                        $valueDetail['qty_oa'] = 0;
                    }

                    if (($valueDetail['qty_oa'] - $nilaiPengurang) >= 0 && $valueDetail['qty_oa'] != 0) {
                        // Cek kondisi apabila ada data obat yang kedua / obatnya di sum.
                        if (array_key_exists($valueDetail['obatalkes_id'], $bmhpPenampung)) {
                            $qtyRetur = $bmhpPenampung[$valueDetail['obatalkes_id']];
                            $valueDetail['qty_oa'] -= $nilaiPengurang;
                        } else {
                            if (!in_array($valueDetail['obatalkes_id'], $validateBmhpPenampung)) {
                                $qtyRetur = $valueDetail['qty_retur'];
                                $valueDetail['qty_oa'] -= $valueDetail['qty_retur'];
                                $validateBmhpPenampung[] = $valueDetail['obatalkes_id'];
                            }
                        }
                    }

                    if ($valueDetail['qty_oa'] != $valueDetail['qty_awal']) {
                        $detail_retur_resep[] =  [
                            'obatalkespasien_id' => $valueDetail['obatalkespasien_id'],
                            'hargasatuan'        => (int) $valueDetail['hargasatuan_oa'],
                            'qty_retur'          => (int) $qtyRetur,
                            'created_by'         => $user_login,
                            'returresep_id'      => $this->returresep_id,
                        ];

                        $totalRetur += ((int) $valueDetail['hargasatuan_oa']) * ((int) $qtyRetur);
                    }
                }
            }
        }


        if(!empty($detail_retur_resep)) {
            $logData = ReturResepDetail::find()->where(['returresep_id' => $this->returresep_id])->asArray()->all();
            ReturResepDetail::deleteAll(['returresep_id' => $this->returresep_id], [], $logData);
            ReturResepDetail::batchInsert($detail_retur_resep); 
            
            $retur = ModelsReturResep::findOne($this->returresep_id);
            $retur->total_retur = $totalRetur;
            if(!$retur->save()) throw new \Exception("Gagal update total retur.", 1);

            DocoHelpers::saveLogActivity($this->payloadLog);
        }

        return $this->returresep_id;
    }

    /**
     * Function validasi return no pendaftaran.
     * 
     * @author Maulana Muhammad Rizky.
     * @param integer $pendaftaran_id
     * @return array
     */
    public static function validasiReturPendaftaran($pendaftaran_id)
    {
        $validasiRetur = ModelsReturResep::find()->where([
            'pendaftaran_id' => $pendaftaran_id,
            'tgl_verif' => NULL
        ])->asArray()->one();

        if(! empty($validasiRetur)) {
            return $validasiRetur;
        }

        return [];
    }

    /**
     * Function save ke resepturdetail_t
     * 
     * @author Maulana Muhammad Rizky.
     * @return array
     */
    public function createResepturDetail()
    {
        $user_login = Yii::$app->jwt->user->loginpemakai_id;
    }

    private static function getDataObatAlkes($pendaftaran_id)
    {
        $connection = Yii::$app->db;
        $statusBelumBayar = DocoConstants::BELUM_LUNAS;
        $statusDiserahkan = DocoConstants::RESEPTUR_DISERAHKAN;

        return $connection->createCommand("
            SELECT 
                penjualan.penjualanresep_id, 
                penjualan.pendaftaran_id,
                penjualan.noresep,
                penjualan.ruangan_id,
                penjualan.tgl_menyerahkan ,
                obat.obatalkespasien_id, 
                obat.obatalkes_id,
                obat.hargasatuan_oa,
                obat.qty_konversi::int as qty_awal,
                obat.qty_konversi::int,
                obat.det_konversi::int,
                obat.qty_oa::int,
                obat.additional_data::json->'nilai_konversi'::text as nilai_konversi
            FROM penjualanresep_t as penjualan 
            JOIN obatalkespasien_t as obat on penjualan.penjualanresep_id = obat.penjualanresep_id 
            WHERE penjualan.pendaftaran_id = {$pendaftaran_id}
            AND penjualan.status_bayar = {$statusBelumBayar}
            AND penjualan.status_reseptur = {$statusDiserahkan}
            AND obat.racikan_id = 2
            AND obat.is_deleted = FALSE
            AND obat.is_active = TRUE
            ORDER BY tgl_menyerahkan DESC
        ")
            ->queryAll();
    }

    private static function getDataBmhp($pendaftaran_id)
    {
        $connection = Yii::$app->db;
        return $connection->createCommand("
            SELECT 
                obat.obatalkespasien_id, 
                obat.obatalkes_id,
                obat.hargasatuan_oa,
                obat.qty_oa::int as qty_awal,
                obat.qty_konversi::int,
                obat.qty_oa::int,
                obat.status_bmhp
            FROM obatalkespasien_t obat
            WHERE penjualanresep_id is null
            AND pendaftaran_id = {$pendaftaran_id}
            AND is_deleted = FALSE
            AND is_active = TRUE
            AND (obat.ruangan_id IN ( SELECT ruangan_m.ruangan_id
                   FROM ruangan_m
                  WHERE ruangan_m.instalasi_id <> (( SELECT lookuptransaksi_m.kode_id
                           FROM lookuptransaksi_m
                          WHERE lookuptransaksi_m.kode_transaksi::text = 'instalasi_bedah'::text))))
            ORDER BY status_bmhp ASC
        ")->queryAll();
    }

    /**
     * Function ini untuk melakukan validasi ketika transaksi.
     * 
     * @author Maulana Muhammad Rizky.
     * @return bool
     */
    public static function transcationValidator($pendaftaran_id)
    {
        $cache = Yii::$app->cache;
        if (is_null($pendaftaran_id)) {
            return false;
        }
        $cacheName = 'transaction-retur-validator-' . $pendaftaran_id;


        if (!empty($cache->get($cacheName))) {
            return true;
        } else {
            $cache->set($cacheName, 1, 55);
        }

        return false;
    }

    /**
     * Function ini untuk melakukan validasi untuk injeksi lewat URI.
     * 
     * @author Maulana Muhammad Rizky.ReturResep
     * @return bool
     */
    public static function validasiInjection($pendaftaran_id)
    {
        $returData = ModelsReturResep::find()->where([
            'pendaftaran_id' => $pendaftaran_id,
            'tgl_verif' => NULL
        ])
            ->asArray()->one();

        if (!empty($returData)) {
            return $returData;
        }

        return [];
    }

    public function verifikasi() {
        $oaPasien = ObatAlkesPasien::find(true)
            ->select([
                'returdetail.returresepdetail_id',
                'obatalkespasien_t.obatalkespasien_id',
                'obatalkespasien_t.obatalkes_id',
                'obat.obatalkes_nama',
                new Expression("
                CASE 
                    WHEN obatalkespasien_t.penjualanresep_id IS NULL THEN qty_oa
                    ELSE COALESCE(det_konversi, qty_konversi)
                END AS qty_resep
                "),
                'obatalkespasien_t.hargajual_oa',
                'returdetail.qty_retur',
                'returdetail.hargasatuan',
                'obatalkespasien_t.hargasatuan_oa',
                'retur.ruangan_id',
                'obatalkespasien_t.is_deleted',
                'obatalkespasien_t.status_bmhp',
                'obatalkespasien_t.pembayaran_id',
                'obatalkespasien_t.rke',
                'pembayaran.is_deleted as is_batal_bayar',
                'pembayaran.no_pembayaran',
                'resep.noresep',
                'resep.reseptur_id',
                'obatalkespasien_t.additional_data',
                new Expression("
                CASE 
                    WHEN obatalkespasien_t.det IS NOT NULL THEN TRUE
                    ELSE FALSE
                END AS is_edited,
                obatalkespasien_t.penjualanresep_id
                "),
            ])
            ->leftJoin('(select returresepdetail_id, returresep_id, obatalkespasien_id, qty_retur, hargasatuan from returresepdetail_t) returdetail', 'returdetail.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id')
            ->leftJoin('(select returresep_id, ruangan_id from returresep_t) retur', 'retur.returresep_id = returdetail.returresep_id')
            ->leftJoin('(select obatalkes_id, obatalkes_nama from obatalkes_m ) obat', 'obat.obatalkes_id = obatalkespasien_t.obatalkes_id')
            ->leftJoin('(select pembayaran_id, no_pembayaran, is_deleted from pembayaran_t) pembayaran', 'pembayaran.pembayaran_id = obatalkespasien_t.pembayaran_id')
            ->leftJoin('(select penjualanresep_id, reseptur_id, noresep from penjualanresep_t) resep', 'resep.penjualanresep_id = obatalkespasien_t.penjualanresep_id')
            ->where(['retur.returresep_id' => $this->returresep_id])
            ->asArray()->all();
        $oaPasien = ArrayHelper::index($oaPasien, 'obatalkespasien_id');
        $resepturIds = array_column($oaPasien, 'reseptur_id');
        $noReseps = array_column($oaPasien, 'noresep');

        // validasi tidak bisa verif jika terdapat transaksi yang sudah dibayar
        foreach($oaPasien as $value) {
            $message = "";
            if(!empty($value['pembayaran_id']) && !$value['is_batal_bayar']) {
                $message = "Tagihan dengan nomor pembayaran " . $value['no_pembayaran'] . " sudah dibayar. Silakan lakukan batal bayar terlebih dahulu.";
                throw new \Exception($message, 1);
            }
        }
        
        // update qty dan tagihan pasien
        (new BatchUpdate(ObatAlkesPasien::tableName(), function($query) use ($oaPasien) {
            $listKey = $setPayload = [];
            foreach ($oaPasien as $key => $value) {
                if(!empty($value['noresep'])) { // update resep
                    $additional_data = json_decode($value['additional_data'], true);
                    $setPayload = [
                        'qty_oa' => (float) ($value['qty_resep'] - $value['qty_retur']) / $additional_data['nilai_konversi'],
                        'qty_konversi' => $value['qty_resep'] - $value['qty_retur'],
                        'qty_medis' => (float) ($value['qty_resep'] - $value['qty_retur']) / $additional_data['nilai_konversi'],
                        'det' => $query->setFloatValue($value['is_edited'], ($value['qty_resep'] - $value['qty_retur']) / $additional_data['nilai_konversi']),
                        'det_konversi' => $query->setIntValue($value['is_edited'], $value['qty_resep'] - $value['qty_retur']),
                        'det_medis' => $query->setFloatValue($value['is_edited'], ($value['qty_resep'] - $value['qty_retur']) / $additional_data['nilai_konversi']),
                        'hargajual_oa' => $value['hargajual_oa'] - ($value['qty_retur'] * $value['hargasatuan_oa']),
                        'is_deleted' => ($value['qty_resep'] - $value['qty_retur']) == 0 ? 'true' : 'false',
                        'is_retur' => 'true'
                    ];
                } else {
                    $setPayload = [
                        'qty_oa' => $value['qty_resep'] - $value['qty_retur'],
                        'hargajual_oa' => $value['hargajual_oa'] - ($value['qty_retur'] * $value['hargasatuan_oa']),
                        'is_deleted' => ($value['qty_resep'] - $value['qty_retur']) == 0 ? 'true' : 'false'
                    ];
                }
                
                $query->set($setPayload, "obatalkespasien_id = {$key}", $key);
                array_push($listKey, $key);
            }
            if(count($listKey) > 0){
                $id = implode(", ", $listKey);
                $query->where("obatalkespasien_id in({$id})");
            }
        }
        ))->execute();

        // update detail resep untuk resep dari pelayanan
        $resepturDetail = ResepturDetail::find()->where(['IN', 'reseptur_id', $resepturIds])->asArray()->all();
        $resepturDetail = ArrayHelper::index($resepturDetail, function($col) {
            return $col['obatalkes_id']."-".$col['rke'];
        }, 'reseptur_id');
        (new BatchUpdate(ResepturDetail::tableName(), function($query) use ($resepturDetail, $oaPasien) {
            $listKey = [];
            foreach ($oaPasien as $key => $value) {
                if(!empty($value['reseptur_id'])) {
                    $key_reseptur_detail = $value['obatalkes_id'] . "-" . $value['rke'];
                    $detail_reseptur = $resepturDetail[$value['reseptur_id']][$key_reseptur_detail];

                    $additional_data = json_decode($value['additional_data'], true);
                    $query->set([
                        'qty_reseptur' => (float) ($value['qty_resep'] - $value['qty_retur']) / $additional_data['nilai_konversi'],
                        'qty_konversi' => $value['qty_resep'] - $value['qty_retur'],
                        'qty_medis' => (float) ($value['qty_resep'] - $value['qty_retur']) / $additional_data['nilai_konversi'],
                        'det' => $query->setFloatValue($value['is_edited'], ($value['qty_resep'] - $value['qty_retur']) / $additional_data['nilai_konversi']),
                        'det_konversi' => $query->setIntValue($value['is_edited'], $value['qty_resep'] - $value['qty_retur']),
                        'det_medis' => $query->setFloatValue($value['is_edited'], ($value['qty_resep'] - $value['qty_retur']) / $additional_data['nilai_konversi']),
                        'hargajual_reseptur' => $value['hargajual_oa'] - ($value['qty_retur'] * $value['hargasatuan_oa']),
                        'is_retur' => 'true'
                    ], "resepturdetail_id = {$detail_reseptur['resepturdetail_id']}", $detail_reseptur['resepturdetail_id']);
                    array_push($listKey, $detail_reseptur['resepturdetail_id']);
                }
            }
            if(count($listKey) > 0){
                $id = implode(", ", $listKey);
                $query->where("resepturdetail_id in({$id})");
            }
        }
        ))->execute();

        $penjualanResep = PenjualanResep::find()
            ->select(['penjualanresep_id', 'noresep', 'reseptur_id'])
            ->where(['IN', 'penjualanresep_id', array_column($oaPasien, 'penjualanresep_id')])
            ->asArray()->all();
        $checkOaPasien = ObatAlkesPasien::find(true)
            ->select(['obatalkespasien_id', 'penjualanresep_id', 'qty_oa', 'qty_konversi', 'is_deleted', 'is_active'])
            ->where(['IN', 'penjualanresep_id', array_column($penjualanResep, 'penjualanresep_id')])
            ->asArray()->all();
        $returOaPasien = ArrayHelper::index($oaPasien, 'obatalkespasien_id', 'penjualanresep_id');
        $checkOaPasien = ArrayHelper::index($checkOaPasien, 'obatalkespasien_id', 'penjualanresep_id');
        $penjualanresepIdDelete = [];
        foreach($checkOaPasien as $penjualanresepId => $oapasien) {
            if(count($returOaPasien[$penjualanresepId]) == count($checkOaPasien[$penjualanresepId])) {
                foreach($oaPasien as $data) {
                    if(isset($checkOaPasien[$penjualanresepId][$data['obatalkespasien_id']]) && $checkOaPasien[$penjualanresepId][$data['obatalkespasien_id']]['qty_oa'] == 0) {
                        array_push($penjualanresepIdDelete, $penjualanresepId);
                    }
                }
            }
        }

        PenjualanResep::updateAll(['is_deleted' => true, 'is_active' => false], ['IN', 'penjualanresep_id', $penjualanresepIdDelete]);
        Reseptur::updateAll(['is_deleted' => true, 'is_active' => false], ['IN', 'penjualanresep_id', $penjualanresepIdDelete]);

        // simpan qty_pemberian_akhir
        (new BatchUpdate(ReturResepDetail::tableName(), function($query) use ($oaPasien) {
            $returDetail = ReturResepDetail::find()->where(['returresep_id' => $this->returresep_id])->asArray()->all();
            $returDetail = ArrayHelper::index($returDetail, 'obatalkespasien_id');
            $listKey = [];
            foreach ($returDetail as $key => $value) {
                $qty_resep = $oaPasien[$key]['qty_resep'];

                $query->set([
                    'qty_pemberian_akhir' => $qty_resep
                ], "returresepdetail_id = {$value['returresepdetail_id']}", $value['returresepdetail_id']);
                array_push($listKey, $value['returresepdetail_id']);
            }
            
            if(count($listKey) > 0){
                $id = implode(", ", $listKey);
                $query->where("returresepdetail_id in({$id})");
            }
        }
        ))->execute();

        // remove BMHP belum approve agar tidak potong stok
        $oaPasien = array_filter($oaPasien, function($obj){
            if ($obj['status_bmhp'] == DocoConstants::BMHP_BELUM_VERIFIKASI) return false; 
            return true;
        });
        
        // potong stok
        if(count($oaPasien) > 0) {
            StokObatAlkes::returnObatPasienPartial($oaPasien);
        }

        // update status dan verifikator retur
        $retur = ModelsReturResep::findOne($this->returresep_id);
        $retur->status_retur = DocoConstants::RETUR_VERIFIKASI;
        $retur->tgl_verif = date('Y-m-d H:i:s');
        $retur->user_verifikator = Yii::$app->user->identity->pegawai_id;
        $retur->save();

        // simpan log activity
        $verifikator = Pegawai::find()
            ->select(['pegawai_id', 'nama_pegawai'])
            ->where(['pegawai_id' => Yii::$app->user->identity->pegawai_id])
            ->asArray()->one();
        $logActivity = [
            'tipe' => 'RETUR',
            'aksi' => 'Verifikasi',
            'keterangan' => $verifikator['nama_pegawai'],
            'transaksi_id' => $this->returresep_id,
            'additional_detail' => json_encode($oaPasien)
        ];
        DocoHelpers::saveLogActivity($logActivity);
    }
}
