<?php

/**
 * @Author: Iqbal@docotel.com
 * @Date:   2018-12-04 14:12:02
 * @Last Modified by:  
 * @Last Modified time: 
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\LaporanKunjunganRawatJalanView;
use app\modules\v1\models\Lookup;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoMessages;
use Doco\components\DocoHelpers;
use app\modules\v1\models\SkriningRajal;
use app\modules\v1\models\SkriningCovidRajal;
use app\modules\v1\models\SkriningAssesmentPasien;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Pendaftaran;

class SkriningController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\SkiriningRajal';


    public function actionGetDataPasien()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $dataPasien = LaporanKunjunganRawatJalanView::find()
            ->select([
                'pendaftaran_id', 
                'nama_pasien', 
                'nama_pegawai', 
                'ruangan_id', 
                'ruangan_nama', 
                'instalasi_id', 
                'instalasi_nama',
                'asalrujukan_id',
                'asalrujukan_nama',
                'status_skrining',
                'tanggal_lahir',
                'tempat_lahir',
                'no_rekam_medik',
                'jenis_kelamin'
            ])
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->one();

            return [
                'status' => 200,
                'datapasien' => $dataPasien
            ];

        } catch (\Throwable $th) {
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionGetDataSkrining()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $laporanKunjungan = LaporanKunjunganRawatJalanView::find()
        ->select([
            'pendaftaran_id', 
            'no_pendaftaran', 
            'nama_pasien', 
            'nama_pegawai', 
            'ruangan_id', 
            'ruangan_nama', 
            'instalasi_id', 
            'instalasi_nama',
            'asalrujukan_id',
            'asalrujukan_nama',
            'status_skrining'
        ])
        ->where(['pendaftaran_id' => $pendaftaran_id])
        ->one();

        $skriningData = SkriningRajal::find()
        ->where(['pendaftaran_id' => $pendaftaran_id])
        ->one();

        $skriningQuestion = Lookup::find()
        ->select(['lookup_id', 'lookup_name', 'lookup_type','lookup_urutan'])
        ->where(['IN', 'lookup_type', ['keputusan', 'kesadaran', 'batuk', 'pernapasan', 'nyeri_dada', 'resiko_jatuh']])
        ->orderBy('lookup_id', 'desc')
        ->asArray()
        ->all();

        $tmpArray = [];
        foreach ($skriningQuestion as $key => $value) {
            $tmpArray[$value['lookup_type']][] = $value;
        }

        return [
            'status' => 200,
            'pertanyaan_skrining' => $tmpArray,
            'laporankunjungan' => $laporanKunjungan,
            'data_skrining' => $skriningData
        ];
    }

    public function actionSaveSkriningRajal()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $bahasaAsing = ArrayHelper::getValue($post, 'bahasa_asing');
        $bahasaDaerah = ArrayHelper::getValue($post, 'bahasa_daerah');
        try {
            $modelParent = SkriningRajal::find()->where(['pendaftaran_id' => $post['pendaftaran_id']])->one();
            
            if(! $modelParent) {
                $modelParent = new SkriningRajal;
            }
                
            $modelParent->attributes = $post;
            $modelParent->bahasa_asing = $bahasaAsing ? $bahasaAsing : null;
            $modelParent->bahasa_daerah = $bahasaDaerah ? $bahasaDaerah : null;
            if($modelParent->validate()) {
                if($modelParent->save()) {
                    return [
                        'status' => 200, 
                        'message' => "Skrining berhasil disimpan !"
                    ];
                }
            } else {
                return [
                    'status' => 422, 
                    'message' => $modelParent->errors
                ];
            }
        } catch (\Throwable $th) {
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionGetDataSkriningAssesmentPasien()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $skriningQuestion =  SkriningAssesmentPasien::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $pendaftaran = (new \yii\db\Query())
                ->select([
                    'kabupaten.kabupaten_nama',
                    'kecamatan.kecamatan_nama',
                    'kelurahan.kelurahan_nama',
                    'pekerjaan.pekerjaan_nama', 
                    'carabayar.carabayar_nama', 
                    'caramasuk.caramasuk_nama', 
                    'bpjs.nokartuasuransi',
                    'pasien_m.alamat_pasien',
                    'pasien_m.tempat_lahir',
                    'pasien_m.tanggal_lahir',
                    'pasien_m.nama_ayah',
                    'pasien_m.no_mobile_pasien',
                    'pasien_m.nama_pasien',
                    'lookup.lookup_kode',
                ])
                ->from('pendaftaran_t pendaftaran')
                ->leftjoin('(select nama_pasien,alamat_pasien,tempat_lahir,tanggal_lahir,nama_ayah,no_mobile_pasien,jeniskelamin,pasien_id,kabupaten_id,kecamatan_id,kelurahan_id,pekerjaan_id from pasien_m ) pasien_m' , 'pasien_m.pasien_id = pendaftaran.pasien_id')
                ->leftjoin('(select kabupaten_id, kabupaten_nama from kabupaten_m ) kabupaten' , 'kabupaten.kabupaten_id = pasien_m.kabupaten_id')
                ->leftjoin('(select kecamatan_id, kecamatan_nama from kecamatan_m ) kecamatan' , 'kecamatan.kecamatan_id = pasien_m.kecamatan_id')
                ->leftjoin('(select kelurahan_id, kelurahan_nama from kelurahan_m ) kelurahan' , 'kelurahan.kelurahan_id = pasien_m.kelurahan_id')
                ->leftjoin('(select pekerjaan_id, pekerjaan_nama from pekerjaan_m ) pekerjaan' , 'pekerjaan.pekerjaan_id = pasien_m.pekerjaan_id')
                ->leftjoin('(select lookup_id, lookup_kode from lookup_m ) lookup' , 'lookup.lookup_id = pasien_m.jeniskelamin::int')
                ->leftjoin('(select carabayar_id, carabayar_nama from carabayar_m ) carabayar' , 'carabayar.carabayar_id = pendaftaran.carabayar_id')
                ->leftjoin('(select caramasuk_id, caramasuk_nama from caramasuk_m ) caramasuk' , 'caramasuk.caramasuk_id = pendaftaran.caramasuk_id')
                ->leftjoin('(select bpjs_id, nokartuasuransi from bpjs_t ) bpjs' , 'bpjs.bpjs_id = pendaftaran.bpjs_id')
                ->where(['pendaftaran_id' => $pendaftaran_id])->one();
        
        return [
            'status' => 200,
            'skrining_pasien' => $skriningQuestion,
            'pendaftaran' => $pendaftaran
        ];
    }

    public function actionSaveAssesmentPasien()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $modelParent = SkriningAssesmentPasien::find()->where(['pendaftaran_id' => $post['pendaftaran_id']])->one();
            if(! $modelParent) {
                $modelParent = new SkriningAssesmentPasien;
            }
            
            $modelParent->attributes = $post;
            if($modelParent->validate()) {
                if($modelParent->save()) {
                    return [
                        'status' => 200, 
                        'message' => "Skrining berhasil disimpan !"
                    ];
                }
            } else {
                return [
                    'status' => 422, 
                    'message' => $modelParent->errors
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

    public function actionGetDataSkriningCovid(){
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        try{
            $result = SkriningCovidRajal::find()
                    ->where(['pendaftaran_id' => $pendaftaran_id])
                    ->andWhere(['is_deleted' => false])
                    ->one();
            $getPendaftaran = Pendaftaran::find()
                    ->select(['pasien_id'])
                    ->where(['pendaftaran_id' => $pendaftaran_id])->one();
            return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM_DATA, [
                'text' => DocoMessages::KEY_SUC_SYSTEM,
                'data' => [
                    'skrining' => $result,
                    'pasien_id' => $getPendaftaran->pasien_id
                ]
            ]);

        } catch (\Throwable $th) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'message' => $th->getMessage()
            ]);
        }
    }

    public function actionSaveSkriningCovid()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model = SkriningCovidRajal::find()->where(['pendaftaran_id' => $post['pendaftaran_id']])->one();
            $getPendaftaran = Pendaftaran::findOne($post['pendaftaran_id']);
            if(! $model) {
                $model = new SkriningCovidRajal;
            }
            $model->attributes = $post;
            $model->tgl_swab_positif = isset($post['tgl_swab_positif']) ? $post['tgl_swab_positif'] : null;
            $model->tgl_swab_negatif = isset($post['tgl_swab_negatif']) ? $post['tgl_swab_negatif'] : null;
            $model->suspek = isset($post['suspek']) ? $post['suspek'] : null;
            $model->terkonfirmasi = isset($post['terkonfirmasi']) ? $post['terkonfirmasi'] : null;
            unset($model->skrining_covid_id);
            if($model->validate()) {
                if($model->save()) {
                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM_DATA, [
                        'text' => DocoMessages::SUC_MESSAGE,
                        'data' => [
                            'pendaftaran_id' => DocoHelpers::encrypt($getPendaftaran->pendaftaran_id),
                            'pasien_id' => DocoHelpers::encrypt($getPendaftaran->pasien_id)
                        ]
                    ]);
                }
            } else {
                return DocoHelpers::callback(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $model->errors
                ]);
            }
        } catch (\Throwable $th) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'message' => $th->getMessage()
            ]);
        }
    }
}