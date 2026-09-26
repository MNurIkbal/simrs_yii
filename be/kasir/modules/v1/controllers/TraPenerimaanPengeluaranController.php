<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoMessages;
use app\modules\v1\models\PembayaranTransaksi;
use app\modules\v1\models\TandaBuktiBayar;
use app\modules\v1\models\TandaBuktiKeluar;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Pendaftaran;

class TraPenerimaanPengeluaranController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PembayaranTransaksi';

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
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionCreate()
    {
        $result = [];
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            if($post) {
                $model = new PembayaranTransaksi;
                $model->attributes = $post;
                $model->tipe_transaksi = $post['tipe'];
                $model->tgl_transaksi =  date('Y-m-d', strtotime($post['tanggal_transaksi']));
                $model->metode_pembayaran = $request->post('metode');
                $model->kategoritransaksi_id = $request->post('kategori');
                $model->deskripsi = $request->post('deskripsi');
                $model->referensi = $request->post('referensi');
                $model->pendaftaran_id = $request->post('id_pendaftaran');

                if($model->tipe_transaksi == DocoConstants::TIPE_TRANSAKSI_VENDOR) {
                    $model->supplier_id = $model->dari_kepada;
                }
                elseif($model->tipe_transaksi == DocoConstants::TIPE_TRANSAKSI_KARYAWAN) {
                    $model->pegawai_id = $model->dari_kepada;
                }
                else {
                    $model->pasien_id = $model->dari_kepada;
                }
                
                if($model->validate() && $model->save()) {
                    $idParent = $model->pembayarantransaksi_id;
                    if ($model->jenis_transaksi == DocoConstants::JENIS_TRANSAKSI_PEMASUKAN) {
                        $tandaBuktiBayar = new TandaBuktiBayar;
                        $tandaBuktiBayar->ruangan_id = Yii::$app->jwt->ruangan_id;
                        $tandaBuktiBayar->tglbuktibayar = date('Y-m-d H:i:s');
                        $tandaBuktiBayar->jmlpembayaran = $model->jumlah;
                        $tandaBuktiBayar->uangditerima = 0;
                        $tandaBuktiBayar->carapembayaran = $model->metode_pembayaran;
                        $tandaBuktiBayar->jmlpembulatan = 0;
                        $tandaBuktiBayar->uangkembalian = 0;
                        $tandaBuktiBayar->pegawai1_id = Yii::$app->jwt->user->pegawai_id;
                        $tandaBuktiBayar->penerimaanumum_id = $idParent;
                        if(!$tandaBuktiBayar->validate()) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                                'data' => $tandaBuktiBayar->errors
                            ]);
                        }

                        $tandaBuktiBayar->save();
                    } else {
                        $tandaBuktiKeluar = new TandaBuktiKeluar;
                        $tandaBuktiKeluar->ruangan_id = Yii::$app->jwt->ruangan_id;
                        $tandaBuktiKeluar->tgl_buktikeluar = date('Y-m-d H:i:s');
                        $tandaBuktiKeluar->jml_pembayaran = $model->jumlah;
                        $tandaBuktiKeluar->uang_diterima = 0;
                        $tandaBuktiKeluar->pegawai1_id = Yii::$app->jwt->user->pegawai_id;
                        $tandaBuktiKeluar->pembayarantransaksi_id = $idParent;
                        if(!$tandaBuktiKeluar->validate()) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                                'data' => $tandaBuktiKeluar->errors
                            ]);
                        }

                        $tandaBuktiKeluar->save();
                    }

                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM_DATA);
                }
                else {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $model->errors
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }

    public function actionGetAttributes()
    {
        $getLookup = Lookup::find()->select([
            'lookup_id',
            'lookup_type',
            'lookup_name',
        ])->andWhere([
            'lookup_type' => [
                'metode_bayar',
                'tipe_transaksi',
                'jenis_transaksi',
            ]
        ])->asArray()->all();

        $listAttr = [];
        foreach ($getLookup as $value) {
            $id = isset($value['lookup_id']) ? (int) $value['lookup_id'] : null;
            $type = isset($value['lookup_type']) ? $value['lookup_type'] : null;
            $name = isset($value['lookup_name']) ? $value['lookup_name'] : null;
            $listAttr[$type][$id] = $name;
        }
        return [
            'listAttr' => $listAttr,
            'vendor' => DocoConstants::TIPE_TRANSAKSI_VENDOR,
            'karyawan' => DocoConstants::TIPE_TRANSAKSI_KARYAWAN,
            'pasien' => DocoConstants::TIPE_TRANSAKSI_PASIEN
        ];
    }

    public function actionGetNoPendaftaranPasien()
    {
        $request = Yii::$app->request;
        $pasien_id = $request->get('pasien_id');
        $query = Pendaftaran::find()
            ->select([
                'pendaftaran_id as id',
                'no_pendaftaran as text'
            ]);
        $query = $query->where(['pasien_id' => $pasien_id])->orderBy(['created_date' => SORT_DESC]);
        $query = $query->andWhere(['=', 'is_active', true]);

        return $query
            ->limit(5)
            ->asArray()
            ->all();
    }

}

