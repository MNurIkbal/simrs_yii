<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-06-21 14:12:02
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-11-26 10:44:10
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\PemeriksaanFisik;
use app\modules\v1\models\AsesmenMedis;
use app\modules\v1\models\PeriksaTubuh;
use app\modules\v1\models\InfoPasienRanap;
use app\modules\v1\models\MetodeGcs;
use app\modules\v1\models\InfoKunjunganRi;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\InfoPasienPenunjangView;
use app\modules\v1\models\ResumeMedisRIT;
use app\modules\v1\models\RiwayatAsesmenMedisView;
use app\modules\v1\models\AsesmenAwal;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\PasienAdmisi;
use SirsCore\businessLogic\MonitoringTtvLogic;

class AsesmenMedisController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\AsesmenMedis';

    // Get pemeriksaan fisik
    public function actionGetAsesmen($pendaftaranId)
    {
        $anatomi = [];
        $asesmen = $this->getAsesmen($pendaftaranId);
        if ($asesmen) {
            $anatomi = $this->getAnatomi($asesmen['asesmenmedis_id']);
        }
        return [
            'anatomi' => $anatomi,
            'asesmen' => $asesmen,
        ];
    }
    public function getAsesmen($pendaftaran_id)
    {
        $data = AsesmenMedis::find()->where(['pendaftaran_id' => $pendaftaran_id])
            ->asArray()->one();
        if ($data) {
            if ($data['tinggi_badan'] == NULL || $data['berat_badan'] == NULL) {
                $data_askep = AsesmenAwal::find()->Select([
                    'additional_data'
                ])->andWhere([
                    'pendaftaran_id' => $pendaftaran_id
                ])
                    ->asArray()->one();

                if(!empty($data_askep)){
                    $body_askep = json_decode($data_askep['additional_data'], true);
                    $data['tinggi_badan'] = @$body_askep['tinggi_badan'];
                    $data['berat_badan'] = @$body_askep['berat_badan'];
                    $data['bb_ideal'] = @$body_askep['bb_ideal'];
                    $data['imt'] = @$body_askep['imt'];
                    $data['ket_imt'] = @$body_askep['ket_imt'];
                }
            }
            $data['text_diagnosa_id'] = null;

            $getDiag = json_decode($data['diagnosa_id'], true);

            if (isset($getDiag['text'])) {
                // $diagnosa = DiagnosaView::find()
                // ->andWhere(['diagnosa_id'=>$data['diagnosa_id']])->one();
                // $data['text_diagnosa_id'] = $diagnosa->diagnosa_kode . ' - ' . $diagnosa->diagnosa_nama;
                $data['text_diagnosa_id'] = $getDiag['text'];
            }
            return $data;
        }
        return [];
    }
    public function actionGetRiwayat($pasienId, $pendaftaran_id)
    {
        // Try catch
        try {
            // Query
            $query = (new \yii\db\Query())
                ->select([
                    RiwayatAsesmenMedisView::tableName() . '.r_penyakitkeluarga',
                    RiwayatAsesmenMedisView::tableName() . '.r_imunisasi',
                    RiwayatAsesmenMedisView::tableName() . '.r_penyakitdahulu',
                    RiwayatAsesmenMedisView::tableName() . '.r_kelahiran',
                    RiwayatAsesmenMedisView::tableName() . '.r_alergiobat',
                ])
                ->from(RiwayatAsesmenMedisView::tableName())
                ->where([RiwayatAsesmenMedisView::tableName() . '.pasien_id' => $pasienId])
                ->andWhere(['!=', RiwayatAsesmenMedisView::tableName() . '.pendaftaran_id', $pendaftaran_id])
                ->orderBy([RiwayatAsesmenMedisView::tableName() . '.asesmenmedis_id' => SORT_DESC])
                ->all();

            // Return
            return $query;
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    public function getAnatomi($asesmenmedis_id)
    {
        try {
            $anatomiTubuh = PeriksaTubuh::find()->select([
                'periksatubuh_t.bagiantubuh_id',
                'periksatubuh_t.bagiantubuhdetail_id',
                'periksatubuh_t.catatan_tubuh',
                'periksatubuh_t.koordinat_x',
                'periksatubuh_t.koordinat_y',
                'periksatubuh_t.counters',
                'periksatubuh_t.created_date',
                'bagian' => 'bagiantubuh_m.namabagtubuh',
                'bagianDetail' => 'bagiantubuhdetail_m.nama_bagiantubuh',
                'periksatubuh_t.jenis_gambar_id',
                'periksatubuh_t.jenis_gambar_nama',
                'periksatubuh_t.berat_luka_bakar',
            ])->joinWith(
                [
                    'bagianTubuh' => function ($query) {
                        $query->select([
                            'bagiantubuh_m.bagiantubuh_id'
                        ]);
                    },
                    'bagianTubuhDetail' => function ($query) {
                        $query->select([
                            'bagiantubuhdetail_m.bagiantubuhdetail_id'
                        ]);
                    },
                ]
            )->where([
                'asesmenmedis_id' => $asesmenmedis_id
            ])->orderBy(['counters' => SORT_ASC])->asArray()->all();
            $result = $anatomiTubuh;
        } catch (\Exception $e) {
            $result = ['message' => $e->getMessage()];
        }
        return $result;
    }
    public function actionSaveAsesmen()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $asesmen = $post['AsesmenMedisForm'];
        $anatomi = $post['anatomi'];
        $result = [];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = new AsesmenMedis();
            if (!empty($asesmen['asesmenmedis_id'])) {
                $model = AsesmenMedis::findOne($asesmen['asesmenmedis_id']);
            }
            $model->attributes = $asesmen;

            // $tmpDiagJson = json_decode($model->diagnosa_id, true);
            // if (!isset($tmpDiagJson['text'])) {
            if (isset($model->diagnosa_id)) {
                $valDiag = [];
                $splitVal = explode('_', $model->diagnosa_id);
                if (count($splitVal) > 1) {
                    $valDiag['id'] = $splitVal[0];
                    $splitTxt = explode('-', $splitVal[1]);
                    if (count($splitTxt) > 1) {
                        $valDiag['kode'] = $splitTxt[0];
                        $valDiag['nama'] = $splitTxt[1];
                    }
                    $valDiag['text'] = $splitVal[1];
                } else {
                    $valDiag['text'] = $model->diagnosa_id;
                }
                $model->diagnosa_id = ($valDiag);
            }
            // }else {
            //     $model->diagnosa_id = $model->diagnosa_id;
            // }

            // $tmpPenyakitKeluargaJson = json_decode($model->r_penyakitkeluarga, true);
            // if (!isset($tmpPenyakitKeluargaJson['text'])) {
            if (isset($model->r_penyakitkeluarga) && is_array($model->r_penyakitkeluarga)) {
                $valuePenyakitKel = [];
                foreach ($model->r_penyakitkeluarga as $keyPenyakitKel) {
                    $valPenyakitKel = [];
                    $splitPenyakitKel = explode('_', $keyPenyakitKel);
                    if (count($splitPenyakitKel) > 1) {
                        $valPenyakitKel['id'] = $splitPenyakitKel[0];
                        $splitTxtKel = explode('-', $splitPenyakitKel[1]);
                        if (count($splitTxtKel) > 1) {
                            $valPenyakitKel['kode'] = $splitTxtKel[0];
                            $valPenyakitKel['nama'] = $splitTxtKel[1];
                        }
                        $valPenyakitKel['text'] = $splitPenyakitKel[1];
                    } else {
                        $valPenyakitKel['text'] = $keyPenyakitKel;
                    }
                    $valuePenyakitKel[] = $valPenyakitKel;
                    $valueReturn[] = $valPenyakitKel['text'];
                }
                $model->r_penyakitkeluarga = $valuePenyakitKel;
                $returnPenyakitKel = $valueReturn;
            }
            // }else{
            //     $model->r_penyakitkeluarga = $model->r_penyakitkeluarga;
            // }

            // $tmpImunJson = json_decode($model->r_imunisasi, true);
            // if (!isset($tmpImunJson['text'])) {
            if (isset($model->r_imunisasi) && is_array($model->r_imunisasi)) {
                $valueImun = [];
                foreach ($model->r_imunisasi as $keyImun) {
                    $valImun = [];
                    $splitImun = explode('_', $keyImun);
                    if (count($splitImun) > 1) {
                        $valImun['id'] = $splitImun[0];
                        $splitTxtImun = explode('-', $splitImun[1]);
                        if (count($splitTxtImun) > 1) {
                            $valImun['kode'] = $splitTxtImun[0];
                            $valImun['nama'] = $splitTxtImun[1];
                        }
                        $valImun['text'] = $splitImun[1];
                    } else {
                        $valImun['text'] = $keyImun;
                    }
                    $valueImun[] = $valImun;
                }
                $model->r_imunisasi = $valueImun;
            }
            // }else{
            //     $model->r_imunisasi = $model->r_imunisasi;
            // }

            if ($model->save()) {
                MonitoringTtvLogic::feedData($model->attributes, DocoConstants::ASESMEN_MEDIS, DocoConstants::INSTALASI_RAWAT_INAP);

                if ($anatomi) {
                    $dataAnatomi = [
                        'anatomi' => $anatomi,
                        'data_pasien' => [
                            'pendaftaran_id' => $asesmen['pendaftaran_id'],
                            'pasien_id' => $post['pasien_id'],
                            'asesmenmedis_id' => $model->asesmenmedis_id,
                        ]
                    ];
                    $save_anatomi = $this->saveAnatomi($dataAnatomi);
                    if (!$save_anatomi) {
                        $transaction->rollBack();
                        $result = [
                            'message' => 'Terjadi Kesalahan!',
                            'status' => 500,
                        ];
                    }
                }
                $transaction->commit();
                return [
                    'message' => 'Data Berhasil di simpan',
                    'return' => 'removeDisable()',
                    'status' => 200,
                    'data' => [
                        'status_merokok' => !empty($model->is_merokok) ? (bool) $model->is_merokok : false,
                        'riwayat_penyakit_keluarga' => !empty($model->r_penyakitkeluarga) ? implode(', ', $returnPenyakitKel) : '-',
                    ]
                ];
            } else {
                $transaction->rollBack();
                $result = [
                    'message' => $model->getErrors(),
                    'status' => 500,
                ];
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            $result = [
                'message' => 'Terjadi Kesalahan!',
                'status' => 500,
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $result = [
                'message' => 'Terjadi Kesalahan!',
                'status' => 500,
            ];
        }

        return $result;
    }
    public function saveAnatomi($anatomi)
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $anatomi['data_pasien']['pendaftaran_id'];
            $pasien_id = $anatomi['data_pasien']['pasien_id'];
            $asesmenmedis_id = $anatomi['data_pasien']['asesmenmedis_id'];

            $data_bagiantubuh = json_decode($anatomi['anatomi'], true);

            $model = new PeriksaTubuh;
            if ($asesmenmedis_id) {
                $count = 0;
                $data_insert_periksatubuh = [];
                if (!empty($data_bagiantubuh)) {
                    foreach ($data_bagiantubuh as $key => $value) {
                        $data_insert_periksatubuh_temp = [];
                        $data_insert_periksatubuh_temp['asesmenmedis_id'] = $asesmenmedis_id;
                        $data_insert_periksatubuh_temp['bagiantubuh_id'] = $value['bagiantubuh_id'];
                        $data_insert_periksatubuh_temp['catatan_tubuh'] = $value['catatan_tubuh'];
                        $data_insert_periksatubuh_temp['koordinat_y'] = $value['koordinat_y'];
                        $data_insert_periksatubuh_temp['koordinat_x'] = $value['koordinat_x'];
                        $data_insert_periksatubuh_temp['counters'] = $value['counters'];
                        $data_insert_periksatubuh_temp['created_date'] = isset($value['created_date']) ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $value['created_date']))) : '';
                        $data_insert_periksatubuh_temp['bagiantubuhdetail_id'] = isset($value['bagiantubuhdetail_id']) ? $value['bagiantubuhdetail_id'] : null;
                        if(isset($value['height']) && isset($value['width'])) {
                            $additional_data = [];
                            $additional_data['height'] = $value['height'];
                            $additional_data['width'] = $value['width'];
                            $data_insert_periksatubuh_temp['additional_data'] = json_encode($additional_data);
                        } else {
                            $data_insert_periksatubuh_temp['additional_data'] = null;
                        }
                        $data_insert_periksatubuh_temp['jenis_gambar_id'] = isset($value['jenis_gambar_id']) ? $value['jenis_gambar_id'] : null;
                        $data_insert_periksatubuh_temp['jenis_gambar_nama'] = isset($value['jenis_gambar_nama']) ? $value['jenis_gambar_nama'] : null;
                        $data_insert_periksatubuh_temp['berat_luka_bakar'] = isset($value['berat_luka_bakar']) ? $value['berat_luka_bakar'] : null;
                        $data_insert_periksatubuh[] = $data_insert_periksatubuh_temp;
                    }

                    if ($data_insert_periksatubuh) {
                        $list_columns = [
                            'asesmenmedis_id',
                            'bagiantubuh_id',
                            'catatan_tubuh',
                            'koordinat_y',
                            'koordinat_x',
                            'counters',
                            'created_date',
                            'bagiantubuhdetail_id',
                            'additional_data',
                            'jenis_gambar_id',
                            'jenis_gambar_nama',
                            'berat_luka_bakar'
                        ];
                        Yii::$app->db->createCommand("
                                DELETE FROM periksatubuh_t WHERE asesmenmedis_id = $asesmenmedis_id
                            ")->execute();
                        Yii::$app->db->createCommand()
                            ->batchInsert(PeriksaTubuh::tableName(), $list_columns, $data_insert_periksatubuh)
                            ->execute();
                    }

                    return [
                        'message' => 'Data Berhasil di simpan',
                        'status' => 200
                    ];
                } else {
                    Yii::$app->db->createCommand("
                        DELETE FROM periksatubuh_t WHERE asesmenmedis_id = $asesmenmedis_id
                    ")->execute();

                    return [
                        'message' => 'Data Berhasil di simpan',
                        'status' => 200
                    ];
                }
            } else {
                return [
                    'message' => 'Data fisik kosong',
                    'status' => 500
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    /**
     * @controller actionCetakAsesmen
     * @attribute #no_rekam_medik# => No Rekam Medik
     * @attribute #nama_pasien# => Nama Pasien
     * @attribute #tanggal_lahir# => Tanggal lahir pasien
     * @attribute #jeniskelamin# => Jenis Kelamin pasien
     * @attribute #ruangan_kelas# => Ruangan / Kelas
     * @attribute #dokter_nama# => Dokter yang Merawat
     * @attribute #penjamin# => Penjamin
     * @attribute #tgl_asesmenmedis# => Tanggal Asesmen Medis
     * @attribute #sumber_info# => Sumber Informasi
     * @attribute #keluhan_utama# => Keluhan Utama
     * @attribute #keluhan_tambahan# => Keluhan Tambahan
     * @attribute #r_penyakitsekarang# => Riwayat Penyakit Sekarang
     * @attribute #lama_sakit# => Lama Sakit
     * @attribute #r_penyakitdahulu# => Riwayat Penyakit Dahulu
     * @attribute #r_penyakitkeluarga# => Riwayat Penyakit Keluarga
     * @attribute #r_imunisasi# => Riwayat Imunisasi
     * @attribute #r_peskk# => Riwayat Pekerjaan, Sosial, Ekonomi, Kejiwaan dan Kebiasaan
     * @attribute #is_merokok# => Status Merokok
     * @attribute #jml_rokok# => Jumlah Batang Rokok
     * @attribute #obat_diberikan# => Obat yang sudah diberikan
     * @attribute #obat_diberikan# => Obat yang sudah diberikan
     * @attribute #r_makanan# => Riwayat Makanan
     * @attribute #r_kelahiran# => Riwayat Kelahiran
     * @attribute #r_alergiobat# => Riwayat Alergi Obat
     * @attribute #keterangan# => Keterangan Anamnesa
     * @attribute #td_systolic# => Tekanan Darah Systolic
     * @attribute #td_diastolic# => Tekanan Darah Diastolic
     * @attribute #tekanan_darah# => Tekanan Darah
     * @attribute #hasil_td# => Hasil Tekanan Darah
     * @attribute #tinggi_badan# => Tinggi Badan
     * @attribute #bb_ideal# => Berat Badan Ideal
     * @attribute #detak_nadi# => Detak Nadi
     * @attribute #pernapasan# => Pernapasan
     * @attribute #berat_badan# => Berat Badan
     * @attribute #imt# => Nilai IMT
     * @attribute #ket_imt# => Keterangan Nilai IMT
     * @attribute #denyut_jantung# => Denyut Jantung
     * @attribute #suhu_tubuh# => Suhu Tubuh
     * @attribute #gcs_eye# => GCS Eye
     * @attribute #gcs_verbal# => GCS Verbal
     * @attribute #gcs_motorik# => GCS Motorik
     * @attribute #hasil_gcs# => Hasil GCS
     * @attribute #kontak# => Kontak
     * @attribute #metod_asmennyeri# => Metode Asesmen Nyeri
     * @attribute #is_terintubasi# => Terintubasi
     * @attribute #skala# => Skala
     * @attribute #lokasi_nyeri# => Lokasi Nyeri
     * @attribute #inspeksi_kepala# => Inspeksi Kepala
     * @attribute #palpasi_kepala# => Palpasi Kepala
     * @attribute #neurologi_kepala# => Neurologi Kepala
     * @attribute #is_kakududuk# => Kaku Duduk
     * @attribute #jvp# => JVP
     * @attribute #kgb# => KGB
     * @attribute #inspeksi_toraks# => Inspeksi Toraks
     * @attribute #palpasi_toraks# => Palpasi Toraks
     * @attribute #perkusi_toraks# => Perkusi Toraks
     * @attribute #auskultasi_toraks# => Auskultasi Toraks
     * @attribute #inspeksi_punggung# => Inspeksi Punggung
     * @attribute #palpasi_punggung# => Palpasi Punggung
     * @attribute #perkusi_punggung# => Perkusi Punggung
     * @attribute #auskultasi_punggung# => Auskultasi Punggung
     * @attribute #inspeksi_abdomen# => Inspeksi Abdomen
     * @attribute #palpasi_abdomen# => Palpasi Abdomen
     * @attribute #perkusi_abdomen# => Perkusi Abdomen
     * @attribute #auskultasi_abdomen# => Auskultasi Abdomen
     * @attribute #hepar# => Hepar
     * @attribute #lien# => Lien
     * @attribute #inspeksi_ekstrim# => Inspeksi Ekstrimitas
     * @attribute #palpasi_ekstrim# => Palpasi Ekstrimitas
     * @attribute #perkusi_ekstrim# => Perkusi Ekstrimitas
     * @attribute #anus_genitalia# => Anus Genitalia
     * @attribute #catatan_rad# => Catatan Hasil Radiologi
     * @attribute #catatan_lab# => Catatan Hasil Laboratorium
     * @attribute #diagnosa_id# => Diagnosa
     * @attribute #masalah# => Masalah
     * @attribute #discharge_plan# => Discharge Planning
     * @attribute #care_plan# => Care Planning
     * @attribute #nama_pegawai# => Nama Pegawai
     * @attribute #timestamps# => tanggal hari ini
     **/
    public function actionCetakAsesmen($id)
    {
        try {
            $request = Yii::$app->request;
            $print = new DocoPrint();
            $nama_pegawai = $request->get('nama_pegawai', null);
            $images = [];
            $htmlTable = [];
            $htmlImage = [];

            /* define data and variable start */
            $data = $this->actionGetAsesmen($id);
            $images['anatomi_tubuh'] = $data['asesmen']['kategori_asmed'] == 2 ? Yii::$app->urlManagerFrontend->createUrl('')."media/img/img-pemeriksaan/bagian_tubuh_anak.png" : Yii::$app->urlManagerFrontend->createUrl('')."media/img/img-pemeriksaan/bagian_tubuh_medis.jpg";
            $images['lukabakar'] = $data['asesmen']['kategori_asmed'] == 2 ? Yii::$app->urlManagerFrontend->createUrl('')."media/img/img-pemeriksaan/bagian_luka_bakar_anak.png" : Yii::$app->urlManagerFrontend->createUrl('')."media/img/img-pemeriksaan/bagian_luka_bakar.png";
            $getInfoKunjunganRi = InfoKunjunganRi::find()->select(['no_rekam_medik', 'nama_pasien', 'jenis_kelamin', 'ruangan_nama', 'kelaspelayanan_nama', 'tanggal_lahir', 'nama_pegawai', 'penjamin_nama', 'pasien_id'])->where(['pendaftaran_id' => $id])->one();
            $gcsindicator_eye = DocoConstants::GCS_LIST_EYE;
            $gcsindicator_verbal = DocoConstants::GCS_LIST_VERBAL;
            $gcsindicator_motorik = DocoConstants::GCS_LIST_MOTORIK;
            $data_listgcs = [];
            $data_metodegcs = $this->getOrSetCache(DocoConstants::VAR_CACHE_GCS_METODE, MetodeGcs::find());
            foreach ($data_metodegcs as $key => $value) {
                if (!$value['metodegcs_nilai']) {
                    continue;
                }
                if ($value['metodegcs_singkatan'] == $gcsindicator_eye) {
                    $data_listgcs['eye'][$value['metodegcs_id']] = $value['metodegcs_nama'];
                } elseif ($value['metodegcs_singkatan'] == $gcsindicator_verbal) {
                    $data_listgcs['verbal'][$value['metodegcs_id']] = $value['metodegcs_nama'];
                } elseif ($value['metodegcs_singkatan'] == $gcsindicator_motorik) {
                    $data_listgcs['motorik'][$value['metodegcs_id']] = $value['metodegcs_nama'];
                }
            }

            $kategori = [];
            if(isset($data['asesmen']['kategori_asmed'])){
                if($data['asesmen']['kategori_asmed'] == 1){
                    $kategori = [1,2];
                }else {
                    $kategori = [3,4];
                }
            }

            $periksa_tubuh = PeriksaTubuh::find()->select([
                'periksatubuh_t.periksatubuh_id',
                'periksatubuh_t.created_date',
                'periksatubuh_t.catatan_tubuh',
                'periksatubuh_t.koordinat_x',
                'periksatubuh_t.koordinat_y',
                'periksatubuh_t.jenis_gambar_nama',
                'periksatubuh_t.jenis_gambar_id',
                'periksatubuh_t.berat_luka_bakar',
                'bagiantubuh_m.namabagtubuh',
                'bagiantubuhdetail_m.nama_bagiantubuh',
                'periksatubuh_t.additional_data as periksatubuh_additional_data',
            ])->join('JOIN', 'bagiantubuh_m', 'bagiantubuh_m.bagiantubuh_id=periksatubuh_t.bagiantubuh_id')
                ->join('JOIN', 'bagiantubuhdetail_m', 'bagiantubuhdetail_m.bagiantubuhdetail_id=periksatubuh_t.bagiantubuhdetail_id')
                ->where(['asesmenmedis_id' => $data['asesmen']['asesmenmedis_id']])
                ->andWhere(['in', 'jenis_gambar_id', $kategori])
                ->orderBy('periksatubuh_t.periksatubuh_id ASC')->asArray()->all();

            // grouping data periksa tubuh by jenis gambar
            $periksa_tubuh = ArrayHelper::index($periksa_tubuh, null, function($val){
                if(strpos($val['jenis_gambar_nama'], 'LB') != false){
                    return 'lukabakar';
                }
                return 'anatomi_tubuh';
            }, 'id');


            // looping jenis status lokalis {anatomi_tubuh, lukabakar} yang disimpan di images
            foreach ($images as $key => $image) {
                $xx = file_get_contents($image);

                $imagecreate  = imagecreatefromstring($xx);
                $white  = imagecolorallocate($imagecreate, 255, 255, 255);
                $red = imagecolorallocate($imagecreate, 0xFF, 0x00, 0x00);
                $orange = imagecolorallocate($imagecreate, 255, 112, 67);
                $fontImage = "fonts/arialbd.ttf";

                // default height and width
                $height = $key == 'anatomi_tubuh' ? 520 : ($key == 'lukabakar' ? 550 : 557);
                $width = $key == 'anatomi_tubuh' ? 500 : ($key == 'lukabakar' ? 300 : 666);

                $no = 1;
                $htmlTable[$key] = '<table border="1" cellpadding="5" cellspacing="0" style="width:400px">';
                $htmlTable[$key] .= '<tbody>';
                $htmlTable[$key] .= '<tr>';
                $htmlTable[$key] .= '<th>No</th>';
                $htmlTable[$key] .= '<th>Tanggal periksa</th>';
                $htmlTable[$key] .= '<th>Bagian Tubuh</th>';
                $htmlTable[$key] .= '<th>Bagian Tubuh Detail</th>';
                $htmlTable[$key] .= '<th>Catatan</th>';
                $htmlTable[$key] .= $key == 'lukabakar' ? '<th>Berat luka bakar</th>' : '';
                $htmlTable[$key] .= '</tr>';

                if(isset($periksa_tubuh[$key])){
                    foreach ($periksa_tubuh[$key] as $key_periksa_tubuh => $value) {
                        // Generate table
                        $htmlTable[$key] .= '<tr><td>' . $no . '</td><td>' . date('d/m/Y H:i:s', strtotime($value['created_date'])) . '</td><td>' . $value['namabagtubuh'] . '</td><td>' . $value['nama_bagiantubuh'] . '</td><td>' . $value['catatan_tubuh'] . '</td>'.($key == 'lukabakar' ? "<td>".$value['berat_luka_bakar']."</td>" : "").'</tr>';
                        if (isset($value['koordinat_x']) && isset($value['koordinat_y'])) {
                            $notext = $no . "";
                            try {
                                $additional_data = isset($value['periksatubuh_additional_data']) ? json_decode($value['periksatubuh_additional_data'], true) : [];
                            } catch(\Exception $ex) {
                                $additional_data = [];
                            }
                            $height = isset($additional_data['height']) ? $additional_data['height'] : 557;
                            $width = isset($additional_data['width']) ? $additional_data['width'] : 665;
                            if($value['jenis_gambar_id'] == 1){
                                imagefilledarc($imagecreate, $value['koordinat_x'] * imagesx($imagecreate) / $width + 10, $value['koordinat_y'] * imagesy($imagecreate) / $height + (25 / 2), 25, 25, 0, 360, $orange, IMG_ARC_PIE);
                                imagefttext($imagecreate, 10, 0, $value['koordinat_x'] * imagesx($imagecreate) / $width - (3.5 * strlen($notext))+10, $value['koordinat_y'] * imagesy($imagecreate) / $height + (25 / 2) + 5, $white, $fontImage, $notext);
                            }else {
                                imagefilledarc($imagecreate, $value['koordinat_x'] * imagesx($imagecreate) / $width+20, $value['koordinat_y'] * imagesy($imagecreate) / $height + (100 / 2), 60, 60, 0, 360, $orange, IMG_ARC_PIE);
                                imagefttext($imagecreate, 35, 0, $value['koordinat_x'] * imagesx($imagecreate) / $width - (3.5 * strlen($notext))+12, $value['koordinat_y'] * imagesy($imagecreate) / $height + (130 / 2), $white, $fontImage, $notext);
                            }

                        }

                        $no++;
                    }
                }else{
                    $htmlTable[$key] .= '<tr><td colspan="'.($key == "lukabakar" ? "6" : "5" ).'" style="text-align:center;">--- Tidak ada data ---</td></tr>';
                }
                // Close tag
                $htmlTable[$key] .= '</tbody>';
                $htmlTable[$key] .= '</table>';
                $htmlTable[$key] .= $key == 'lukabakar' ? '<br><div style="margin-top:20px;">Berat luka bakar : '.($data['asesmen']['luka_bakar'] != null ? $data['asesmen']['luka_bakar'] : 0).'%</div>' : '';
                $filename = isset($data['asesmen']) && $data['asesmen']['kategori_asmed'] == 2 ? 'anak_'.$key : 'dewasa_'.$key; // ex : tmp_bagian_tubuh-medis_anak_lukabakar
                $fileTempPath = Yii::getAlias("@webroot") . "/assets/tmp_bagian_tubuh_medis_".$filename.".png";
                imagesavealpha($imagecreate, true);
                imagepng($imagecreate, $fileTempPath);
                    $htmlImage[$key] = '
                            <div>
                        <img src="' . $fileTempPath . '" width="'.$width.'" height="'.$height.'">
                        </div>';
            }
            // $asda = $asdad;
            $asesmen = isset($data['asesmen']) ? $data['asesmen'] : [];
            $anatomi = isset($data['anatomi']) ? $data['anatomi'] : [];
            $ruangan = isset($getInfoKunjunganRi['ruangan_nama']) ? $getInfoKunjunganRi['ruangan_nama'] : '-';
            $kelas = isset($getInfoKunjunganRi['kelaspelayanan_nama']) ? $getInfoKunjunganRi['kelaspelayanan_nama'] : '-';
            $penyakitDahulu = isset($asesmen['r_penyakitdahulu']) ? json_decode($asesmen['r_penyakitdahulu'], true) : [];
            $penyakitKeluarga = isset($asesmen['r_penyakitkeluarga']) ? json_decode($asesmen['r_penyakitkeluarga'], true) : [];
            $penyakitImunisasi = isset($asesmen['r_imunisasi']) ? json_decode($asesmen['r_imunisasi'], true) : [];
            $penyakitKelahiran = isset($asesmen['r_kelahiran']) ? $asesmen['r_kelahiran'] : '';
            // kebutuhan change jadi free text alergi di komen - issue 1699
            // $penyakitAlergiObat = isset($asesmen['r_alergiobat']) && $asesmen['r_alergiobat'] != '' ? json_decode($asesmen['r_alergiobat'], true) : [];


            $nama_diagnosa = '-';
            if ($asesmen['diagnosa_id']) {
                // $diagnosa = $this->getDiagnosa($asesmen['diagnosa_id']);
                // $nama_diagnosa = $diagnosa ? $diagnosa['diagnosa_kode'] .' - '. $diagnosa['diagnosa_nama'] : '-';
                $tmpDiag = json_decode($asesmen['diagnosa_id'], true);
                if (isset($tmpDiag)) {
                    $nama_diagnosa = $tmpDiag['text'];
                }
            }

            $r_penyakitkeluarga = [];
            if ($asesmen['r_penyakitkeluarga'] != '') {
                $r_penyakitkeluarga = json_decode($asesmen['r_penyakitkeluarga']);
            }

            $riwayat_penyakit_dahulu = [];
            $riwayat_imunisasi = '-';
            $riwayat_kelahiran = '-';
            $riwayat_alergi_obat = '-';

            if ($getInfoKunjunganRi != '') {
                $riwayat = $this->actionGetRiwayat($getInfoKunjunganRi['pasien_id'], $id);

                if (!empty($riwayat)) {
                    foreach ($riwayat as $key => $value) {
                        if ($value['r_penyakitdahulu'] != '') {
                            $riwayat_penyakit_dahulu = json_decode($value['r_penyakitdahulu']);

                            if (!empty($riwayat_penyakit_dahulu)) {
                                foreach ($riwayat_penyakit_dahulu as $keyrpd => $valuerpd) {
                                    $penyakitDahulu[] = $valuerpd;
                                }
                            }
                        }

                        if ($value['r_penyakitkeluarga'] != '') {
                            $riwayat_penyakit_keluarga = json_decode($value['r_penyakitkeluarga']);

                            if (!empty($riwayat_penyakit_keluarga)) {
                                foreach ($riwayat_penyakit_keluarga as $keyrpk => $valuerpk) {
                                    $r_penyakitkeluarga[] = $valuerpk;
                                }
                            }
                        }

                        if ($value['r_imunisasi'] != '') {
                            $riwayat_imunisasi = json_decode($value['r_imunisasi']);

                            if (!empty($riwayat_imunisasi)) {
                                foreach ($riwayat_imunisasi as $keyri => $valueri) {
                                    if (!in_array($valueri, $penyakitImunisasi)) {
                                        $penyakitImunisasi[] = $valueri;
                                    }
                                }
                            }
                        }

                        if ($value['r_kelahiran'] != '') {
                            $riwayat_kelahiran = $value['r_kelahiran'];

                            if ($penyakitKelahiran != '') {
                                $penyakitKelahiran = $penyakitKelahiran . ', ' . $riwayat_kelahiran;
                            } else {
                                $penyakitKelahiran = $riwayat_kelahiran;
                            }
                        }

                        // untuk kebutuhan change field alergi menjadi free text jadi di comment dulu ya - issue 1699
                        // if ($value['r_alergiobat'] != '') {
                        //     $riwayat_alergi_obat = json_decode($value['r_alergiobat']);

                        //     if (!empty($riwayat_alergi_obat)) {
                        //         foreach ($riwayat_alergi_obat as $keyrao => $valuerao) {
                        //             if (!in_array($valuerao, $penyakitAlergiObat) && $valuerao != '') {
                        //                 $penyakitAlergiObat[] = $valuerao;
                        //             }
                        //         }
                        //     }
                        // }
                    }
                }
            }

            if (!empty($r_penyakitkeluarga)) {
                foreach ($r_penyakitkeluarga as $key => $value) {
                    // $exploded = explode('_', $value);

                    // if (count($exploded) == 2) {
                    //     $r_penyakitkeluarga[$key] = $exploded[1];
                    // } else {
                    //     $r_penyakitkeluarga[$key] = $exploded[0];
                    // }
                    $r_penyakitkeluarga[$key] = $value->text;
                }
            }

            $r_penyakitdahulu = [];
            if (!empty($penyakitDahulu)) {
                foreach ($penyakitDahulu as $key => $value) {
                    if (is_array($value)) {
                        $r_penyakitdahulu[$key]['tahun'] = $value['tahun'];
                        $r_penyakitdahulu[$key]['penyakit'] = $value['penyakit'];
                        $r_penyakitdahulu[$key]['terapi'] = $value['terapi'];
                    } elseif (is_object($value)) {
                        $r_penyakitdahulu[$key]['tahun'] = $value->tahun;
                        $r_penyakitdahulu[$key]['penyakit'] = $value->penyakit;
                        $r_penyakitdahulu[$key]['terapi'] = $value->terapi;
                    }
                }
            }

            $r_tumbuh_kembang = !empty($asesmen['r_tumbuh_kembang']) ? json_decode($asesmen['r_tumbuh_kembang'], true) : [];

            $kepala = isset($asesmen['kepala']) ? $asesmen['kepala'] : '';
            $mulut = isset($asesmen['mulut']) ? $asesmen['mulut'] : '';
            $mata = isset($asesmen['mata']) ? $asesmen['mata'] : '';
            $tht = isset($asesmen['tht']) ? $asesmen['tht'] : '';
            $leher = isset($asesmen['leher']) ? $asesmen['leher'] : '';
            $toraks = isset($asesmen['toraks']) ? $asesmen['toraks'] : '';
            $jantung = isset($asesmen['jantung']) ? $asesmen['jantung'] : '';
            $paru = isset($asesmen['paru']) ? $asesmen['paru'] : '';
            $abdomen = isset($asesmen['abdomen']) ? $asesmen['abdomen'] : '';
            $genitalia_anus = isset($asesmen['genitalia_anus']) ? $asesmen['genitalia_anus'] : '';
            $ekstremitas = isset($asesmen['ekstremitas']) ? $asesmen['ekstremitas'] : '';
            $kulit = isset($asesmen['kulit']) ? $asesmen['kulit'] : '';
            $rencana = isset($asesmen['rencana']) ? $asesmen['rencana'] : '';
            /* define data and variable end */
            $riwayat_terdahulu = $this->renderPartial('riwayat-dahulu', ['dataPenyakit' => $r_penyakitdahulu]);

            $print->attributes = [
                '#no_rekam_medik#' => isset($getInfoKunjunganRi['no_rekam_medik']) ? $getInfoKunjunganRi['no_rekam_medik'] : '',
                '#nama_pasien#' => isset($getInfoKunjunganRi['nama_pasien']) ? $getInfoKunjunganRi['nama_pasien'] : '',
                '#tanggal_lahir#' => isset($getInfoKunjunganRi['tanggal_lahir']) ? $getInfoKunjunganRi['tanggal_lahir'] : '',
                '#jeniskelamin#' => isset($getInfoKunjunganRi['jenis_kelamin']) ? $getInfoKunjunganRi['jenis_kelamin'] : '',
                '#ruangan_kelas#' => $ruangan . '/' . $kelas,
                '#dokter_nama#' => isset($getInfoKunjunganRi['nama_pegawai']) ? $getInfoKunjunganRi['nama_pegawai'] : '',
                '#penjamin#' => isset($getInfoKunjunganRi['penjamin_nama']) ? $getInfoKunjunganRi['penjamin_nama'] : '',
                '#riwayat_pasien#' => $this->renderPartial('riwayat-pasien', compact('asesmen', 'r_penyakitkeluarga', 'penyakitImunisasi', 'penyakitKelahiran', 'r_tumbuh_kembang', 'riwayat_terdahulu')),
                // kebutuhan change field alergi menjadi free text - issue 1699
                // '#r_alergiobat#' => !empty($penyakitAlergiObat) ? implode(', ', $penyakitAlergiObat) : '-',
                '#td_systolic#' => isset($asesmen['td_systolic']) ? $asesmen['td_systolic'] : '',
                '#td_diastolic#' => isset($asesmen['td_diastolic']) ? $asesmen['td_diastolic'] : '',
                '#tekanan_darah#' => isset($asesmen['tekanan_darah']) ? $asesmen['tekanan_darah'] . ' /mmHG' : ' - ',
                '#hasil_td#' => isset($asesmen['hasil_td']) ? $asesmen['hasil_td'] : '',
                '#tinggi_badan#' => isset($asesmen['tinggi_badan']) ? str_replace(".", ",", $asesmen['tinggi_badan']) . ' cm' : ' - ',
                '#bb_ideal#' => isset($asesmen['bb_ideal']) ? str_replace(".", ",", $asesmen['bb_ideal']) . ' kg' : ' - ',
                '#detak_nadi#' => isset($asesmen['detak_nadi']) ? $asesmen['detak_nadi'] . ' /Menit' : ' - ',
                '#kategori_detak_nadi#' => isset($asesmen['kategori_denyut_nadi']) ? $asesmen['kategori_denyut_nadi'] : ' - ',
                '#pernapasan#' => isset($asesmen['pernapasan']) ? $asesmen['pernapasan'] . ' /Menit' : ' - ',
                '#berat_badan#' => isset($asesmen['berat_badan']) ? str_replace(".", ",", $asesmen['berat_badan']) . ' kg' : ' - ',
                '#imt#' => isset($asesmen['imt']) ? str_replace(".", ",", $asesmen['imt']) : ' - ',
                '#ket_imt#' => isset($asesmen['ket_imt']) ? $asesmen['ket_imt'] : '',
                '#denyut_jantung#' => isset($asesmen['denyut_jantung']) ? $asesmen['denyut_jantung'] : '',
                '#suhu_tubuh#' => isset($asesmen['suhu_tubuh']) ? str_replace(".", ",", $asesmen['suhu_tubuh']) : '',
                '#gcs_eye#' => isset($asesmen['gcs_eye']) ? isset($data_listgcs['eye'][$asesmen['gcs_eye']]) ? $data_listgcs['eye'][$asesmen['gcs_eye']] : '' : '',
                '#gcs_verbal#' => isset($asesmen['gcs_verbal']) ? isset($data_listgcs['verbal'][$asesmen['gcs_verbal']]) ? $data_listgcs['verbal'][$asesmen['gcs_verbal']] : ''  : '',
                '#gcs_motorik#' => isset($asesmen['gcs_motorik']) ? isset($data_listgcs['motorik'][$asesmen['gcs_motorik']]) ? $data_listgcs['motorik'][$asesmen['gcs_motorik']] : ''  : '',
                '#hasil_gcs#' => isset($asesmen['hasil_gcs']) ? $asesmen['hasil_gcs'] : '',
                '#kontak#' => isset($asesmen['kontak']) ? ($asesmen['kontak'] == 1) ? 'Adekuat' : 'Tidak Adekuat' : '',
                '#metod_asmennyeri#' => isset($asesmen['metod_asmennyeri']) ? $asesmen['metod_asmennyeri'] : '',
                '#is_terintubasi#' => isset($asesmen['is_terintubasi']) ? ($asesmen['is_terintubasi'] == 1) ? 'Terintubasi' : 'Tidak Terintubasi' : '',
                '#skala#' => isset($asesmen['skala']) ? $asesmen['skala'] : '',
                '#lokasi_nyeri#' => isset($asesmen['lokasi_nyeri']) ? $asesmen['lokasi_nyeri'] : '',
                '#inspeksi_kepala#' => isset($asesmen['inspeksi_kepala']) ? $asesmen['inspeksi_kepala'] : '',
                '#palpasi_kepala#' => isset($asesmen['palpasi_kepala']) ? $asesmen['palpasi_kepala'] : '',
                '#neurologi_kepala#' => isset($asesmen['neurologi_kepala']) ? $asesmen['neurologi_kepala'] : '',
                '#is_kakududuk#' => isset($asesmen['is_kakududuk']) ? ($asesmen['is_kakududuk'] == 1) ? 'Ya' : 'Tidak' : '',
                '#jvp#' => isset($asesmen['jvp']) ? $asesmen['jvp'] : '',
                '#kgb#' => isset($asesmen['kgb']) ? $asesmen['kgb'] : '',
                '#inspeksi_toraks#' => isset($asesmen['inspeksi_toraks']) ? $asesmen['inspeksi_toraks'] : '',
                '#palpasi_toraks#' => isset($asesmen['palpasi_toraks']) ? $asesmen['palpasi_toraks'] : '',
                '#perkusi_toraks#' => isset($asesmen['perkusi_toraks']) ? $asesmen['perkusi_toraks'] : '',
                '#auskultasi_toraks#' => isset($asesmen['auskultasi_toraks']) ? $asesmen['auskultasi_toraks'] : '',
                '#inspeksi_punggung#' => isset($asesmen['inspeksi_punggung']) ? $asesmen['inspeksi_punggung'] : '',
                '#palpasi_punggung#' => isset($asesmen['palpasi_punggung']) ? $asesmen['palpasi_punggung'] : '',
                '#perkusi_punggung#' => isset($asesmen['perkusi_punggung']) ? $asesmen['perkusi_punggung'] : '',
                '#auskultasi_punggung#' => isset($asesmen['auskultasi_punggung']) ? $asesmen['auskultasi_punggung'] : '',
                '#inspeksi_abdomen#' => isset($asesmen['inspeksi_abdomen']) ? $asesmen['inspeksi_abdomen'] : '',
                '#palpasi_abdomen#' => isset($asesmen['palpasi_abdomen']) ? $asesmen['palpasi_abdomen'] : '',
                '#perkusi_abdomen#' => isset($asesmen['perkusi_abdomen']) ? $asesmen['perkusi_abdomen'] : '',
                '#auskultasi_abdomen#' => isset($asesmen['auskultasi_abdomen']) ? $asesmen['auskultasi_abdomen'] : '',
                '#hepar#' => isset($asesmen['hepar']) ? $asesmen['hepar'] : '',
                '#lien#' => isset($asesmen['lien']) ? $asesmen['lien'] : '',
                '#inspeksi_ekstrim#' => isset($asesmen['inspeksi_ekstrim']) ? $asesmen['inspeksi_ekstrim'] : '',
                '#palpasi_ekstrim#' => isset($asesmen['palpasi_ekstrim']) ? $asesmen['palpasi_ekstrim'] : '',
                '#neurologi_ekstrim#' => isset($asesmen['neurologi_ekstrim']) ? $asesmen['neurologi_ekstrim'] : '',
                '#anus_genitalia#' => isset($asesmen['anus_genitalia']) ? $asesmen['anus_genitalia'] : '',
                '#catatan_rad#' => isset($asesmen['catatan_rad']) ? $asesmen['catatan_rad'] : '',
                '#catatan_lab#' => isset($asesmen['catatan_lab']) ? $asesmen['catatan_lab'] : '',
                '#diagnosa_id#' => $nama_diagnosa,
                '#masalah#' => isset($asesmen['masalah']) ? $asesmen['masalah'] : '',
                '#discharge_plan#' => isset($asesmen['discharge_plan']) ? ($asesmen['discharge_plan'] == 1) ? 'Ya' : 'Tidak' : '',
                '#care_plan#' => isset($asesmen['care_plan']) ? $asesmen['care_plan'] : '',
                '#status_lokalis#' => $this->renderPartial('status-lokalis', ['dataAnatomi' => ($anatomi) ? $anatomi : []]),
                '#date_now#' => date('j F Y', strtotime('NOW')),
                '#time_now#' => date('h:i:s', strtotime('NOW')),
                '#timestamps#' => date('j F Y', strtotime('NOW')),
                '#kepala#' => $kepala,
                '#mulut#' => $mulut,
                '#mata#' => $mata,
                '#tht#' => $tht,
                '#leher#' => $leher,
                '#toraks#' => $toraks,
                '#jantung#' => $jantung,
                '#paru#' => $paru,
                '#abdomen#' => $abdomen,
                '#genitalia_anus#' => $genitalia_anus,
                '#ekstremitas#' => $ekstremitas,
                '#kulit#' => $kulit,
                '#rencana#' => $rencana,
                '#pegawai_cetak#' => $nama_pegawai,
                '#nama_pegawai#' => $nama_pegawai,
                '#gambar_anatomi#' => $htmlImage['anatomi_tubuh'],
                '#list_tabel_anatomi#' => isset($htmlTable['anatomi_tubuh']) ? $htmlTable['anatomi_tubuh'] : null,
                '#gambar_lukabakar#' => $htmlImage['lukabakar'],
                '#list_tabel_lukabakar#' => isset($htmlTable['lukabakar']) ? $htmlTable['lukabakar'] : null ,
                '#inspeksi_kepala#' => @$data['asesmen']['inspeksi_kepala'],
                '#palpasi_kepala#' => @$data['asesmen']['palpasi_kepala'],
                '#neurologi_kepala#' => @$data['asesmen']['neurologi_kepala'],
                '#kakududuk#' => @$data['asesmen']['is_kakududuk'] ? 'Ya' : 'Tidak',
                '#jvp#' => @$data['asesmen']['jvp'],
                '#kgb#' => @$data['asesmen']['kgb'],
                '#inspeksi_toraks#' => @$data['asesmen']['inspeksi_toraks'],
                '#palpasi_toraks#' => @$data['asesmen']['palpasi_toraks'],
                '#perkusi_toraks#' => @$data['asesmen']['perkusi_toraks'],
                '#auskultasi_toraks#' => @$data['asesmen']['auskultasi_toraks'],
                '#inspeksi_punggung#' => @$data['asesmen']['inspeksi_punggung'],
                '#palpasi_punggung#' => @$data['asesmen']['palpasi_punggung'],
                '#perkusi_punggung#' => @$data['asesmen']['perkusi_punggung'],
                '#auskultasi_punggung#' => @$data['asesmen']['auskultasi_punggung'],
                '#inspeksi_abdomen#' => @$data['asesmen']['inspeksi_abdomen'],
                '#palpasi_abdomen#' => @$data['asesmen']['palpasi_abdomen'],
                '#perkusi_abdomen#' => @$data['asesmen']['perkusi_abdomen'],
                '#auskultasi_abdomen#' => @$data['asesmen']['auskultasi_abdomen'],
                '#inspeksi_ekstrim#' => @$data['asesmen']['inspeksi_ekstrim'],
                '#palpasi_ekstrim#' => @$data['asesmen']['palpasi_ekstrim'],
                '#neurologi_ekstrim#' => @$data['asesmen']['neurologi_ekstrim'],
                '#anus_genitalia#' => @$data['asesmen']['anus_genitalia'],
                // Section Pemeriksaan Fisik
                '#pemeriksaan_kepala#'=> $this->getValueRadioButton('normal',$asesmen['kepala'],$asesmen['kepala_lainnya']),
                '#pemeriksaan_mata#'=> $this->getValueRadioButton('normal',$asesmen['mata'],$asesmen['mata_lainnya']),
                '#pemeriksaan_tht#'=> $this->getValueRadioButton('normal',$asesmen['tht'],$asesmen['tht_lainnya']),
                '#pemeriksaan_leher#'=> $this->getValueRadioButton('normal',$asesmen['leher'],$asesmen['leher_lainnya']),
                '#pemeriksaan_mulut#'=> $this->getValueRadioButton('normal',$asesmen['mulut'],$asesmen['mulut_lainnya']),
                '#pemeriksaan_thoraks#'=> $this->getValueRadioButton('normal',$asesmen['toraks'],$asesmen['thoraks_lainnya']),
                '#pemeriksaan_paruparu_pergerakan#' => $this->getValueRadioButton('asimetris',$asesmen['pergerakan'],null),
                '#pemeriksaan_paruparu_perkusi#' => $this->getValueRadioButton('normal',$asesmen['perkusi'],$asesmen['perkusi_lainnya']),
                '#pemeriksaan_paruparu_pernapasan#' => $this->getValueRadioButton('normal',$asesmen['pernapasan'],$asesmen['pernapasan_lainnya']),
                '#pemeriksaan_paruparu_rochi#' => $this->getValueRadioButton('ada',$asesmen['rochi'],null),
                '#pemeriksaan_paruparu_wheezing#' => $this->getValueRadioButton('ada',$asesmen['wheezing'],null),
                '#pemeriksaan_jantung_irama#' => $this->getValueRadioButton('reguler',$asesmen['irama'],null),
                '#pemeriksaan_jantung_bunyijantung#' => $this->getValueRadioButton('normal',$asesmen['bunyi_jantung'],$asesmen['bunyi_jantung_lainnya']),
                '#pemeriksaan_abdomen_kelainan#' => $this->getValueRadioButton('normal',$asesmen['kelainan'],$asesmen['kelaianan_lainnya']),
                '#pemeriksaan_abdomen_benjolan#' => $this->getValueRadioButton('ya',$asesmen['benjolan'],$asesmen['benjolan_lainnya']),
                '#pemeriksaan_abdomen_tekan#' => $this->getValueRadioButton('normal',$asesmen['nyeri_tekan'],$asesmen['nyeri_tekan_lainnya']),
                '#pemeriksaan_abdomen_hernia#' => $this->getValueRadioButton('normal',$asesmen['hernia'],$asesmen['hernia_lainnya']),
                '#pemeriksaan_abdomen_bisingusus#' => $this->getValueRadioButton('normal',$asesmen['bising_usus'],$asesmen['bising_usus_lainnya']),
                '#pemeriksaan_abdomen_distensi#' => $this->getValueRadioButton('normal',$asesmen['distensi'],$asesmen['distensi_lainnya']),
                '#pemeriksaan_tulangbelakang#' => $this->getValueRadioButton('normal',$asesmen['tulang_belakang'],$asesmen['tulang_belakang_lainnya']),
                '#pemeriksaan_sistem_saraf#' => $this->getValueRadioButton('normal',$asesmen['sistem_saraf'],$asesmen['sistem_saraf_lainnya']),
                '#pemeriksaan_genetalia#' => $this->getValueRadioButton('normal',$asesmen['genetalia'],$asesmen['genetalia_lainnya']),
                '#pemeriksaan_edema#' => $this->getValueRadioButton('ya',$asesmen['edema'],$asesmen['edema_lainnya']),
                '#pemeriksaan_crt#' => $this->getValueRadioButton('ya',$asesmen['crt'],$asesmen['crt_lainnya']),
                '#pemeriksaan_lain_lain#' => isset($asesmen['pemeriksaan_fisik_lainnya']) ? $asesmen['pemeriksaan_fisik_lainnya']  : '',
                '#imagenya#' => $htmlImage['lukabakar'],
                '#tabelnya#' => $htmlTable['lukabakar'],
                '#tgl_asesmenmedis#' => $data['asesmen']['tgl_asesmenmedis'] ? date('j F Y', strtotime($data['asesmen']['tgl_asesmenmedis'])) : '',
            ];

            $print->Output();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function getValueRadioButton($string, $val, $alasan = null)
    {
        
        $result = $yes = $no = '';
        if($string == 'normal'){
            $yes = 'Normal';
            $no = 'Tidak Normal';
        }else if($string == 'reguler') {
            $yes = 'Reguler';
            $no = 'Inreguler';
        }else if($string == 'ada') {
            $yes = 'Ada';
            $no = 'Tidak Ada';
        }else if($string == 'asimetris') {
            $yes = 'Normal';
            $no = 'Asimetris';
        } else {
            $yes = 'Tidak';
            $no = 'Ya';
        }

        $alasan = isset($alasan) ? ', '.$alasan : $alasan;
        if(isset($val)){
            $result = $val == 0 ? $no.''.$alasan  : $yes;
        }


        return $result;
    }

    private function getDiagnosa($diagnosa_id)
    {
        $diagnosa = DiagnosaView::find()->andWhere(['diagnosa_id' => $diagnosa_id])
            ->asArray()->one();
        return $diagnosa;
    }

    public function actionGetFormAsmed()
    {
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        $pasienAdmisiId = $request->get('pasienadmisi_id');
        $dataAsmedRanap = AsesmenMedis::find()
            ->select(['asesmenmedis_t.asesmenmedis_id', 'lookup_m.lookup_name AS nama_dokumen', 'asesmenmedis_t.tgl_asesmenmedis AS tanggal',
                'lookup_m.lookup_kode', 'asesmenmedis_t.is_dokumen_eklaim', 'asesmenmedis_t.formasesmen_id',
                new \yii\db\Expression('CASE WHEN pegawai_update.nama_pegawai IS NOT NULL THEN pegawai_update.nama_pegawai ELSE pegawai_m.nama_pegawai END AS user_input')])
            ->join('JOIN', 'lookup_m', 'lookup_m.lookup_id=asesmenmedis_t.formasesmen_id')
            ->join('JOIN', 'loginpemakai_k', 'loginpemakai_k.loginpemakai_id=asesmenmedis_t.created_by')
            ->join('JOIN', 'pegawai_m', 'pegawai_m.pegawai_id=loginpemakai_k.pegawai_id')
            ->join('LEFT JOIN', 'loginpemakai_k user_update', 'user_update.loginpemakai_id=asesmenmedis_t.last_modified_by')
            ->join('LEFT JOIN', 'pegawai_m pegawai_update', 'pegawai_update.pegawai_id=user_update.pegawai_id')
            ->where(['pendaftaran_id' => $pendaftaranId, 'pasienadmisi_id' => $pasienAdmisiId])
            ->asArray()->all();

        $lookup = Lookup::find()
            ->select(['lookup_kode', 'lookup_name'])
            ->where(['lookup_type' => 'form_asmed_ranap'])
            ->orderBy(['lookup_urutan' => SORT_ASC])
            ->all();

        return [
            'lookup' => $lookup,
            'data_asmed' => $dataAsmedRanap,
        ];
    }

    public function actionSaveAsmedRanap()
    {
        $request = Yii::$app->request;
        $data = $request->post();
        $formName = ArrayHelper::getValue($data, 'form_name');
        $postData = ArrayHelper::getValue($data, $formName);
        $asesmenMedisId = ArrayHelper::getValue($postData, 'asesmenmedis_id');
        $model = new AsesmenMedis;
        $model = ($asesmenMedisId) ? $model::findOne($asesmenMedisId) : $model;
        $model->scenario = $model::SCENARIO_SPESIALIS;
        $model->attributes = $postData;
        
        $formAsesmenCode = ArrayHelper::getValue($postData, 'formasesmen_code');
        $formAsesmenId = $this->getFormAsesmenId($formAsesmenCode);
        $groupEmployee = Pegawai::find()
            ->select(['kelompokpegawai_id', 'spesialis_id'])
            ->andWhere(['pegawai_id' => Yii::$app->jwt->user->pegawai_id])
            ->asArray()
            ->one();
        
        $pasienAdmisiId = $model->pasienadmisi_id;
        $kelompokPegawaiId = ArrayHelper::getValue($groupEmployee, 'kelompokpegawai_id');
        if($kelompokPegawaiId == DocoConstants::KELOMPOK_PEGAWAI_DOKTER ){
            $dokterDpjp = Yii::$app->jwt->user->pegawai_id;
        }
        else {
            $dokterDpjp = PasienAdmisi::findOne($pasienAdmisiId);
            $dokterDpjp = ArrayHelper::getValue($dokterDpjp, 'pegawai_id');
        }

        $model->dokter_id = $dokterDpjp;
        $pemeriksaanSpesialis = ArrayHelper::getValue($postData, 'pemeriksaan_spesialis');
        if($pemeriksaanSpesialis) {
            $pemeriksaanSpesialis = json_encode($pemeriksaanSpesialis);
        }

        $model->tgl_asesmenmedis = date('Y-m-d H:i:s');
        $model->pemeriksaan_spesialis = $pemeriksaanSpesialis;
        $model->formasesmen_id = $formAsesmenId;
        if($model->sumber_info) {
            unset($model->sumber_info);
        }
        
        if ($model->validate() && $model->save()) {
            return [
                'status' => 200,
                'message' => 'Data Berhasil di simpan',
                'asesmenmedis_id' => $model->asesmenmedis_id
            ];
        } else {
            $errors = DocoHelpers::parseError($model->errors, $formName);
            return [
                'data' => $errors,
                'status' => 422
            ];
        }
    }

    private function getFormAsesmenId($formAsesmenCode)
    {
        $lookup = Lookup::find()
            ->select(['lookup_id'])
            ->where(['lookup_kode' => $formAsesmenCode])
            ->one();

        return ArrayHelper::getValue($lookup, 'lookup_id');
    }

    public function actionGetDataAsmed()
    {
        $request = Yii::$app->request;
        $asesmenMedisId = $request->get('asesmenmedis_id');
        return AsesmenMedis::findOne($asesmenMedisId);
    }

    public function actionGetDataDokter()
    {
        return Pegawai::find()
            ->select(['pegawai_id', 'nama_pegawai'])
            ->andWhere(['kelompokpegawai_id' => 1, 'is_active' => true])
            ->orderBy(['nama_pegawai' => SORT_ASC])
            ->all();
    }
}
