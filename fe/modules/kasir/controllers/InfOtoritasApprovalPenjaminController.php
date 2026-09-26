<?php


/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */


namespace Doco\kasir\controllers;

use app\components\DocoConstants;
use Yii;
use yii\web\Response;

use app\components\DocoHelpers;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class InfOtoritasApprovalPenjaminController extends DocoController
{
    protected $_title = "Informasi List Approval Penjamin";
    protected $_restKasir;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $response = $this->guzzleExec($this->_restKasir, [
            'url' => "inf-otoritas-approval-penjamin/init-index",
            'method' => 'GET'
        ]);
        $statusApprove = [];

        if(isset($response['data'])) {
            $statusApprove = $response['data']['status_approve'];
        }

        return $this->render('index', compact('title', 'statusApprove'));
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam($request->get());
        $response = $this->_restKasir->get('inf-otoritas-approval-penjamin/index?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
        $body = json_decode($response->getBody(), true);
        $row = [];
        $result = [];
        $no = $request->get('start', 1);

        foreach ($body['response']['data'] as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['approvaldiskon_id']);
            $value['primary'] = $primaryKey;
            $value['rowNum'] = $no;
            $value['nominal_diskon_display'] = DocoHelpers::rupiahDisplay($value['jumlah_diskon']);
            if ($value['status_approve'] == DocoConstants::STATUS_APPROVED || $value['status_approve'] == DocoConstants::STATUS_REJECT) {
                $value['action'] = '
                <button type="button" 
                    class="btn btn-info btn-labeled btn-xs" 
                    data-toggle= "modal"
                    data-target = "#modal_backdrop"
                    action = "/kasir/inf-otoritas-approval-penjamin/modal-detail?approval_id=' . $primaryKey . '"
                    data-width = "40%"
                >
                    <b><i class="fa fa-file"></i></b>
                    Detail
                </button>
                ';
            } else {
                $value['action'] = '
                <button type="button" 
                    class="btn btn-info btn-labeled btn-xs"  
                    data-toggle= "modal"
                    data-target = "#modal_backdrop"
                    action = "/kasir/inf-otoritas-approval-penjamin/modal-detail?approval_id=' . $primaryKey . '"
                    data-width = "40%"
                >
                    <b><i class="fa fa-file"></i></b>
                    Detail
                </button>
                <button type="button" class="btn btn-success btn-labeled btn-xs" onclick="ApprovalPenjamin(`' . $primaryKey . '`)" >
                    <b><i class="fa fa-check" aria-hidden="true"></i></b>
                    Approve
                </button>
                <button type="button" class="btn btn-danger btn-labeled btn-xs" onclick="RejectPenjamin(`' . $primaryKey . '`)" >
                    <b><i class="fa fa-times" aria-hidden="true"></i></b>
                    Reject
                </button>
            ';
            }

            $row[$key] = $value;
        }

        $result['data'] = $row;
        $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
        $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
        return $result;
    }

     /**
     * Function untuk melalukan unduh excel.
     * 
     * @author Maulana Muhammad Rizky
     * @return any
     */
    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam($request->get());
        $result = [];
        $url = "inf-otoritas-approval-penjamin/export-excel?" . http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/laporan-approval.xlsx";
        try {
            $this->_restKasir->get($url, [
                'save_to' => $path,
            ]);

            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * Function untuk melalukan approval penjamin.
     * 
     * @author Maulana Muhammad Rizky
     * @param integer $approvalId
     * @return json
     */
    public function actionApprovalPenjamin()
    {
        try {
            $request = Yii::$app->request;
            $approvalId = $request->post("approval_id");
            if (!is_numeric($approvalId)) {
                $approvalId = DocoHelpers::decrypt($approvalId);
            }

            $payload = [
                "approval_id" => $approvalId,
            ];

            return $this->guzzleExec($this->_restKasir, [
                'url' => "inf-otoritas-approval-penjamin/approval-pembayaran",
                'method' => 'POST',
                'payload' => [
                    'form_params' => $payload
                ],
                'returnResponse' => true
            ]);
        } catch (\Exception $th) {
            return DocoHelpers::responseTemplate(422, 'Error', $th->getMessage());
        }
    }

     /**
     * Function untuk melalukan reject penjamin.
     * 
     * @author Maulana Muhammad Rizky
     * @param integer $approvalId
     * @return json
     */
    public function actionRejectPenjamin()
    {
        try {
            $request = Yii::$app->request;
            $approvalId = $request->post("approval_id");
            if (!is_numeric($approvalId)) {
                $approvalId = DocoHelpers::decrypt($approvalId);
            }

            $payload = [
                "approval_id" => $approvalId,
            ];

            return $this->guzzleExec($this->_restKasir, [
                'url' => "inf-otoritas-approval-penjamin/reject-pembayaran",
                'method' => 'POST',
                'payload' => [
                    'form_params' => $payload
                ],
                'returnResponse' => true
            ]);
        } catch (\Exception $th) {
            return DocoHelpers::responseTemplate(422, 'Error', $th->getMessage());
        }
    }

    public function actionModalDetail()
    {
        $request = Yii::$app->request;
        try {
            $id = $request->get('approval_id');
            if (!is_numeric($id)) {
                $id = DocoHelpers::decrypt($id);
            }

            $response = $this->guzzleExec($this->_restKasir, [
                'url' => "inf-otoritas-approval-penjamin/get-detail-data",
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        "approval_id" => $id
                    ]
                ],
            ]);

            if (empty($response)) {
                return DocoHelpers::responseTemplate(404, 'Error', "Data tidak ditemukan !");
            }

            /**
             * Data from table.
             */
            $namaPasien = ArrayHelper::getValue($response['data'], 'nama_pasien');
            $pegawaiKasir = ArrayHelper::getValue($response['data'], 'pegawai_kasir');
            $jumlahLimitDiskon = ArrayHelper::getValue($response['data'], 'jumlah_limit_diskon');
            $limitDiskon = ArrayHelper::getValue($response['data'], 'limit_diskon');
            $diskon = ArrayHelper::getValue($response['data'], 'diskon');
            $statusApprove = ArrayHelper::getValue($response['data'], 'status_approve');
            $jumlahDiskon = ArrayHelper::getValue($response['data'], 'jumlah_diskon');
            $tglPembayaran = ArrayHelper::getValue($response['data'], 'tgl_pembayaran');
            $noPendaftaran = ArrayHelper::getValue($response['data'], 'no_pendaftaran');
            $noRekamMedik = ArrayHelper::getValue($response['data'], 'no_rekam_medik');
            $defaultPenjamin = ArrayHelper::getValue($response, 'penjamin.penjamin_nama');

            /**
             * Collect data from additional data.
             */
            $addtionalData = json_decode($response['data']['additional_data'], true);
            $subsidiAsuransi = ArrayHelper::getValue($addtionalData, 'subsidi_asuransi', 0);
            $totalTagihan = ArrayHelper::getValue($addtionalData, 'total_tagihan', 0);
            $totalDibayar = ArrayHelper::getValue($addtionalData, 'total_dibayar', 0);
            $penjaminMaster = ArrayHelper::getValue($addtionalData, 'limit_master_penjamin', []);

            /**
             * Collect data penjamin.
             */
            $penjaminUmum = ArrayHelper::getValue($penjaminMaster, 'umum', []);
            $penjaminAsuransi = ArrayHelper::getValue($penjaminMaster, 'asuransi', []);
            $limitMasterUmum = ArrayHelper::getValue($penjaminUmum, 'diskon_otomatis', 0);
            $limitMasterAsuransi = ArrayHelper::getValue($penjaminAsuransi, 'diskon_otomatis', 0);

            /**
             * Handle bayar setengah - setengah.
             */
            if (!empty($subsidiAsuransi) && !empty($totalDibayar)) {
                $diskonDiberikanUmum = ArrayHelper::getValue($addtionalData, 'nominaldiskon_edit_tagihan', 0);
                $diskonDiberikanAsuransi = $jumlahDiskon - $diskonDiberikanUmum;
                $diskonPersenUmum = (float) number_format(($diskonDiberikanUmum / $totalTagihan) * 100, 2);
                $diskonPersenAsuransi = (float) number_format(($diskonDiberikanAsuransi / $totalTagihan) * 100, 2);
                $totalTagihanAsuransi = $subsidiAsuransi + $diskonDiberikanAsuransi;
                $totalTagihanUmum = $totalDibayar + $diskonDiberikanUmum;

                $limitDiscUmum = round($totalTagihan / 100 * $limitMasterUmum);
                $limitDiscAsuransi = round($totalTagihan / 100 * $limitMasterAsuransi);


                $totalUmum = round($totalDibayar);
                $totalAsuransi = round($subsidiAsuransi);
            } else {
                $diskonDiberikanUmum = $jumlahDiskon;
                $diskonPersenUmum = $diskon;
                $diskonPersenAsuransi = $diskon;
                $diskonDiberikanAsuransi = $jumlahDiskon;
                $totalTagihanAsuransi = $totalTagihan;
                $totalTagihanUmum = $totalTagihan;

                $limitDiscUmum = round($totalTagihan / 100 * $limitDiskon);
                $limitDiscAsuransi = round($totalTagihan / 100 * $limitDiskon);

                $totalUmum = round($totalTagihan - $diskonDiberikanUmum);
                $totalAsuransi = round($totalTagihan - $diskonDiberikanAsuransi);
            }

            return $this->renderPartial('modal_detail', compact(
                "addtionalData",
                "namaPasien",
                "pegawaiKasir",
                "limitDiskon",
                "limitMasterAsuransi",
                "limitMasterUmum",
                "diskon",
                "statusApprove",
                "jumlahDiskon",
                "tglPembayaran",
                "noPendaftaran",
                "noRekamMedik",
                "jumlahLimitDiskon",
                "subsidiAsuransi",
                "totalTagihan",
                "totalDibayar",
                "penjaminUmum",
                "penjaminAsuransi",
                "limitDiscUmum",
                "limitDiscAsuransi",
                "totalUmum",
                "totalAsuransi",
                "diskonDiberikanUmum",
                "diskonDiberikanAsuransi",
                "diskonPersenAsuransi",
                "diskonPersenUmum",
                "defaultPenjamin",
                "totalTagihanUmum",
                "totalTagihanAsuransi"
            ));
        } catch (\Exception $th) {
            return DocoHelpers::responseTemplate(500, 'Error', $th->getMessage());
        }
    }
}
