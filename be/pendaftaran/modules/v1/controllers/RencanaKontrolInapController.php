<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-24 16:56:34
 */

namespace app\modules\v1\controllers;

use Yii;

use app\modules\v1\components\BpjsController;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\RencanaKontrolT;
use app\modules\v1\models\RencanaKontrolView;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\Pegawai;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoMessages;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstansId;
use Picqer\Barcode\BarcodeGeneratorPNG;


class RencanaKontrolInapController extends BpjsController
{
    public $modelClass = 'app\modules\v1\models\RencanaKontrol';

    const LOOKUP_TYPE = 'jenis_rencana';
    const RENCANA_RAWAT_INAP = 1126;
    const JENIS_KONTROL_RANAP = 1;
    const JENIS_KONTROL_RAJAL = 2;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["hapus-data-vclaim"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);


        return $actions;
    }

    /**
     * @todo Action untuk mendapatkan list data rencana kontrol / inap
     * @author Fajar Supriadi <fajar.supriadi@sirs.co.id>
     */
    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new RencanaKontrolView;
            $advancedFilters = $request->get('advanced-filter', []);
            $query = $model::find();
            $tgl_awal = date('Y-m-d 00:00:00');
            $tgl_akhir = date('Y-m-d 23:59:59');

            if (isset($advancedFilters['tgl_rencanakontrol_awal']) && isset($advancedFilters['tgl_rencanakontrol_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_rencanakontrol_awal'];
                $tgl_akhir = $advancedFilters['tgl_rencanakontrol_akhir'];
            }
            if (isset($advancedFilters['nosuratkontrolspri'])) {
                $noSurat = $advancedFilters['nosuratkontrolspri'];
                $query->andWhere(['or',
                    ['nosuratkontrol' => $noSurat],
                    ['no_spri' => $noSurat]
                ]);
                unset($advancedFilters['nosuratkontrolspri']);
            }
            $query->andWhere(['between', 'tgl_rencanakontrol', $tgl_awal, $tgl_akhir]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
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
    
    public function actionAllDokterList()
    {
        $page = Yii::$app->request->get('page', 1);
        $query = Pegawai::find()
            ->select([
                'kode_dokter_bpjs as id',
                'nama_pegawai as text'
            ]);

        $query = $query->where(['kelompokpegawai_id' => '1'])
            ->andWhere(['NOT', ['kode_dokter_bpjs' => null]])
            ->orderBy(['nama_pegawai' => SORT_ASC]);

        $term = Yii::$app->request->get('term');
        if (!empty($term)) {
            $query = $query->andWhere([
                'like',
                'LOWER(nama_pegawai)',
                strtolower($term)
            ]);
        }

        return $query
            ->limit(11)
            ->offset(($page - 1) * 10)
            ->asArray()
            ->all();
    }

    /**
     * @todo Action untuk mendapatkan list filter informasi rencana kontrol/inap
     * @author Fajar Supriadi <fajar.supriadi@sirs.co.id>
     */
    public function actionGetPack()
    {
        $jenis_rencana = $this->getLookup()->select([
            'lookup_id',
            'lookup_name'
        ])->where(['lookup_type' => self::LOOKUP_TYPE])
        ->asArray()->all();

        return [
            'jenis_rencana_list' => $jenis_rencana,
        ];
    }

    private function getLookup() {
        $query = Lookup::find();

        return $query;
    }

    public function actionGetDataPeserta()
    {

        $request = Yii::$app->request;
        $post = $request->post();
        if ($post['jenis_rencana'] == '1') {

            $nosep = trim($post['nosep']);
            $model_bpjs = Bpjs::find()->where(['nosep' => $nosep, 'is_deleted' => false, 'is_active' => true])->asArray()->one();
        } else {

            $no_kartu = trim($post['no_kartu']);
            $model_bpjs = Bpjs::find()->where(['nokartuasuransi' => $no_kartu , 'is_deleted' => false, 'is_active' => true])->asArray()->one();
        }

        $model_pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $model_bpjs['pendaftaran_id']])->asArray()->one();

        $data = [
            'bpjs' => $model_bpjs,
            'peserta' => $model_pendaftaran
        ];
        return $data;
    }

    public function actionCreate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $post = Yii::$app->request->post();
            $bpjsForm = $post['RencanaKontrolForm'];
            $model = new Bpjs;
            $rencana_kontrol = new RencanaKontrolT;
            $t_rencanakontrol = [];
            $t_rencanakontrol['jenis_rencana'] = ($post['jenis_rencana'] == 1) ? DocoConstants::STS_RECANA_KONTROL : DocoConstants::STS_RENCAN_INAP;
            $t_rencanakontrol['no_sep'] = $bpjsForm['no_sep'];
            $t_rencanakontrol['bpjs_id'] = $bpjsForm['bpjs_id'];
            $t_rencanakontrol['pendaftaran_id'] = $bpjsForm['pendaftaran_id'];
            $t_rencanakontrol['tgl_rencana_inap'] = isset($bpjsForm['tgl_rencana_inap']) ? $bpjsForm['tgl_rencana_inap'] : null;
            $t_rencanakontrol['no_kartu'] = $bpjsForm['no_kartu'];
            $t_rencanakontrol['tgl_rencanakontrol'] = date('Y-m-d',strtotime($bpjsForm['tgl_rencanakontrol']));
            $t_rencanakontrol['jenis_pelayanan'] = $bpjsForm['jenis_pelayanan'];
            $t_rencanakontrol['nama_spesialis'] = $bpjsForm['nama_spesialis'];
            $t_rencanakontrol['kode_poli'] = $bpjsForm['kode_poli'];
            $t_rencanakontrol['dokterdpjp_kode'] = $bpjsForm['dokterdpjp_kode'];
            $t_rencanakontrol['dokterdpjp_nama'] = $bpjsForm['dokterdpjp_nama'];
            $t_rencanakontrol['konsulpoli_id'] = ArrayHelper::getValue($bpjsForm, 'konsulpoli_id');
            $t_rencanakontrol['user'] = $post['user'];

            $model->t_rencanakontrol = $t_rencanakontrol;
            if ($post['jenis_rencana'] == 1) {
                $result = $model->createRencanaKontrol();
            }else {
                $result = $model->createRencanaInap();
            }
            if (($result['metaData']['code'] == 200) || ( $result['metaData']['code'] == '200')) {

                $rencana_kontrol->pendaftaran_id = ArrayHelper::getValue($t_rencanakontrol,'pendaftaran_id',null);
                $rencana_kontrol->bpjs_id = ArrayHelper::getValue($t_rencanakontrol,'bpjs_id',null);
                $rencana_kontrol->jenis_rencana = (string) ArrayHelper::getValue($t_rencanakontrol,'jenis_rencana',null);
                $rencana_kontrol->no_sep =  ArrayHelper::getValue($t_rencanakontrol,'no_sep',null);
                $rencana_kontrol->nama_spesialis =  ArrayHelper::getValue($t_rencanakontrol,'nama_spesialis',null);
                $rencana_kontrol->kode_poli =  ArrayHelper::getValue($t_rencanakontrol,'kode_poli',null);
                $rencana_kontrol->dokterdpjp_kode =  ArrayHelper::getValue($t_rencanakontrol,'dokterdpjp_kode',null);
                $rencana_kontrol->dokterdpjp_nama =  ArrayHelper::getValue($t_rencanakontrol,'dokterdpjp_nama',null);
                $rencana_kontrol->jenis_pelayanan =  ArrayHelper::getValue($t_rencanakontrol,'jenis_pelayanan',null);
                $additional_data = [
                    'payload' => $t_rencanakontrol,
                    'response' => ArrayHelper::getValue($result,'response',null),
                ];
                $rencana_kontrol->additional_data = json_encode($additional_data);
                $rencana_kontrol->nosuratkontrol =  ArrayHelper::getValue( $result['response'],'noSuratKontrol',null);
                $rencana_kontrol->nama = ArrayHelper::getValue( $result['response'],'nama',null);
                $rencana_kontrol->no_kartu = ArrayHelper::getValue( $result['response'],'noKartu',null);
                $rencana_kontrol->no_spri = ArrayHelper::getValue( $result['response'],'noSPRI',null);
                $rencana_kontrol->tgl_rencanakontrol = ArrayHelper::getValue( $result['response'],'tglRencanaKontrol',null);
                $rencana_kontrol->konsulpoli_id = ArrayHelper::getValue($t_rencanakontrol, 'konsulpoli_id');
                if($rencana_kontrol->validate()) {
                    if ($rencana_kontrol->save()) {
                        $transaction->commit();
                        $result ['message'] = 'Data Berhasil di simpan';
                    }
                } else {
                    $errors = DocoHelpers::parseError($rencana_kontrol->errors,'RencanaKontrolT');
                    return [
                        'data' => $errors,
                        'status' => 422,
                        'result' => $result,
                        'model' => $rencana_kontrol,
                    ];
                }
            }
            return [
                'result' => $result,
                'model' => $rencana_kontrol
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetDataUpdate()
    {
        $request = Yii::$app->request;
        $rencanakontrol_id = $request->get('rencanakontrol_id', null);

        $data_kontrol = RencanaKontrolT::find()
        ->where(['rencanakontrol_id' => $rencanakontrol_id])
        ->asArray()->one();

        $data_pasien = PasienV::find()->select([
            'no_rekam_medik',
            'nama_pasien'
        ])->where(['nopeserta_bpjs' => $data_kontrol['no_kartu']])
        ->orderBy(['pasien_id' => SORT_DESC])
        ->asArray()->one();

        return [
            'data_kontrol' => $data_kontrol,
            'data_pasien' => $data_pasien,
        ];
    }

    public function actionUpdateRencanaKontrol()
    {
        $request = Yii::$app->request;
        $model = new RencanaKontrolT;
        $old_rencanakontrol_id = $request->get('rencanakontrol_id', null);
        $bpjsRes = [];

        if (!$old_rencanakontrol_id) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => 'Data tidak ditemukan!',
            ];
        }

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model->attributes = $request->post();

            $model->tgl_rencanakontrol = date('Y-m-d', strtotime($model->tgl_rencanakontrol));

            $bpjsRes = $this->updateRencanaKontrol($model);
            $result = $bpjsRes['results'];
            if (isset($result['metaData']['code']) && $result['metaData']['code'] == 200) {
                $additional_data = [
                    'payload' => $bpjsRes['payload'],
                    'response' => $result['response'],
                ];

                $model->additional_data = json_encode($additional_data);
            } else {
                return [
                    'status' => 422,
                    'title' => 'Proses BPJS Gagal!',
                    'text' => $result['metaData']['message'],
                ];
            }

            if (!empty($model->pendaftaran_id)) {
                if ($model->save()) {
                    $oldRencanaKontrol = RencanaKontrolT::findOne($old_rencanakontrol_id);
                    if ($oldRencanaKontrol->delete()) {
                        $transaction->commit();

                        return [
                            'rencanakontrol_id' => $model->rencanakontrol_id,
                            'message' => 'Data Berhasil di simpan.',
                            'id' => DocoHelpers::encrypt($model->rencanakontrol_id)
                        ];
                    } else {
                        $transaction->rollBack();
                        return [
                            'status' => 422,
                            'title' => 'Proses Gagal!',
                            'data' => $oldRencanaKontrol->errors,
                        ];
                    }
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'RencanaKontrolForm');
                    $transaction->rollBack();

                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'data' => $errors,
                    ];
                }
            } else {
                return [
                        'rencanakontrol_id' => $model->rencanakontrol_id,
                        'no_surat_kontrol' => $model->nosuratkontrol,
                        'no_kartu' => $model->no_kartu,
                        'message' => 'Data Berhasil di update.',
                        'id' => null
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

    private function updateRencanaKontrol($payload)
    {
        $model = new Bpjs;
        $user = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user->nama_pemakai : null;
        $payloadBpjs = [
            'nosuratkontrol' => $payload['nosuratkontrol'],
            'no_spri' => $payload['no_spri'],
            'no_sep' => $payload['no_sep'],
            'dokterdpjp_kode' => $payload['dokterdpjp_kode'],
            'kode_poli' => $payload['kode_poli'],
            'tgl_rencanakontrol' => date('Y-m-d', strtotime($payload['tgl_rencanakontrol'])),
            'user' => $user,
        ];

        $model->t_rencanakontrol = $payloadBpjs;
        if ($payload['jenis_rencana'] == DocoConstants::STS_RECANA_KONTROL) {
            $results = $model->updateRencanaKontrol();
        }else {
            $results = $model->updateRencanaInap();
        }

        return [
            'payload' => $payloadBpjs,
            'results' => $results,
            'data' => $payload
        ];
    }

    /**
     * @controller actionPrintRencana
     * @attribute #no_rencana# => No Rencana
     * @attribute #kepada_yth# => Kepada YTH
     * @attribute #no_kartu# => No Kartu Peserta
     * @attribute #judul# => Judul
     * @attribute #nama_peserta# => Nama Peserta
     * @attribute #tgl_lahir# => Tanggal Lahir
     * @attribute #diagnosa# => Diagnosa
     * @attribute #tgl_rencana_kontrol# => Tanggal Rencana Kontrol
     * @attribute #tgl_rencana_kontrol# => Tanggal Cetak
     * @attribute #tgl_entri# => Tanggal Entri
     * @attribute #sub_spesialis# => Sub Spesialis
     * @attribute #jenis_kelamin# => Jenis Kelamin
     * @attribute #label_rencana_tgl# => Label Rencana
     * @attribute #dokter_dpjp# => Dokter DPJP
     * @attribute #ttd_dokter# => Tanda Tangan Dokter DPJP
     **/
    public function actionPrintRencana()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '360');
        try{
            $request = Yii::$app->request;
            $model = new RencanaKontrolView;
            $rencanakontrol_id = $request->get('rencanakontrol_id', null);
            $ruangan_id = $request->get('ruangan_id', null);
            $show_cetak = (new DocoConstansId)->actionGetAdditional('show_diagnosa_cetakan_spri');
            $jenisRencana = $request->get('jenis_rencana', null);
            $whereCond = [
                'rencanakontrol_id' => $rencanakontrol_id
            ];

            if (!empty($jenisRencana)) {
                $whereCond['jenis_rencana'] = $jenisRencana;
            }

            $rencana = $model::find()
            ->where($whereCond);

            $rencana = $rencana->one();

            $judul = 'RENCANA KONTROL';
            $no_rencana = '';
            $label_rencana_tgl = 'Rencana Kontrol';
            if ($rencana['jenis_rencana'] == self::RENCANA_RAWAT_INAP){
                $judul = 'PERINTAH RAWAT INAP';
                $no_rencana = isset($rencana['no_spri']) ? $rencana['no_spri'] : '';
                $label_rencana_tgl = 'Rencana Inap';
            } else {
                $no_rencana = isset($rencana['nosuratkontrol']) ? $rencana['nosuratkontrol'] : '';
            }


            $kepada_yth = isset($rencana['dokterdpjp_nama']) ? $rencana['dokterdpjp_nama'] : '';
            $nama_peserta = isset($rencana['nama']) ? $rencana['nama'] : '';
            $no_kartu = isset($rencana['no_kartu']) ? $rencana['no_kartu'] : '';
            $tgl_entri = isset($rencana['created_date']) ? $rencana['created_date'] : '';
            $tgl_cetak = date('Y-m-d H:i:s');

            $additional_data = isset($rencana['additional_data']) ? $this->stdToArray(json_decode($rencana['additional_data'])) : '';
            $tgl_lahir = isset($additional_data['response']['tglLahir']) ? DocoHelpers::getTanggalIndonesia($additional_data['response']['tglLahir']) : '';
            if (!empty($tgl_lahir)){
                $tgl_lahir = $tgl_lahir['tanggal'].' '.$tgl_lahir['bulan'].' '.$tgl_lahir['tahun'];
            }

            $tgl_rencana_kontrol = isset($additional_data['response']['tglRencanaKontrol']) ? DocoHelpers::getTanggalIndonesia($additional_data['response']['tglRencanaKontrol']) : '';
            if (!empty($tgl_rencana_kontrol)){
                $tgl_rencana_kontrol = $tgl_rencana_kontrol['tanggal'].' '.$tgl_rencana_kontrol['bulan'].' '.$tgl_rencana_kontrol['tahun'];
            }
            $sub_spesialis = isset($rencana['nama_spesialis']) ? $rencana['nama_spesialis'] : '';

            $jenis_kelamin = (strtolower($additional_data['response']['kelamin']) == 'p' || strtolower($additional_data['response']['kelamin'])=='perempuan') ? 'Perempuan' : 'Laki-laki';

            $diagnosa_nama = '';
            if (!empty($rencana['bpjs_id'])){
                $bpjs = new Bpjs;
                $data_bpjs = $bpjs::find()
                ->where(['bpjs_id' => $rencana['bpjs_id']])->one();

                if (!empty($data_bpjs['diagnosaawal'])){
                    $diagnosa =  $bpjs->referensiDiagnosa($data_bpjs['diagnosaawal']);
                    $diagnosa_nama = $diagnosa['response']['diagnosa'][0]['nama'];
                }

            } else {
                $diagnosa_nama = ArrayHelper::getValue($additional_data,'response.namaDiagnosa');
            }
            $print = new DocoPrint();
            if ($rencana['jenis_rencana'] != self::RENCANA_RAWAT_INAP && isset($rencana->pendaftaran_id)){
                $pendaftaran = Pendaftaran::find()
                    ->select([
                        'pasien_m.nama_pasien',
                        'pasien_m.no_rekam_medik'
                    ])
                    ->join("JOIN", "pasien_m", "pendaftaran_t.pasien_id = pasien_m.pasien_id")
                    ->where(['pendaftaran_id' => $rencana->pendaftaran_id])->asArray()->one();
    
                    if (! empty($pendaftaran)) {
                        $filename = $pendaftaran['nama_pasien']."_".$rencana['nosuratkontrol'].'_'.$pendaftaran['no_rekam_medik'].'.pdf';
                        $print->docName = $filename;
                        $print->SetTitle($filename);
                    }
            }

            $dokter_dpjp = ArrayHelper::getValue($rencana,'dokterdpjp_nama');
            $barcode = '';
            $barcodeGen = new BarcodeGeneratorPNG;
            if($no_rencana) {
                $barcode_value = base64_encode($barcodeGen->getBarcode($no_rencana, $barcodeGen::TYPE_CODE_128));
                $barcode = '<img style="height: 20px; margin-left: 2px" src="data:image/png;base64,' . $barcode_value . '">';
            }
            $dokterdpjp_kode = ArrayHelper::getValue($rencana,'dokterdpjp_kode');
            $signaturePath = Pegawai::signatureEmployee('kode_dokter_bpjs', $dokterdpjp_kode);
            
            error_reporting(0);
            $print->useSubstitutions=false;
            $print->simpleTables = true;
            $print->attributes = [
                '#no_rencana#' => $no_rencana,
                '#kepada_yth#' => $kepada_yth,
                '#no_kartu#' => $no_kartu,
                '#nama_peserta#' => $nama_peserta,
                '#tgl_lahir#' => $tgl_lahir,
                '#diagnosa#' => $show_cetak=='true'? $diagnosa_nama:'',
                '#tgl_rencana_kontrol#' => $tgl_rencana_kontrol,
                '#judul#' => $judul,
                '#tgl_cetak#' => $tgl_cetak,
                '#sub_spesialis#' => $sub_spesialis,
                '#jenis_kelamin#' => $jenis_kelamin,
                '#tgl_entri#' => $tgl_entri,
                '#label_rencana_tgl#' => $label_rencana_tgl,
                '#dokter_dpjp#' => $dokter_dpjp,
                '#barcode#' => $barcode,
                '#ttd_dokter#' => $signaturePath
            ];
            $print->Output();
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
     * @controller actionPrintRencanaVclaim
     * @attribute #no_rencana# => No Rencana
     * @attribute #kepada_yth# => Kepada YTH
     * @attribute #no_kartu# => No Kartu Peserta
     * @attribute #judul# => Judul
     * @attribute #nama_peserta# => Nama Peserta
     * @attribute #tgl_lahir# => Tanggal Lahir
     * @attribute #diagnosa# => Diagnosa
     * @attribute #tgl_rencana_kontrol# => Tanggal Rencana Kontrol
     * @attribute #tgl_rencana_kontrol# => Tanggal Cetak
     * @attribute #tgl_entri# => Tanggal Entri
     * @attribute #sub_spesialis# => Sub Spesialis
     * @attribute #jenis_kelamin# => Jenis Kelamin
     * @attribute #label_rencana_tgl# => Label Rencana
     * @attribute #dokter_dpjp# => Dokter DPJP
     * @attribute #ttd_dokter# => Tanda Tangan Dokter DPJP
     **/
    public function actionPrintRencanaVclaim()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '360');
        try{
            $request = Yii::$app->request;
            $jnsKontrol = $request->get('jnsKontrol', null);
            $noSuratKontrol = $request->get('noSuratKontrol', null);
            $kodeDokter = $request->get('kodeDokter', null);
            $namaDokter = $request->get('namaDokter', null);
            $tglRencanaKontrol = $request->get('tglRencanaKontrol', null);
            if (!empty($tglRencanaKontrol)){
                $tglRencanaKontrol = DocoHelpers::getTanggalIndonesia($tglRencanaKontrol);
                $tglRencanaKontrol = $tglRencanaKontrol['tanggal'].' '.$tglRencanaKontrol['bulan'].' '.$tglRencanaKontrol['tahun'];
            }
            $sep = $request->get('sep', null);
            $diagnosa = isset($sep['diagnosa']) ? $sep['diagnosa'] : '-';
            $noKartu = isset($sep['peserta']['noKartu']) ? $sep['peserta']['noKartu'] : '-';
            $nama_peserta = isset($sep['peserta']['nama']) ? $sep['peserta']['nama'] : '-';
            $tglLahir = isset($sep['peserta']['tglLahir']) ? DocoHelpers::getTanggalIndonesia($sep['peserta']['tglLahir']) : null;
            if (!empty($tglLahir)){
                $tglLahir = $tglLahir['tanggal'].' '.$tglLahir['bulan'].' '.$tglLahir['tahun'];
            }
            $kelamin = isset($sep['peserta']['kelamin']) ? $sep['peserta']['kelamin'] : '-';
            $jenis_kelamin = (strtolower($kelamin) == 'p') ? 'Perempuan' : 'Laki-laki';
            $tgl_cetak = date('Y-m-d H:i:s');

            $judul = 'RENCANA KONTROL';
            $label_rencana_tgl = 'Rencana Kontrol';
            if ($jnsKontrol == self::JENIS_KONTROL_RANAP){
                $judul = 'PERINTAH RAWAT INAP';
                $label_rencana_tgl = 'Rencana Inap';
            }

            $barcode = '';
            $barcodeGen = new BarcodeGeneratorPNG;
            if($noSuratKontrol) {
                $barcode_value = base64_encode($barcodeGen->getBarcode($noSuratKontrol, $barcodeGen::TYPE_CODE_128));
                $barcode = '<img style="height: 20px; margin-left: 2px" src="data:image/png;base64,' . $barcode_value . '">';
            }
            $signaturePath = Pegawai::signatureEmployee('kode_dokter_bpjs', $kodeDokter);

            $print = new DocoPrint();
            error_reporting(0);
            $print->useSubstitutions=false;
            $print->simpleTables = true;
            $print->attributes = [
                '#no_rencana#' => $noSuratKontrol,
                '#kepada_yth#' => $namaDokter,
                '#no_kartu#' => $noKartu,
                '#nama_peserta#' => $nama_peserta,
                '#tgl_lahir#' => $tglLahir,
                '#diagnosa#' => $diagnosa,
                '#tgl_rencana_kontrol#' => $tglRencanaKontrol,
                '#judul#' => $judul,
                '#tgl_cetak#' => $tgl_cetak,
                '#sub_spesialis#' => '-',
                '#jenis_kelamin#' => $jenis_kelamin,
                '#tgl_entri#' => '-',
                '#label_rencana_tgl#' => $label_rencana_tgl,
                '#dokter_dpjp#' => $namaDokter,
                '#barcode#' => $barcode,
                '#ttd_dokter#' => $signaturePath
            ];
            $print->Output();
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    protected function stdToArray($obj){
        $reaged = (array)$obj;
        foreach($reaged as $key => &$field){
            if(is_object($field))$field = $this->stdToArray($field);
        }
        return $reaged;
    }

    public function actionGetInfoPeserta($rencanakontrol_id = null)
    {
        try {
            $request = Yii::$app->request;
            $model = new RencanaKontrolView;
            $rencanakontrol_id_get = $request->get('rencanakontrol_id', null);
            $set_rencana_kontrol_id = null;
            if (!is_null($rencanakontrol_id_get)) {
                # code...
                $set_rencana_kontrol_id = $rencanakontrol_id_get;
            } else {
                $set_rencana_kontrol_id = $rencanakontrol_id;
            }
            $rencana = $model::find()
            ->where(['rencanakontrol_id' => $set_rencana_kontrol_id]);

            $data = $rencana->asArray()->one();
            $can_delete = true;
            if ($data['jenis_rencana'] == self::RENCANA_RAWAT_INAP) {
                $can_delete = false;
            }
            $response = [
                            'data' => $data,
                            'can_delete' => $can_delete,
                            'Error' => '',
                        ];

            return  $response;
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }

    }

    public function actionHapus()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $model = new Bpjs;
        $result =[];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $check = $jwt->katakunci_pemakai;
            if ($request->post('bypass')) {
                $valid = true;
            } else {
                $valid = Yii::$app->security->validatePassword($request->post('password',''), $check);
            }

            if (!$valid) {
                $transaction->rollBack();
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Password salah.'
                ]);
            }

            if (ArrayHelper::getValue($post, 'is_from_vclaim', false) == true) {
                // untuk case hapus no surat kontrol dari vclaim
                $model->t_rencanakontrol = [
                    'user' => ArrayHelper::getValue($post, 'username'),
                    'nosuratkontrol' => ArrayHelper::getValue($post, 'noSuratKontrol')
                ];
                $result = $model->hapusRencanaKontrol();
                return [
                    'result' => $result,
                ];

            } else {
                $response = $this->actionGetInfoPeserta($post['rencanakontrol_id']);
                $data = $response['data'];
            }


            $t_rencanakontrol = [];
            $t_rencanakontrol['user'] = $post['username'];
            if ($data['jenis_rencana'] == self::RENCANA_RAWAT_INAP) {
                // Delete SPRI sudah ada
                $t_rencanakontrol['nosuratkontrol'] = $data['no_spri'];
                $model->t_rencanakontrol = $t_rencanakontrol;
                if ($t_rencanakontrol['nosuratkontrol'] != null) {
                    $result = $model->hapusRencanaKontrol();
                }else {
                    $transaction->rollBack();
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => 'Data tidak memiliki SPRI.'
                    ]);
                }
            }else {
                $t_rencanakontrol['nosuratkontrol'] = $data['nosuratkontrol'];
                $model->t_rencanakontrol = $t_rencanakontrol;
                if ($t_rencanakontrol['nosuratkontrol'] != null) {
                    $result = $model->hapusRencanaKontrol();
                }else {
                    $transaction->rollBack();
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => 'Data tidak memiliki No Surat Kontrol.'
                    ]);
                }
            }

            $rencana_kontrol = RencanaKontrolT::find()
            ->where(['rencanakontrol_id' => $post['rencanakontrol_id']])
            ->one();
            if (ArrayHelper::getValue($result, 'metaData.code', 500) == 200) {
                if ($rencana_kontrol->delete()) {
                        $transaction->commit();
                        $result ['message'] = 'Data Berhasil di simpan';
                } else {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($rencana_kontrol->errors,'RencanaKontrolT');
                    return [
                        'data' => $errors,
                        'status' => 422,
                        'result' => $result,
                        'model' => $rencana_kontrol,
                    ];
                }
            } else {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Berdasarkan BPJS '.ArrayHelper::getValue($result, 'metaData.message'),
                ]);
            }
            return [
                'result' => $result,
                'model' => $rencana_kontrol
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionHapusDataVclaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $noSuratKontrol = ArrayHelper::getValue($post, 'noSuratKontrol');
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $model = new Bpjs;
        $result = [];
        try {
            $check = $jwt->katakunci_pemakai;
            $valid = Yii::$app->security->validatePassword($request->post('password',null), $check);
            if (!$valid) {
                return $this->helper->response([
                    'title' => 'Proses Gagal!',
                    'text' => 'Password salah.',
                ], 422);
            }

            $isExist = $this->validateDataVclaim($noSuratKontrol);
            if ($isExist) {
                return $this->helper->response([
                    'title' => 'Proses Gagal!',
                    'text' => 'Data sudah terdaftar di sistem. Silahkan menggunakan fitur hapus pada menu Rencana Kontrol/Inap.',
                ], 422);
            }

            $t_rencanakontrol['nosuratkontrol'] = $noSuratKontrol;
            $t_rencanakontrol['user'] = ArrayHelper::getValue($post, 'username');
            $model->t_rencanakontrol = $t_rencanakontrol;
            $result = $model->hapusRencanaKontrol();

            $responseCode = ArrayHelper::getValue($result['metaData'], 'code', 422);
            if ((int) $responseCode > 200) {
                return $this->helper->response([
                    'title' => 'Proses BPJS Gagal!',
                    'text' => ArrayHelper::getValue($result['metaData'], 'message', ''),
                ], $responseCode);
            }

            return $this->helper->response($result);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    private function validateDataVclaim($noSuratKontrol)
    {
        $result = false;
        $data = RencanaKontrolView::find()
        ->where(['or',
            ['nosuratkontrol' => $noSuratKontrol],
            ['no_spri' => $noSuratKontrol]
        ])->asArray()->one();
        if (!empty($data)) $result = true;

        return $result;
    }

    public function actionGetDataPasienPeserta()
    {
        $request = Yii::$app->request;
        $no_kartu = $request->get('no_kartu', null);

        $data_pasien = PasienV::find()->select([
            'no_rekam_medik',
            'nama_pasien'
        ])->where(['nopeserta_bpjs' => $no_kartu])
        ->orderBy(['pasien_id' => SORT_DESC])
        ->asArray()->one();

        return [
            'data_pasien' => $data_pasien,
        ];
    }
}
