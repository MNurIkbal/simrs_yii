<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-02-27 14:00:00
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-01 14:17:43
 */

namespace app\modules\rajal\components\traits;

use Yii;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\rajal\models\RujukBalikForm;

trait RujukBalikTrait 
{
    public function actionRujukBalik($id) {
        $pendaftaran_id  = DocoHelpers::decrypt($id);
        $userIdentity = Yii::$app->session->get('user_identity');
        
        $is_nurse = false;
        if($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN){
            $is_nurse = true;
        }
        
        return $this->renderAjax('rujuk-balik/index', [
            'pendaftaran_id'   => $id,
            'is_nurse' => $is_nurse,
        ]);
    }

    public function actionGetRujukBalik($id) {
        $pendaftaran_id  = DocoHelpers::decrypt($id);
        $restRajal =  Yii::$app->docoRest->rajal;
        $userIdentity = Yii::$app->session->get('user_identity');
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['pendaftaran_id'] = $pendaftaran_id;

        $is_nurse = false;
        if($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN){
            $is_nurse = true;
        }

        $res = $this->guzzleExec($restRajal, [
            'method' => 'get',
            'url' => 'tra-pemeriksaan/get-rujuk-balik',
            'payload' => [
                'query' => $payload,
            ],
        ]);

        foreach ($res['data'] as $key => $value) {
            $aksi = '';
            $value['tgl_rujukbalik'] = date('d/m/Y H:i:s',strtotime($value['tgl_rujukbalik']));
            if($value['is_deleted']) {                
                $value['no_srb'] = '<s>' . $value['no_srb'] . '</s>';
                $value['nosep'] = '<s>' . $value['nosep'] . '</s>';
                $value['no_rekam_medik'] = '<s>' . $value['no_rekam_medik'] . '</s>';
                $value['tgl_rujukbalik'] = '<s>' . $value['tgl_rujukbalik'] . '</s>';

                if(isset($value['has_child']) && $value['has_child']) {
                    $aksi = 'Diubah Oleh :'.$value['peg_deleted_nama'];
                } else {
                    $aksi = 'Dihapus Oleh :'.$value['peg_deleted_nama'];
                }
            } else {
                if($is_nurse) {
                    $aksi = Html::button(
                        '<i class="fa fa-eye"></i>',
                        [
                            'class' => 'btn btn-info btn-sm view-rujuk-balik',
                            "action" => "/rajal/pemeriksaan/view-rujuk-balik?id=" . $id . "&rujukbalik_id=" . $value['rujukbalik_id'],
                            "data-width" => "90%",
                            "data-toggle" => "modal",
                            "data-target"=>"#modal_backdrop",
                        ]
                    );
                } else {
                    $aksi = Html::button(
                        '<i class="fa fa-pencil"></i>',
                        [
                            'class' => 'btn btn-info btn-sm edit-rujuk-balik',
                            "action" => "/rajal/pemeriksaan/edit-rujuk-balik?id=" . $id . "&rujukbalik_id=" . $value['rujukbalik_id'],
                            "data-width" => "90%",
                            "data-toggle" => "modal",
                            "data-target"=>"#modal_backdrop",
                        ]
                    ) . ' ' . Html::button(
                        '<i class="fa fa-trash"></i>',
                        [
                            'class' => 'btn btn-danger btn-sm delete-rujuk-balik',
                            "action" => "/rajal/pemeriksaan/delete-rujuk-balik?id=" . $id . "&rujukbalik_id=" . $value['rujukbalik_id'],
                        ]
                    );
                }
            }
            $value['aksi'] = $aksi;
            $res['data'][$key] = $value;
        }
        return DocoHelpers::response($res);
    }

    public function actionSaveRujukBalik($id, $rujukbalik_id) {
        if (Yii::$app->request->post()) {
            $restRajal =  Yii::$app->docoRest->rajal;
            $model = new RujukBalikForm;
            $model->load(Yii::$app->request->post());
            if($model->validate()) {
                $result = $this->guzzleExec($restRajal, [
                    'url' => 'tra-pemeriksaan/update-rujuk-balik',
                    'method' => 'post',
                    'payload' => [
                        'query' => [
                            'rujukbalik_id' => $rujukbalik_id,
                        ],
                        'form_params' => [
                            'rujukBalik' => $model->attributes
                        ]
                    ],
                ]);
                return json_encode($result);
            } else {
                return $this->responseJson(422, 'Silakan cek kembali inputan.', $this->mapErrorForm($model->errors, 'RujukBalikForm'));
            }
        } 
    }

    public function actionEditRujukBalik($id, $rujukbalik_id) {
        return $this->viewRujukBalik($id, $rujukbalik_id, true);
    }


    public function actionViewRujukBalik($id, $rujukbalik_id) {
        return $this->viewRujukBalik($id, $rujukbalik_id, false);
    }

    private function viewRujukBalik($id, $rujukbalik_id, $editable) {
        $pendaftaran_id  = DocoHelpers::decrypt($id);
        $restRajal =  Yii::$app->docoRest->rajal;
        $userIdentity = Yii::$app->session->get('user_identity');
        
        $is_nurse = false;
        if($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN){
            $is_nurse = true;
        }

        $payload = [
            'pendaftaran_id' => $pendaftaran_id,
            'rujukbalik_id' => $rujukbalik_id,
        ];

        $bodyData = $this->guzzleExec($restRajal, [
            'method' => 'get',
            'url' => 'tra-pemeriksaan/get-data-rujuk-balik',
            'payload' => [
                'query' => $payload,
            ],
        ]);
        
        $listDpjp = !empty($bodyData['list-dpjp-bpjs']) ? $bodyData['list-dpjp-bpjs'] : [];
        $listDpjp = ArrayHelper::map($listDpjp, 'kode', 'nama');
        $listDiagnosa = !empty($bodyData['list-diagnosa']) ? $bodyData['list-diagnosa'] : [];
        $listDiagnosa = ArrayHelper::map($listDiagnosa, 'kode', 'nama');

        $model = new RujukBalikForm;
        $model->attributes = $bodyData['data'];
        $listReseptur = json_decode($model->data_reseptur, true);
        return $this->renderAjax('rujuk-balik/edit', [
            'pendaftaran_id'   => $id,
            'rujukbalik_id'   => $rujukbalik_id,
            'is_nurse' => $is_nurse,
            'model' => $model,
            'listDpjp' => $listDpjp,
            'listDiagnosa' => $listDiagnosa,
            'listReseptur' => $listReseptur,
            'editable' => $editable,
        ]);
    }

    public function actionDeleteRujukBalik($id, $rujukbalik_id) {
        $restRajal =  Yii::$app->docoRest->rajal;
        $result = $this->guzzleExec($restRajal, [
            'url' => 'tra-pemeriksaan/delete-rujuk-balik',
            'method' => 'post',
            'payload' => [
                'query' => [
                    'rujukbalik_id' => $rujukbalik_id,
                ],
            ],
        ]);

        return json_encode($result);
    }

}