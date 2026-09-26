<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use SirsCore\features\IntegrasiAkunting;
use SirsCore\businessLogic\StokObatAlkes as BLStokObatAlkes;
use app\modules\v1\models\AdjusmenObat;
use app\modules\v1\models\AdjusmenObatKeluar;
use app\modules\v1\models\AdjusmenObatMasuk;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\SatuanUnit;
use app\modules\v1\models\StokObatAlkes;

class MigrasiAdjustmentController extends DocoActiveController {
    public $modelClass = '';

    private $kode_obat = [];
    private $nama_obat = [];
    private $satuan_unit = [];
    private $data_konversi = [];
    private $data_valid = [];
    private $stok_obat = [];
    private $error_dump = [];

    public function init()
    {
        $this->getObatData();
    }

    public function verbs() {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions() {
        return [
            'template-adjustment-oa' => 'app\modules\v1\actions\MigrasiAdjustment\TemplateAdjustmentOaAction'
        ];
    }

    public function getObatData()
    {
        try {
            $obatalkes = ObatAlkes::find()->select("obatalkes_id , obatalkes_kode, obatalkes_nama")->all();
            $kode_obat = $nama_obat = [];
            foreach ($obatalkes as $obat) {
                $id = $obat['obatalkes_id'];
                $nama_obat[$id] = trim(strtoupper($obat['obatalkes_nama']));
                $kode_obat[$id] = trim(strtoupper($obat['obatalkes_kode']));
            }

            $satuan_unit = $data_konversi = [];
            $satuan_data = Yii::$app->db->createCommand("
                select
                konversi.satuankonversi_id, konversi.satuanbesar_id, konversi.satuankecil_id, konversi.obatalkes_id, konversi.nilai_konversi ,
                master.satuanunit_nama
                from satuankonversi_m konversi
                join satuanunit_m master on konversi.satuanbesar_id = master.satuanunit_id
            ")->queryAll();

            foreach ($satuan_data as $satuan) {
                $satuan_nama = trim(strtoupper($satuan["satuanunit_nama"]));

                // untuk kebutuhan dapetin id satuan
                $satuan_id = $satuan["satuanbesar_id"];
                $satuan_unit[$satuan_id] = $satuan_nama;

                // untuk simpan data konversi
                $idenfier = $satuan["satuanbesar_id"]. "-" .$satuan["obatalkes_id"];
                $temp = [
                    "satuankonversi_id" => $satuan["satuankonversi_id"],
                    "satuanbesar_id" => $satuan["satuanbesar_id"],
                    "satuankecil_id" => $satuan["satuankecil_id"],
                    "obatalkes_id" => $satuan["obatalkes_id"],
                    "nilai_konversi" => $satuan["nilai_konversi"],
                    "satuanunit_nama" => $satuan_nama,
                ];
                $data_konversi[$idenfier] = $temp;
            }

            $ruangan_id = $_POST['ruangan_id'];
            $stok_obat = [];
            $data_stok_obat = Yii::$app->db->createCommand("
                select
                st.obatalkes_id as id, sum(st.qtystok_in - st.qtystok_out ) as sisa
                from stokobatalkes_t st
                where ruangan_id = {$ruangan_id}
                group by st.obatalkes_id
            ")->queryAll();

            foreach ($data_stok_obat as $row_stok) {
                $stok_obat[$row_stok['id']] = $row_stok['sisa'];
            }

            $this->kode_obat = $kode_obat;
            $this->nama_obat = $nama_obat;
            $this->satuan_unit = $satuan_unit;
            $this->data_konversi = $data_konversi;
            $this->stok_obat = $stok_obat;
            return true;
        } catch (\Exception $e) {
            $this->error_dump['get_obat_data'] = "failed";
            return $e->getMessage();
        }
    }

    public function actionGetRuangan()
    {
        try {
            $satuan_data = Yii::$app->db->createCommand("
                select * from ruangan_v rv
                order by instalasi_nama asc
            ")->queryAll();
        } catch (\Exception $e) {
            $satuan_data = [];
        }

        return $satuan_data;
    }

    public function actionSaveImport()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $data_import = json_decode($post['attachment'], true);
        $data_valid = $this->sanitasiDataImport($data_import);
        $save = [
            'text' => 'Perhatian',
            'title' => 'Tidak ada Data Valid',
            'no_adjusmen' => null,
            'id_adjustment' => null,
            'stok_habis' => "",
        ];

        if (count($data_valid) > 0) {
            $save = $this->importAdjustment($post, $data_valid);
        }
        $response = $save;
        $response["all_valid"] = count($this->error_dump) == 0;
        $response["invalid_data"] = $this->error_dump;
        return DocoHelpers::response($response);
    }

    public function sanitasiDataImport($data)
    {
        foreach ($data as $row) {
            $validasi = $this->validasiDataImport($row);
            // return $validasi;
            if ($validasi) {
                $this->data_valid[] = $row;
            } else {
                $this->error_dump['data'][] = $row;
            }
        }
        return $this->data_valid;
    }

    public function validasiDataImport($data)
    {
        $columns = $this->checkColumns($data);
        $kode = $this->checkKodeObat($data);
        $expired = $this->checkTanggalKadaluarsa($data);
        $satuan = $this->checkSatuan($data);
        $qty = $this->checkQty($data);
        $harga = $this->checkHargaNetto($data);
        if (isset($_POST['jenis_adjusmen']) && $_POST['jenis_adjusmen'] == 'keluar') {
            $stok = $this->checkStock($data);
        } else {
            $stok = true;
        }
        if ($columns && $kode &&
            $expired && $satuan &&
            $qty && $harga && $stok) {
            return true;
        }
        return false;
    }

    public function checkColumns($data)
    {
        $must_have_columns = [
            'obatalkes_kode',
            'tglkadaluarsa',
            'satuan_nama',
            'qty',
            'harganetto',
        ];

        foreach ($must_have_columns as $columns) {
            if (!array_key_exists($columns, $data)) {
                return false;
            }
        }
        return true;
    }

    public function checkKodeObat($data)
    {
        $item = trim(strtoupper($data['obatalkes_kode']));
        if (empty($item) || !in_array($item, $this->kode_obat)) {
            return false;
        }
        return true;
    }

    public function checkSatuan($data)
    {
        $kode = $data['obatalkes_kode'];
        $satuan = $data['satuan_nama'];
        $obatalkes_id = reset(array_keys($this->kode_obat, trim(strtoupper($kode))));
        $satuan_besar_id = reset(array_keys($this->satuan_unit, trim(strtoupper($satuan))));
        $identifier_konversi = $satuan_besar_id. "-" .$obatalkes_id;

        if (!array_key_exists($identifier_konversi, $this->data_konversi)) {
            return false;
        }
        return true;
    }

    public function checkTanggalKadaluarsa($data)
    {
        $item = $data['tglkadaluarsa'];
        if (empty($item) || date('Y-m-d', strtotime($item)) == "1970-01-01") {
            return false;
        }
        return true;
    }

    public function checkQty($data)
    {
        $item = trim(strtoupper($data['qty']));
        if (empty($item) || !is_numeric($item)) {
            return false;
        }
        return true;
    }

    public function checkHargaNetto($data)
    {
        $item = trim(strtoupper($data['harganetto']));
        if (!is_numeric($item) || $item < 0) {
            return false;
        }
        return true;
    }

    public function checkStock($data)
    {
        $kode = $data['obatalkes_kode'];
        $satuan = $data['satuan_nama'];
        $qty = $data['qty'];

        $obatalkes_id = reset(array_keys($this->kode_obat, trim(strtoupper($kode))));
        $satuan_besar_id = reset(array_keys($this->satuan_unit, trim(strtoupper($satuan))));
        $identifier_konversi = $satuan_besar_id. "-" .$obatalkes_id;

        if (!array_key_exists($identifier_konversi, $this->data_konversi)) {
            return false;
        }

        $selected_konversi = $this->data_konversi[$identifier_konversi];
        $nilai_konversi = $selected_konversi['nilai_konversi'];
        $qty_konversi = ((float) $qty) * ((float) $nilai_konversi);

        $stok_tersedia = array_key_exists($obatalkes_id, $this->stok_obat) ?
            $this->stok_obat[$obatalkes_id] :
            0;

        return $stok_tersedia >= $qty_konversi;

    }

    public function importAdjustment($post, $data)
    {
        try {
            $connection = Yii::$app->db;

            $model_adjustmen = new AdjusmenObat;
            $model_adjustmen->attributes = $post;
            $model_adjustmen->jenis_adjusmen = $post['jenis_adjusmen'] == 'masuk' ? 0 : 1;
            $model_adjustmen->tgl_adjusmen = date('Y-m-d', strtotime($post['tgl_adjusmen']));

            $transaction = $connection->beginTransaction();
            $stok_habis = [];

            if ($model_adjustmen->validate() && $model_adjustmen->save()) {
                if (count($data) > 0) {
                    foreach ($data as $row) {
                        $obatalkes_kode = strtoupper(trim($row['obatalkes_kode']));
                        $satuan_besar_nama = strtoupper(trim($row['satuan_nama']));

                        $obatalkes_id = reset(array_keys($this->kode_obat, trim(strtoupper($obatalkes_kode))));
                        $satuan_besar_id = reset(array_keys($this->satuan_unit, trim(strtoupper($satuan_besar_nama))));
                        if ($obatalkes_id == false || $satuan_besar_id == false) {
                            return [
                                'msg' => 'proses foreach adjustment',
                                'is_success' => false,
                                'data' => $row
                            ];
                        }
                        $tgl_kadaluarsa = date('Y-m-d', strtotime($row['tglkadaluarsa']));
                        $identifier_konversi = $satuan_besar_id. "-" .$obatalkes_id;
                        $selected_konversi = $this->data_konversi[$identifier_konversi];

                        $nilai_konversi = $selected_konversi['nilai_konversi'];

                        $qty_konversi = ((float) $row['qty']) * $nilai_konversi;
                        $harga_satuan_kecil = ((float) $row['harganetto']) / $qty_konversi;

                        if ($model_adjustmen->jenis_adjusmen == 0) {
                            $data_adjustmen[] = [
                                'adjusmenobat_id'   => $model_adjustmen->adjusmenobat_id,
                                'obatalkes_id'      => $obatalkes_id,
                                'tgl_kadaluarsa'    => $tgl_kadaluarsa,
                                'qty'               => (float) $row['qty'],
                                'satuankecil_id'    => $selected_konversi['satuankecil_id'],
                                'satuanbesar_id'    => $satuan_besar_id,
                                'harga_netto'       => (float) $row['harganetto'],
                                'satuankonversi_id' => $selected_konversi['satuankonversi_id'],
                                'qty_konversi'      => $qty_konversi,
                                'no_batch'          => "",
                                'keterangan'        => "",
                            ];
                        } else {
                            $data_adjustmen[] = [
                                'adjusmenobat_id'   => $model_adjustmen->adjusmenobat_id,
                                'alasan'            => "",
                                'obatalkes_id'      => $obatalkes_id,
                                'qty'               => (float) $row['qty'],
                                'qty_konversi'      => $qty_konversi,
                                'satuanbesar_id'    => $satuan_besar_id,
                                'satuankecil_id'    => $selected_konversi['satuankecil_id'],
                                'satuankonversi_id' => $selected_konversi['satuankonversi_id'],
                            ];
                        }
                    }

                    if ($model_adjustmen->jenis_adjusmen == 0) {
                        AdjusmenObatMasuk::batchInsert($data_adjustmen);

                        $data_adjustmen = $connection
                            ->createCommand("SELECT * FROM adjusmenobatmasuk_t WHERE adjusmenobat_id = {$model_adjustmen->adjusmenobat_id}")->queryAll();

                        $data_kartu_stok = [];
                        foreach ($data_adjustmen as $row_adj) {
                            $data_kartu_stok[] = [
                                'adjusmenobatmasuk_id'  => $row_adj['adjusmenobatmasuk_id'],
                                'harganetto'            => $row_adj['harga_netto'] ,
                                'obatalkes_id'          => $row_adj['obatalkes_id'] ,
                                'qtystok_in'            => $row_adj['qty_konversi'] ,
                                'ruangan_id'            => $model_adjustmen->ruangan_adjusmen_id,
                                'satuankecil_id'        => $row_adj['satuankecil_id'] ,
                                'stokoa_aktif'          => true,
                                'tglkadaluarsa'         => $row_adj['tgl_kadaluarsa'] ,
                                'tglstok_in'            => $model_adjustmen->tgl_adjusmen,
                            ];
                        }
                        StokObatAlkes::batchInsert($data_kartu_stok);
                    } else {
                        AdjusmenObatKeluar::batchInsert($data_adjustmen);
                        $data_adjustmen = $connection
                            ->createCommand("SELECT * FROM adjusmenobatkeluar_t WHERE adjusmenobat_id = {$model_adjustmen->adjusmenobat_id}")->queryAll();

                        $data_kartu_stok = [];
                        foreach ($data_adjustmen as $row_adj) {
                            $data_kartu_stok[] = [
                                'adjusmenobatkeluar_id' => $row_adj['adjusmenobatkeluar_id'],
                                'obatalkes_id'          => $row_adj['obatalkes_id'],
                                'qtystok_out'           => $row_adj['qty_konversi'],
                                'ruangan_id'            => $model_adjustmen->ruangan_adjusmen_id,
                                'satuankecil_id'        => $row_adj['satuankecil_id'],
                                'qty_satuanpakai'       => $row_adj['qty_konversi'],
                            ];
                        }

                        $tanggalBerlaku = date('Y-m-d');
                        $konfig = $connection->createCommand("
                            SELECT metodeantrian FROM konfigfarmasi_k
                            WHERE tglberlaku >= '{$tanggalBerlaku}'
                            AND konfigfarmasi_aktif = true
                            AND is_active = true
                        ")->queryOne();

                        $currentMetode = BLStokObatAlkes::FEFO;
                        if ($konfig) {
                            $currentMetode = isset($konfig['metodeantrian'])
                               ? strtoupper($konfig['metodeantrian']) : BLStokObatAlkes::FEFO;
                        }

                        // Execute By Condition
                        if ($currentMetode === BLStokObatAlkes::FEFO) {
                            $methode = BLStokObatAlkes::methodeFEFO($data_kartu_stok, $model_adjustmen->tgl_adjusmen);
                        } else {
                            $methode = BLStokObatAlkes::methodeFIFO($data_kartu_stok, $model_adjustmen->tgl_adjusmen);
                        }

                        if ($methode !== true) {
                            $stok_habis = $methode;
                        }
                    }

                    $transaction->commit();

                    $adjusmen = AdjusmenObat::find()
                        ->select(['MAX(adjusmenobat_id)'])
                        ->scalar();

                    $adjusmen = AdjusmenObat::find()
                        ->select(['no_adjusmen', "adjusmenobat_id"])
                        ->where(['adjusmenobat_id' => $adjusmen])
                        ->one();

                    IntegrasiAkunting::integratePenerimaanSupplier($adjusmen->no_adjusmen, DocoConstants::ADJ.DocoConstants::JENIS_OBAT);

                    $response = [
                        'text' => 'Adjustment Obat Alkes berhasil disimpan',
                        'title' => 'Proses berhasil !',
                        'no_adjusmen' => $adjusmen->no_adjusmen,
                        'id_adjustment' => DocoHelpers::encrypt($adjusmen->adjusmenobat_id),
                        'stok_habis' => count($stok_habis) > 0 ? "Terdapat ".count($stok_habis)." obat yang stoknya habis" : "",
                    ];

                    return $response;
                }
            } else {
                return [
                    'data' => $model_adjustmen->errors,
                    'status' => 422
                ];
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}