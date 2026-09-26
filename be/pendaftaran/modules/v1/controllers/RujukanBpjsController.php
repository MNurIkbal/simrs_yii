<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-24 16:56:34
 */

namespace app\modules\v1\controllers;

use Yii;

use app\modules\v1\components\BpjsController;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\RujukanBpjs;
use app\modules\v1\models\RujukanBpjsView;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoMessages;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class RujukanBpjsController extends BpjsController
{
    public $modelClass = 'app\modules\v1\models\RujukanBpjs';

    public function verbs()
    {
        $verbs = parent::verbs();
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
     * @todo Action untuk mendapatkan list data rujukan bpjs
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new RujukanBpjsView;
            $advancedFilters = $request->get('advanced-filter', []);
            $query = $model::find();

            if (isset($advancedFilters['tanggal_rujukan_awal']) && isset($advancedFilters['tanggal_rujukan_akhir'])) {
                $tgl_awal = $advancedFilters['tanggal_rujukan_awal'];
                $tgl_akhir = $advancedFilters['tanggal_rujukan_akhir'];

                $query->andWhere(['between', 'tanggal_rujukan', $tgl_awal, $tgl_akhir]);
            } else {
                $query->andWhere(['tanggal_rujukan' => date('Y-m-d', strtotime('NOW'))]);
            }

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

    /**
     * @todo Action untuk create rujukan bpjs
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCreate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $model = new RujukanBpjs;

            if ($request->post()) {
                $model->attributes = $request->post();
                $responseRujukan = $this->saveRujukan($request->post('nosep'), $model);

                if (!isset($responseRujukan['data']['rujukan'])) {
                    // \Yii::$app->response->statusCode = 500;
                    $transaction->rollback();
                    return $responseRujukan;
                    // return [
                    //     'message' => Yii::t('app', 'Gagal membuat rujukan di service vclaim')
                    // ];
                }

                $rujukan = $responseRujukan['data']['rujukan'];
                $model->no_rujukan = $rujukan['noRujukan'];
                $model->diagnosa_rujukan_nama = $rujukan['diagnosa']['nama'];
                $model->additional_data = json_encode($responseRujukan['data']);
                $model->additional_request = json_encode($responseRujukan['request']);

                if ($model->save()) {
                    
                    $transaction->commit();
                    return [
                        'rujukanbpjs_id' => $model->rujukanbpjs_id,
                        'bpjs_id' => $model->bpjs_id,
                        'message' => 'Data Berhasil di simpan.'
                    ];
                } else {
                    $transaction->rollback();
                    $errors = DocoHelpers::parseError($model->errors, 'RujukanBpjsForm');

                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi untuk mendapatkan data options
     * @return array
     */
    public function actionGetDataOptions()
    {
        $tipeRujukan = Lookup::find()->where([
            'lookup_type' => 'rujukan'
        ])->all();

        $jenisPelayanan = Lookup::find()->where([
            'lookup_type' => 'jenis_pelayanan_bpjs'
        ])->all();

        return [
            'tipeRujukan' => Arrayhelper::map($tipeRujukan, 'lookup_value', 'lookup_name'),
            'jenisPelayanan' => Arrayhelper::map($jenisPelayanan, 'lookup_value', 'lookup_name')
        ];
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi untuk mendapatkan data pasien by nomor sep
     * @return array
     */
    public function actionGetPasienBySep($nosep)
    {
        $modelBpjs = new Bpjs;
        $bpjs = $modelBpjs->find()->where(['nosep' => $nosep, 'is_active' => true, 'is_deleted' => false])->one();
        $sep = $modelBpjs->referensiCariSep($nosep);
        if ($sep['metaData']['code'] != 200 || $sep['metaData']['code'] != '200') {
            \Yii::$app->response->statusCode = 201;
            return [
                'message' => 'Pencarian SEP vclaim '.$sep['metaData']['message'],
                'data' => [
                    'message' =>  'Pencarian SEP vclaim '.$sep['metaData']['message']
                ]
            ];
        } else {
            $sep = $sep['response'];
        }

        // $diagnosa = $modelBpjs->referensiDiagnosa($sep['diagnosa']);

        // if ($diagnosa['metaData']['code'] == 200 && isset($diagnosa['response']['diagnosa'][0]['nama'])) {
        //     $sep['diagnosa'] = $diagnosa['response']['diagnosa'][0]['nama'];
        // }

        /** 
         * @todo 
         * cari api dari bpjs untuk get perujuk nya (karena ketika rujukan penuh tidak bisa milih mau dirujuk kemana)
         * kondisi skrg sebnernya sudah benar cari nya by rujukan tpi ada case ketika dia tidak dibuat rujukannya 
        */
        $peserta = $modelBpjs->peserta($sep['peserta']['noKartu'], $sep['tglSep']);
        // $peserta = $modelBpjs->cariRujukanPeserta($sep['peserta']['noKartu']);

        if ($peserta['metaData']['code'] != 200 || $peserta['metaData']['code'] != '200') {
            \Yii::$app->response->statusCode = 201;
            return [
                'message' =>  'Pencarian peserta vclaim '.$peserta['metaData']['message'],
                'data' => [
                    'message' =>  'Pencarian peserta vclaim '.$peserta['metaData']['message']
                ]
            ];
        }

        $cekRujukan = (new \yii\db\Query())
        ->select([
            'bpjs.pendaftaran_id',
            'bpjs.pasienadmisi_id',
            'bpjs.bpjs_id',
            'bpjs.nosep'
        ])
        ->from('rujukanbpjs_t rujukan')
        ->join('JOIN', 'bpjs_t bpjs', 'bpjs.bpjs_id = rujukan.bpjs_id')
        ->where(['bpjs.nosep' => $nosep])
        ->andWhere(['rujukan.is_deleted' => false])
        ->one();

        if ($cekRujukan) {
            \Yii::$app->response->statusCode = 201;

            return [
                'message' => 'SEP Sudah dibuatkan rujukannya.',
                'data' => [
                    'message' => 'SEP Sudah dibuatkan rujukannya.'
                ]
            ];
        }
        $diagnosaKode = ArrayHelper::getValue($peserta, 'response.rujukan.diagnosa.kode');
        $diagnosaNama = ArrayHelper::getValue($peserta, 'response.rujukan.diagnosa.nama');
        // $sep['provUmum']['kdProvider'] = ArrayHelper::getValue($peserta, 'response.rujukan.provPerujuk.kode');
        // $sep['provUmum']['nmProvider'] = ArrayHelper::getValue($peserta, 'response.rujukan.provPerujuk.nama');
        $sep['provUmum']['kdProvider'] = ArrayHelper::getValue($peserta, 'response.peserta.provUmum.kdProvider');
        $sep['provUmum']['nmProvider'] = ArrayHelper::getValue($peserta, 'response.peserta.provUmum.nmProvider');
        $sep['diagnosa'] = ArrayHelper::getValue($sep, 'diagnosa');
        $sep['poli'] = ArrayHelper::getValue($peserta, 'response.rujukan.poliRujukan.kode');
        $sep['poliNama'] = ArrayHelper::getValue($peserta, 'response.rujukan.poliRujukan.nama');
        $sep['pendaftaran_id'] = isset($bpjs['pendaftaran_id']) ? $bpjs['pendaftaran_id'] : null;
        $sep['pasienadmisi_id'] = isset($bpjs['pasienadmisi_id']) ? $bpjs['pasienadmisi_id'] : null;
        $sep['bpjs_id'] = isset($bpjs['bpjs_id']) ? $bpjs['bpjs_id'] : null;

        return $sep;
    }

    /**
     * @controller actionPrintRujukan
     * @attribute #rujukan# => Layout Cetak Rujukan
     *
     **/
    public function actionPrintRujukan()
    {
        try {
            $modelRujukan = new RujukanBpjsView;
            $modelBpjs = new Bpjs;
            $request = Yii::$app->request;
            $rujukanbpjs_id = $request->get('rujukanbpjs_id');
            $tglsep = $request->get('tglsep');

            $rujukan = $modelRujukan::find()->where(['rujukanbpjs_id' => $rujukanbpjs_id])->one();
            $additional_data = $rujukan->additional_data;
            $additional_data = json_decode($additional_data, true);

            if (isset($additional_data['rujukan']['peserta'])) {
                $peserta = $additional_data['rujukan']['peserta'];

                if (isset($peserta['tglLahir'])) {
                    $peserta['tglLahir'] = DocoHelpers::convDateTime($peserta['tglLahir'].' 00:00:00', false, false);
                }

                $tgl_rencana_kunjungan = DocoHelpers::convDateTime($additional_data['rujukan']['tglRencanaKunjungan'].' 00:00:00', false, false);
                $tgl_berlaku = DocoHelpers::convDateTime($additional_data['rujukan']['tglBerlakuKunjungan'].' 00:00:00', false, false);
                $nama_poli = ArrayHelper::getValue($additional_data, 'rujukan.poliTujuan.nama');
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#rujukan#' => $this->renderPartial('rujukan', get_defined_vars()),
                '#no_rujukan#' => isset($rujukan['no_rujukan']) ? $rujukan['no_rujukan'] : '-',
                '#tanggal_rujukan#' => isset($rujukan['tanggal_rujukan']) ? DocoHelpers::convDateTime($rujukan['tanggal_rujukan'], false, false) : '-'
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

    /**
     * @method cari rujukan by nomor rujukan 
     *
     * @param string $param
     * @return array
     * 
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function actionCariRujukan($noRujukan)
    {
        $modelBpjs = new Bpjs;
        return $modelBpjs->cariRujukan($noRujukan);
    }

    /**
     * @method cari rujukan by nomor peserta 
     *
     * @param string $param
     * @param bool $list
     * @return array
     * 
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function actionCariRujukanPeserta($noKartu, $list = false, $type = 1)
    {
        $modelBpjs = new Bpjs;
        return $modelBpjs->cariRujukanPeserta($noKartu, $list, $type);
    }

    /**
     * @method delete rujukan
     * 
     * @param string $norujukan
     * @param int $rujukanbpjs_id
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function actionHapusRujukan()
    {
        $request = Yii::$app->request;
        $noRujukan = $request->get('noRujukan', null);
        $rujukanbpjs_id = $request->get('rujukanbpjs_id', null);
        $post = $request->post();

        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;

        $check = $jwt->katakunci_pemakai;
        $valid = Yii::$app->security->validatePassword($post['password'], $check);
        if (!$valid) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Password salah.'
            ]);
        }

        $modelBpjs = new Bpjs;
        $model = RujukanBpjs::find()->andWhere(['or',
                ['no_rujukan' => $noRujukan],
                ['rujukanbpjs_id' => $rujukanbpjs_id]
            ])->one();

        if(empty($model)) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => 'Nomor rujukan tidak ditemukan'
            ];
        }

        $hapus = $modelBpjs->deleteRujukan($model->no_rujukan);

        if (isset($hapus['metaData']['code']) && $hapus['metaData']['code'] == 200) {
            $model->delete();
        } else {
            return [
                'status' => 422,
                'title' => 'Proses BPJS Gagal!',
                'text' => $hapus['metaData']['message']
            ];
        }
        
        return $hapus;
    }

    public function actionGetDataRujukan()
    {
        $request = Yii::$app->request;
        
        $rujukanbpjs_id = $request->get('rujukanbpjs_id');

        $model = RujukanBpjs::find()->Where(['rujukanbpjs_id' => $rujukanbpjs_id])->one();

        if(empty($model)) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => 'Nomor rujukan tidak ditemukan'
            ];
        }

        $modelBpjs = new Bpjs;
        $additional_request = (array) json_decode($model['additional_request']);
        $sep = $modelBpjs->referensiCariSep($additional_request['noSep']);
        $peserta = $modelBpjs->peserta($sep['response']['peserta']['noKartu'], null, false);

        return [
            'rujukan' => $model,
            'sep' => $sep,
            'peserta' => $peserta
        ];
    }

    public function actionUpdateRujukan()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $model = new RujukanBpjs;

            if ($request->post()) {
                $model->attributes = $request->post();
                $responseRujukan = $this->rujukanUpdate($request->post('no_rujukan'), $model);

                if ($responseRujukan['status'] != 200 || $responseRujukan['status'] != '200') {
                    // \Yii::$app->response->statusCode = 500;
                    $transaction->rollback();
                    return $responseRujukan;
                }

                $data_rujukan_before = $model->find()->where(['no_rujukan'=>$request->post('no_rujukan'), 'is_deleted' => false])->asArray()->one();

                //$rujukan = $responseRujukan['data']['rujukan'];
                $model->no_rujukan = $request->post('no_rujukan');
                $model->tglsep = $data_rujukan_before['tglsep'];

                $additional_data = json_decode($data_rujukan_before['additional_data']);
                $additional_data = $this->stdToArray($additional_data);
                $additional_data['rujukan']['tglRencanaKunjungan'] = $request->post('tanggal_rencana_kunjungan');
                $additional_data['rujukan']['diagnosa']['kode'] = $request->post('diagnosa_rujukan');
                $additional_data['rujukan']['diagnosa']['nama'] = $request->post('diagnosa_rujukan_nama');
                $additional_data['rujukan']['tujuanRujukan']['kode'] = $request->post('dirujukke');
                $additional_data['rujukan']['tujuanRujukan']['nama'] = $request->post('dirujukke_nama');
                $model->additional_data = json_encode($additional_data);

                $additional_request = json_decode($data_rujukan_before['additional_request']);
                $additional_request = $this->stdToArray($additional_request);
                $additional_request['no_rujukan'] = $request->post('no_rujukan');
                $additional_request['ppkDirujuk'] =  $request->post('dirujukke');
                $additional_request['jnsPelayanan'] =  $request->post('jenis_pelayanan_bpjs');
                $additional_request['catatan'] =  $request->post('catatan_rujukan');
                $additional_request['diagRujukan'] =  $request->post('diagnosa_rujukan');
                $additional_request['tipeRujukan'] =  $request->post('rujukan');
                $additional_request['tglRencanaKunjungan'] =  $request->post('tanggal_rencana_kunjungan');

                $model->additional_request = json_encode($additional_request);

                if ($model->save()) {

                    $rujukanUpdate = "
                        UPDATE rujukanbpjs_t SET is_deleted = true
                        WHERE rujukanbpjs_id = {$data_rujukan_before['rujukanbpjs_id']}
                    ";

                    $rujukanUpdate = Yii::$app->db->createCommand($rujukanUpdate)->execute();

                    $transaction->commit();
                    return [
                        'rujukanbpjs_id' => $model->rujukanbpjs_id,
                        'bpjs_id' => $model->bpjs_id,
                        'message' => 'Data Berhasil di simpan.',
                        'id' => DocoHelpers::encrypt($model->rujukanbpjs_id)
                    ];
                } else {
                    $transaction->rollback();
                    $errors = DocoHelpers::parseError($model->errors, 'RujukanBpjsForm');

                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    protected function rujukanUpdate($no_rujukan, $payloadRujukan)
    {
        $modelBpjs = new Bpjs;
        $namaUser = Yii::$app->jwt->user->nama_pemakai;

        // Set variabel ke rujukan
        $rujukan['noRujukan'] = $no_rujukan;
        $rujukan['tglRujukan'] = $payloadRujukan['tanggal_rujukan'];
        $rujukan['ppkDirujuk'] = $payloadRujukan['dirujukke'];
        $rujukan['jnsPelayanan'] = $payloadRujukan['jenis_pelayanan_bpjs'];
        $rujukan['catatan'] = $payloadRujukan['catatan_rujukan'];
        $rujukan['diagRujukan'] = $payloadRujukan['diagnosa_rujukan'];
        $rujukan['tipeRujukan'] = $payloadRujukan['rujukan'];
        // $rujukan['poliRujukan'] = $payloadRujukan['poli_rujukan'];
        $rujukan['poliRujukan'] = empty($payloadRujukan['kode_spesialis']) || $payloadRujukan['rujukan']==1?'':$payloadRujukan['kode_spesialis'];
        $rujukan['spesialis'] = $payloadRujukan['spesialis'];
        $rujukan['kodeSpesialis'] = $payloadRujukan['kode_spesialis'];
        $rujukan['tglRencanaKunjungan'] = $payloadRujukan['tanggal_rencana_kunjungan'];
        $rujukan['user'] = $namaUser;

        // Set ke model
        $modelBpjs->t_rujukan = $rujukan;

        // Save
        $result = $modelBpjs->updateRujukan();
        if (isset($result['metaData']['code'])) {
            if ($result['metaData']['code'] == 200) {
                $data = $result['response'];

                return [
                    'status' => 200,
                    'message' => 'Berhasil update rujukan.',
                    'data' => $data,
                    'request' => $modelBpjs->t_rujukan
                ];
            } else {
                return [
                    'status' => 422,
                    'title' => 'Proses BPJS Gagal!',
                    'text' => $result,
                    'flag' => 'bpjs_error'
                ];
            }
        }

        return true;
    }

    protected function stdToArray($obj){
        $reaged = (array)$obj;
        foreach($reaged as $key => &$field){
            if(is_object($field))$field = $this->stdToArray($field);
        }
        return $reaged;
    }
}
