<?php

/**
* @author : Anggoro (tri.anggoro@docotel.com)
* A product of PT. Docotel Teknologi
* Powered by Sirs
*/

namespace Doco\gudang\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use Doco\gudang\models\ImportAdjustmentForm;
use GuzzleHttp\Exception\RequestException;
use yii\web\UploadedFile;

class MigrasiAdjustmentController extends DocoController
{
    protected $_title = "Import Adjustment";
    protected $_module = '/gudang/migrasi-adjustment';
    protected $_restGudang;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function actions() {
        return [
            'download-template' => 'Doco\gudang\actions\MigrasiAdjustment\DownloadTemplateAction'
        ];
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $form_model = new ImportAdjustmentForm;
        Yii::$app->cache->delete("ImportAdjustment");
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $form_model->load($post);
            $form_model->attachment = UploadedFile::getInstance($form_model, 'attachment');
            $data = $form_model->upload();
            $form_model->attachment = json_encode($data);
            $form_model->jenis_adjusmen = $form_model->jenis_adjusmen == "0" ? 'masuk' : 'keluar';
            $form_model->ruangan_id = $form_model->ruangan_adjusmen_id;

            if (count($data) <= 0) {
                throw new \Exception("Tidak Ada Data", 1);
            }
            $response = $this->_restGudang->post('migrasi-adjustment/save-import', [
                'form_params' => $form_model
            ]);

            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body, false);
        }
        $request = $this->_restGudang->get('migrasi-adjustment/get-ruangan');
        $data_ruangan = json_decode($request->getBody(), true);
        $ruangan = [];
        foreach ($data_ruangan['response'] as $row) {
            $ruangan[$row['ruangan_id']] = $row['instalasi_nama'] ." - ". $row['ruangan_nama'];
        }

        return $this->render('index', get_defined_vars());
    }
}