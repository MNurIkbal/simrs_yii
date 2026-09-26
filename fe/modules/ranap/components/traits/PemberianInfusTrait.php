<?php

namespace app\modules\ranap\components\traits;

use Yii;
use yii\helpers\Html;

use app\components\DocoHelpers;
use app\components\Pelayanan\PelayananHelpers;
use app\modules\ranap\models\PemberianInfusModel;
use app\components\DocoConstants;


trait PemberianInfusTrait
{
    public function actionPemberianInfus($id, $pasien_id)
    {
        $formVar = $this->setFormVariable($id);
        return $this->renderAjax('pemberian-infus/index', $formVar);
    }

    public function actionGetDataPemberianInfus($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $pendaftaran_id = DocoHelpers::decrypt($id);
        $restRanap =  Yii::$app->docoRest->ranap;
        $request = Yii::$app->request;
        $start = $request->get('start');
        $length = $request->get('length');
        $user = Yii::$app->session->get('user_identity');
        $is_nurse = PelayananHelpers::isNurse();
        $response = $this->guzzleExec($restRanap, [
            'url' => 'pemberian-infus/get-data',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'offset' => $start,
                    'limit' => $length,
                ]
            ]
        ]);

        $response['totalRecord'] = isset($response['_meta']['totalCount']) ? $response['_meta']['totalCount'] : 0;
        $response['recordsFiltered'] = isset($response['_meta']['totalCount']) ? $response['_meta']['totalCount'] : 0;
        $listData = isset($response['data']) ? $response['data'] : [];
        foreach ($listData as $key => $data) {
            $data['durasi'] = str_replace('.', ',', $data['durasi']);
            $data['volume'] = str_replace('.', ',', $data['volume']);
            $data['jumlah_tetesan'] = str_replace('.', ',', $data['jumlah_tetesan']);
            $data['aksi'] =
                Html::button("+ Respon Terapi", [
                    'class' => 'btn btn-add-respon bg-teal',
                    'action' => '/ranap/pemeriksaan-rawat-inap/form-respon-infus?id=' . $id. '&pemberianinfus_id=' . $data['pemberianinfus_id'] ,
                    'data-width' => '60%',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'disabled' => $is_nurse ? false : true,
                ]) . "<br><br>" .
                Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm bg-teal',
                    'data-source'=>'/ranap/pemeriksaan-rawat-inap/respon-infus-expand?id=' . $id. '&pemberianinfus_id=' . $data['pemberianinfus_id'] ,
                    'onclick'=> 'docoHelper.detail(this)'
                ]);
            $listData[$key] = $data;
        }
        $response['data'] = $listData;

        return $response;
    }

    public function actionFormPemberianInfus($id)
    {
        $formVar = $this->setFormVariable($id);

        return $this->renderAjax('pemberian-infus/_form', $formVar);
    }

    private function setFormVariable($id) {
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $model = new PemberianInfusModel;
        $model->attributes = [
            'tgl_pemasangan' => date('Y-m-d H:i:s'),
        ];
        $now = DocoHelpers::convDateTime($model->tgl_pemasangan, true, false) . ' ' . date('H:i');

        $listTindakan = $this->actionGetTindakanInfus($id, false);
        $listTindakan = $listTindakan['results'];
        $listObat = $this->actionGetObatInfus($id, false);
        $listObat = $listObat['results'];
        $is_nurse = PelayananHelpers::isNurse();
        return compact('model', 'id', 'now', 'listTindakan', 'listObat', 'is_nurse');
    }

    public function actionFormResponInfus($id,$pemberianinfus_id)
    {
        $tgl_pengecekan = date('Y-m-d H:i:s');
        $now = DocoHelpers::convDateTime($tgl_pengecekan, true, false) . ' ' . date('H:i');
        
        return $this->renderAjax('pemberian-infus/_respon', compact('id', 'tgl_pengecekan', 'now','pemberianinfus_id'));
    }

    public function actionGetTindakanInfus($id, $json = true)
    {
        if($json) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $restRanap =  Yii::$app->docoRest->ranap;


        $response = $this->guzzleExec($restRanap, [
            'url' => 'pemberian-infus/get-tindakan',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'tipe_instruksi' => 'TINDAKAN',
                ],
            ],
        ]);

        $results = [];
        foreach ($response as $each) {
            if($json) {
                $results[] = ['id' => $each['instruksitindakan_id'], 'text' => $each['tindakaninstruksi_nama'], 'data' => [
                    'instruksitindakan_id' => $each['instruksitindakan_id']
                ]];
            } else {
                $results[$each['instruksitindakan_id']] = $each['tindakaninstruksi_nama'];
            }
        }

        return ['results' => $results];
    }

    public function actionGetObatInfus($id, $json = true)
    {
        if($json) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $restRanap =  Yii::$app->docoRest->ranap;

        $response = $this->guzzleExec($restRanap, [
            'url' => 'pemberian-infus/get-tindakan',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'tipe_instruksi' => 'BMHP',
                ],
            ],
        ]);

        $results = [];
        foreach ($response as $each) {
            if($json) {
                $results[] = ['id' => $each['instruksitindakan_id'], 'text' => $each['tindakaninstruksi_nama'], 'data' => [
                    'instruksitindakan_id' => $each['instruksitindakan_id']
                ]];
            } else {
                $results[$each['instruksitindakan_id']] = $each['tindakaninstruksi_nama'];
            }
        }

        return ['results' => $results];
    }

    public function actionSavePemberianInfus($id)
    {
        if (Yii::$app->request->post()) {
            $restRanap =  Yii::$app->docoRest->ranap;
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $model = new PemberianInfusModel;
            $model->load(Yii::$app->request->post());
            if($model->validate()) {
                $result = $this->guzzleExec($restRanap, [
                    'url' => 'pemberian-infus/save',
                    'method' => 'post',
                    'payload' => [
                        'query' => [
                            'pendaftaran_id' => $pendaftaran_id,
                        ],
                        'form_params' => [
                            'data' => $model->attributes
                        ]
                    ],
                ]);                
                return DocoHelpers::response($result);
            } else {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
        } 
    }

    public function actionResponInfusExpand($id,$pemberianinfus_id)
    {
        $restRanap =  Yii::$app->docoRest->ranap;
        $listRespon = $this->guzzleExec($restRanap, [
            'url' => 'pemberian-infus/respon-detail',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'id' => $pemberianinfus_id,
                ],
            ],
        ]);
        return $this->renderAjax('pemberian-infus/_respon_detail', compact('listRespon'));
    }

    public function actionSaveResponInfus($id,$pemberianinfus_id)
    {
        if (Yii::$app->request->post()) {
            $restRanap =  Yii::$app->docoRest->ranap;
            $post = Yii::$app->request->post();
            if(isset($post['tgl_respon']) && !empty($post['tgl_respon']) &&
                isset($post['respon']) && !empty($post['respon'])) {
                $max_char = strlen($post['respon']);
                if($max_char > 400){
                    return $this->responseJson(422, 'Maksimal jumlah character tidak boleh melebih 400 character<br> Silahkan cek kembali inputan');
                }

                $result = $this->guzzleExec($restRanap, [
                    'url' => 'pemberian-infus/save-respon',
                    'method' => 'post',
                    'payload' => [
                        'query' => [
                            'id' => $pemberianinfus_id,
                        ],
                        'form_params' => [
                            'data' => $post
                        ],
                    ],
                ]);                
                return DocoHelpers::response($result);
            } else {
                return $this->responseJson(422, 'Silakan cek kembali inputan. respon terapi tidak boleh kosong');
            }
        } 
    }

}
