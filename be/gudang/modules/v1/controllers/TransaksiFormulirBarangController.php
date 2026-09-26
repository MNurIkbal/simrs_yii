<?php

/**
 * @author: wahyu saepuloh
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use app\modules\v1\models\TransaksiFormulirBarangView;
use app\modules\v1\models\InfoFormSoBarangDetailView;
use app\modules\v1\models\InfoFormSoBarangView;
use app\modules\v1\models\TransaksiFormulirBarang;
use app\modules\v1\models\TransaksiFormulirBarangDetail;
use app\modules\v1\models\KelompokBarang;
use app\modules\v1\models\SubKelompokBarang;
use app\modules\v1\businessLogic\FormulirStokOpname;
use app\modules\v1\models\KonfigGudang;
use yii\helpers\ArrayHelper;
use Doco\components\DocoMessages;

class TransaksiFormulirBarangController extends DocoActiveController
{
    public $modelClass = TransaksiFormulirBarangView::class;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["save"] = ["POST","GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $konfig = KonfigGudang::find()->select(['max_dataso'])->asArray()->one();
        $max_data_so = $konfig['max_dataso'];

        $query = $this->dataProvider();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $max_data_so
            ]
        ]);

        if($dataProvider->getTotalCount() > $max_data_so){
            $dataProvider->setTotalCount($max_data_so);
        }

        return $dataProvider;
    }

    private function dataProvider()
    {
        $model = new TransaksiFormulirBarangView;
        $query = $model::find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return $query;
    }

    // /**
    //  *
    //  * @see Fungsi insert stok opname barang
    //  * @ Wahyu Saepuloh
    //  *
    //  */
    public function actionSave()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        
        $dataDetail = [];
        $idParent = null;
        $listBarangId = $request->post('listBarangId', []);
        $ruangan_id = $request->post('ruangan_id', null);
 
        try {
            if (empty($listBarangId)) return $this->responseJson(422, 'Tidak ada data yg di pilih!');
            
            $query = $this->getData();
            $query->andWhere(['IN', 'barang_id', $listBarangId]);
            $listDataFormulir = $query->asArray()->all();

            $modelTransaksiFormBarang = new TransaksiFormulirBarang;
            $modelTransaksiFormBarang->ruangan_id = $ruangan_id;
            $modelTransaksiFormBarang->tglformulir = date('Y-m-d H:i:s');
            if (!$modelTransaksiFormBarang->save(false)) {
                return $this->responseJson(422, DocoMessages::ERR_MESSAGE, $modelTransaksiFormBarang->errors);
            }

            foreach($listDataFormulir as $key => $value) {
                $dataDetail[] = [
                    'stokopnamebarangdetail_id' => ArrayHelper::getValue($value, 'sop_sopbarangdetail_id'),
                    'barang_id' => ArrayHelper::getValue($value, 'barang_id'),
                    'formsobarang_id' => $modelTransaksiFormBarang->formsobarang_id,
                    'stok' => DocoHelpers::convertToNumber(DocoHelpers::convertCommaToPoint(DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok_sistem', 0)))),
                    'harganetto' => ArrayHelper::getValue($value, 'barang_harganetto', 0),
                    'periodestok_id' => ArrayHelper::getValue($value, 'periodestokbarang_id'),
                    'ruangan_id' => ArrayHelper::getValue($value, 'ruangan_id'),
                    'satuankecil_id' => ArrayHelper::getValue($value, 'satuankecil_id')
                ];
            }

            $idParent = $modelTransaksiFormBarang->formsobarang_id;
            $noSOBarang = TransaksiFormulirBarang::find()->select(['noformulir'])->where(['formsobarang_id' => $idParent])->asArray()->one();
            $result = TransaksiFormulirBarangDetail::batchInsert($dataDetail,false);
            
            $transaction->commit();

            return $this->responseJson(200, 'Berhasil',[
                'no_formulir' => ArrayHelper::getValue($noSOBarang, 'noformulir'),
                'id_parent' => DocoHelpers::encrypt($idParent)
            ]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::ERR_MESSAGE);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::ERR_MESSAGE);
        }
    }

    public function actionGenerateKelompokApi()
    {
        $kelompokBarang = KelompokBarang::find()->where(['is_active' => true])->all();
        $subKelompokBarang = SubKelompokBarang::find()->where(['is_active' => true])->all();

        return [
            'kelompok' => $kelompokBarang,
            'sub_kelompok' => $subKelompokBarang,
        ];
    }

    public function actionGetNamaSubKelompok($subkelompokbarang_id)
    {
        $query = SubKelompokBarang::findOne($subkelompokbarang_id);

        return $query;
    }

    /**
    * @controller actionPrintFormulirStokOpname
    * @attribute #ruangan_nama# => Menampilkan Nomor Formulir
    * @attribute #ruangan# => Menampilkan Ruangan
    * @attribute #tgl_formulir# => Menampilkan Tanggal Formulir
    * @attribute #detail_formulir# => Data Detail Formulir

    **/

    public function actionPrintFormulirStokOpname($id) {
        $print = new DocoPrint;
        $model = new InfoFormSoBarangView;
        $model_detail = new InfoFormSoBarangDetailView;

        $head = $model->find()->where([
            "formsobarang_id" => $id
        ])->one();

        $detail = $model_detail->find()->where([
            "formsobarang_id" => ArrayHelper::getValue($head, 'formsobarang_id')
        ])->orderBy(['barang_nama' => SORT_ASC])->all();

        $print_attributes = [
            "#nomor_formulir#" => ArrayHelper::getValue($head, 'noformulir'), //$head["noformulir"],
            "#ruangan#" => ArrayHelper::getValue($head, 'ruangan_nama'), //$head["ruangan_nama"],
            "#priode_stok#" => date("d-M-Y H:i:s", strtotime(ArrayHelper::getValue($head, 'tglformulir'))),
            "#detail_formulir#" => $this->renderPartial('index',[
                'head' => $head,
                'detail' => $detail
            ]),
        ];

        $print->attributes = $print_attributes;
        return $print->Output();
    }

    public function getData() 
    {
        $request = Yii::$app->request;
        $post = $request->post();

        $query = TransaksiFormulirBarangView::find();
        if (!empty($post['instalasi'])) {
            $query->andWhere(['instalasi_id'=>$post['instalasi']]);
        }
        if (!empty($post['ruangan_id'])) {
            $query->andWhere(['ruangan_id'=>$post['ruangan_id']]);
        }
        if (!empty($post['namaBarang'])) {
            $query->andWhere(['ILIKE', 'barang_nama', $post['namaBarang']]);
        }
        if (!empty($post['kelompokBarang'])) {
            $query->andWhere(['kelompokbarang_id'=>$post['kelompokBarang']]);
        }
        if (!empty($post['subKelompokBarang'])) {
            $query->andWhere(['subkelompokbarang_id'=>$post['subKelompokBarang']]);
        }
        return $query;
    }
}