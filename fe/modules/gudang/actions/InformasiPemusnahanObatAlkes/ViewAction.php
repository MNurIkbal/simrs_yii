<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\InformasiPemusnahanObatAlkes;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use Doco\gudang\components\access\VerifikasiPemusnahanAccess as VerifikasiPemusnahan;

class ViewAction extends Action {
    protected $_title = "Informasi Pemusnahan Obat Alkes";

    public function run($id) {
        $headTitle = $this->_title;
        $title = 'Detail Pemusnahan Obat Alkes';
        $instalasi = Yii::$app->docoVars->workspace('instalasi_name');
        $pemusnahanobat_id = DocoHelpers::decrypt($id);

        try {
            $response = Yii::$app->docoRest->gudang->get('inf-pemusnahan-obat/data-detail', ['query' => ['id' => $pemusnahanobat_id]]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response'];
        } catch (Exception $e) {
            $data = [];
        }

        $btn_toolbar = [
                        'back',
                        'print'=>[
                            'attributes' => [
                                'data-target' => '/gudang/informasi-pemusnahan-obat/print-pemusnahan?id='.$id.'&nopemusnahan='.$data['nopemusnahan'].'&',
                            ]
                        ],
                    ];
        if((new VerifikasiPemusnahan)->check($data['is_verifikasi'],$data['is_deleted'])){
            $btn_toolbar['verifikasi'] = [
                            'type' => 'button',
                            'title' => 'Verifikasi',
                            'icon' => 'fa fa-check',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'verifikasi-pemusnahan',
                                'data-url' => '/gudang/informasi-pemusnahan-obat/verifikasi-pemusnahan?id='.DocoHelpers::encrypt($id)
                            ]
                        ];
            $btn_toolbar['batal-pemusnahan'] = [
                            'type' => 'button',
                            'title' => 'Batal',
                            'icon' => 'fa fa-times',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'batal-pemusnahan',
                                'data-url' => '/gudang/informasi-pemusnahan-obat/batal-pemusnahan?id='.DocoHelpers::encrypt($pemusnahanobat_id)
                            ]
                        ];
        }

        return $this->controller->render('detail',get_defined_vars());
    }
}