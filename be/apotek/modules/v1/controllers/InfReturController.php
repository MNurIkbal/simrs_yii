<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-02 15:12:56
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-03-26 10:14:41
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use app\modules\v1\models\InfoReturResepView;
use app\modules\v1\models\DetailReturResepView;
use app\modules\v1\models\PasienReturV;
use app\modules\v1\models\LogActivityR;
use app\modules\v1\models\ReturResep;
use SirsCore\businessLogic\ReturResep as BusinessLogicReturResep;
use SirsCore\businessLogic\StokObatAlkes;
use SirsCore\models\ReturResepDetail;
use Doco\components\BatchUpdate;
use Doco\components\DocoHelpers;
use Doco\models\ObatAlkesPasien;
use Doco\models\Pegawai;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use Doco\models\LoginForm;

class InfReturController extends DocoActiveController {
    public $modelClass = 'app\modules\v1\models\InfoReturResepView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions() {
        return [
            'export-excel'          => 'app\modules\v1\actions\InfRetur\ExportExcelAction',
            'get-detail'            => 'app\modules\v1\actions\InfRetur\GetDetailAction',
            'get-detail-pasien'     => 'app\modules\v1\actions\InfRetur\GetDetailPasienAction',
            'get-no-retur'          => 'app\modules\v1\actions\InfRetur\GetNoReturAction',
            'get-no-resep'          => 'app\modules\v1\actions\InfRetur\GetNoResepAction',
            'index'                 => 'app\modules\v1\actions\InfRetur\IndexAction',
            'get-pendaftaran-obat'  => 'app\modules\v1\actions\InfRetur\GetPendaftaranObatAction',
            'get-data-pasien-retur' => 'app\modules\v1\actions\InfRetur\GetDataPasienRetur',
            'get-log-activity'      => 'app\modules\v1\actions\InfRetur\GetLogActivityAction',
            'retur-resep'           => 'app\modules\v1\actions\InfRetur\ReturResepAction',
            'edit-retur-resep'      => 'app\modules\v1\actions\InfRetur\EditReturResepAction',
            'validasi-edit'         => 'app\modules\v1\actions\InfRetur\ValidasiEditAction',
            'batal-retur'            => 'app\modules\v1\actions\InfRetur\BatalReturResepAction',
            'get-pasien-retur'  => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new PasienReturV(),
                'selected' => [
                    'pendaftaran_id',
                    'nama_pasien',
                    'no_rekam_medik',
                    'no_pendaftaran'
                ],
                'field_search' => [
                    'nama_pasien',
                    'no_rekam_medik',
                    'no_pendaftaran'
                ],
                'orderby' => [
                    ['nama_pasien', 'ASC'],
                    ['no_rekam_medik', 'ASC'],
                    ['no_pendaftaran', 'ASC']
                ],
            ]
        ];
    }

    public function dateFilter($query, $request, $dateKey) {
        $advanced_filter = $request->get('advanced-filter');
        foreach ($advanced_filter as $key => $value) {
            if(in_array($key, $dateKey)) {
                $explode = explode(" - ", $advanced_filter[$key]);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                $query->andWhere(['between', $key, $start, $end]);
            }
        }
    }

    public function dataReturResep(){
        $data = InfoReturResepView::find()->select([
            'returresep_id',
            'tgl_retur',
            'no_returresep',
            'nama_pasien',
            'noresep',
            'carabayar_nama',
            'penjamin_nama',
        ]);

        return $data;
    }

    public function detailReturResep($id){
        $data = DetailReturResepView::find()
            ->select([
                'nama_pasien',
                'tgl_retur',
                'carabayar_nama',
                'penjamin_nama',
                'no_returresep',
                'noresep',
                'obatalkes_nama',
                'qty_retur',
                'hargasatuan',
                'total',
            ])
            ->where('returresep_id = :id', [':id' => $id]);

        return $data;
    }

    public function actionVerifikasi() {
        try {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $request = Yii::$app->request->post();
            $verifType = ArrayHelper::getValue($request, 'verif_type');
            $password = ArrayHelper::getValue($request, 'pass');
            $username = ArrayHelper::getValue($request, 'username');

            if($verifType == 'simpan-verifikasi') {
                $modelLogin = new LoginForm();
                $modelLogin->username = $username;
                $modelLogin->password = $password;
                if(!$modelLogin->validate()) {
                    return $this->responseJson(400, 'Password Salah', [], ['title' => 'Proses Gagal !']);
                }
            }
            
            $returBL = new BusinessLogicReturResep;
            $returBL->setPayload($request);
            $returBL->setPayloadLog($request);
            if($verifType == 'simpan-verifikasi') {
                $returBL->saveReturPendaftaran();
            }
            $returBL->verifikasi($verifType);
            
            $transaction->commit();
            return $this->responseJson(200, 'Verifikasi retur berhasil');
        } catch(\yii\base\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(400, $e->getMessage(), [], ['title' => 'Verifikasi Gagal !']);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(400, $e->getMessage(), [], ['title' => 'Verifikasi Gagal !']);
        }
    }
}
