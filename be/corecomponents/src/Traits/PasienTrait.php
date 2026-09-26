<?php

namespace Doco\Traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use Doco\models\pendaftaran\PasienV;
use Doco\models\PenanggungJawabV;
use Doco\components\DocoRestActiveFilter;
use Doco\models\RiwayatPersonalPasienView;

trait PasienTrait
{

    //  Created By Prof. Ir. H. Yafi
    private function getRiwayatPasienTerbaru($id)
    {
        try {
            $data_riwayat = ['alergi' => [], 'penyakit_dahulu' => [], 'pengobatan' => []];

            // ngambil data limit 5, karena bisa jadi kasusnya 1 askep, 1 alergi, maka bisa dapet 5 alergi dari 5 pendaftaran
            $model = RiwayatPersonalPasienView::find()
                ->select([
                    'riwayat_alergi',
                    'riwayat_obat_terakhir',
                    'riwayat_penyakit_terakhir',
                ])
                ->where([
                    'pasien_id' => $id
                ])
                ->one();

            if(isset($model)){
                // ngambil 5 max data
                $data_riwayat['alergi'] = explode(',', rtrim(trim($model['riwayat_alergi']), ','));
                $data_riwayat['penyakit_dahulu'] = explode(',', rtrim(trim($model['riwayat_penyakit_terakhir']), ','));
                $data_riwayat['pengobatan'] = explode(',', rtrim(trim($model['riwayat_obat_terakhir']), ','));

                // remove only blank space values in array
                $data_riwayat['alergi'] = array_filter($data_riwayat['alergi'], function($val){
                        if(!ctype_space($val)){
                          return $val;
                        }
                    });

                // slice array dan ambil 5 unit untuk riwayat alergi
                $data_riwayat['alergi'] = array_slice($data_riwayat['alergi'], 0, 5);

            }

            return $data_riwayat;

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

    public function actionGetDataPasienDetail()
    {
        $request = Yii::$app->request;
        $pasien_id = $request->get('id', null);
        if(!is_null($pasien_id)){
            $data_pasien = PasienV::find()->where(['pasien_id' => $pasien_id])->asArray()->one();
            $data_keluarga = PenanggungJawabV::find()->select(['penanggungjawab_nama AS keluarga_nama',
                                                              'penanggungjawab_tempatlahir AS keluarga_tempat_lahir',
                                                              'penanggungjawab_tgllahir AS keluarga_tempat_lahir',
                                                              'penanggungjawab_alamat AS keluarga_alamat',
                                                              'penanggungjawab_jeniskelamin AS jenis_kelamin',
                                                              'penanggungjawab_notelp AS keluarga_no_telepon',
                                                              'jenisidentitas_nama AS jenisidentitas',
                                                              'no_identitas AS no_identitas_keluarga',
                                                              'hubungankeluarga_nama AS hubungan_keluarga'])
                                            ->where(['pasien_id' => $pasien_id])->asArray()->one();

            if(!empty($data_pasien) && !empty($data_keluarga)){
                return [
                    'data_pasien' => $data_pasien,
                    'data_keluarga' => $data_keluarga,
                    'message' => 'Data ditemukan',
                ];
            }
        }

        return [
            'data_pasien' => $data_pasien,
            'data_keluarga' => $data_keluarga,
            'message' => 'Data Tidak ditemukan'
        ];
    }

}
