<?php

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\modules\master\models\PencarianPasienForm;
use app\modules\master\components\traits\DashboardOdooFarmasiTrait;
use app\modules\master\components\traits\DashboardOdooMasterTrait;

use app\modules\master\components\traits\DashboardOdooAdmissionTrait;

class DashboardOdooController extends DocoController
{
    use DashboardOdooFarmasiTrait;
    use DashboardOdooAdmissionTrait;
    use DashboardOdooFarmasiTrait,
        DashboardOdooMasterTrait;

    const PAKET = 'PKT';
    const TINDAKAN = 'TND';
    const OBAT = 'OBT';
    const BARANG = 'BRG';
    const PAISEN_DEPOSIT = 'pasiendeposit';
    const PAISEN_DEBT = 'patientdebt';
    const SCROLL_CASHIER = 'scrollkasir';
    const TND_LINE = 'saleorderline';
    const OBT_LINE = 'obatalkespasien';
    const SO_BILL = 'saleorderbill';
    const SO_COB = 'saleordercob';

    protected $_title = "Dashboard Odoo";
    protected $_module = '/master/dashboard-odoo';

    protected $_restKasir;
    protected $_restGudang;
    protected $_restPengadaan;
    protected $_restPendaftaran;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir;
        $this->_restGudang = Yii::$app->docoRest->gudang;
        $this->_restPengadaan = Yii::$app->docoRest->pengadaan;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = Yii::t('fe', 'Odoo Monitoring');
        $model = new PencarianPasienForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $request = Yii::$app->request;
        if($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post($formName);
            $model->attributes = $post;
            $path = $this->getPath($model->jenis_transaksi);
            $transTypeUm = [
                'Availed' => 'Availed',
                'Deposit Availed' => 'Deposit Availed',
                'Deposit Collect' => 'Deposit Collect',
                'Deposit Refund' => 'Deposit Refund',
            ];
            $transTypeSc = [
                'BankTransfer' => 'BankTransfer',
                'CASH' => 'CASH',
                'CreditCard' => 'CreditCard',
            ];
            $statusProses = [
                'SUKSES' => 'SUKSES',
                'GAGAL' => 'GAGAL',
                'MENUNGGU PROSES' => 'MENUNGGU PROSES',
                'DALAM PROSES' => 'DALAM PROSES',
            ];
            return $this->renderAjax($path, [
                'transTypeUm' => $transTypeUm,
                'transTypeSc' => $transTypeSc,
                'statusProses' => $statusProses
            ]);
        }
        return $this->render('index', get_defined_vars());
    }

    private function getPath($jenis_transaksi)
    {
        switch ($jenis_transaksi) {
            case 1:
                $path = '_so_line';
                break;

            case 2:
                $path = '_deposit';
                break;

            case 3:
                $path = '_scroll_cashier';
                break;

            case 4:
                $path = '_stockout';
                break;

            case 5:
                $path = '_stockreturn';
                break;

            case 6:
                $path = '_stockscrap';
                break;

            case 7:
                $path = '_grn';
                break;

            case 8:
                $path = '_product_templete';
                break;

            case 9:
                $path = '_patient';
                break;

            case 10:
                $path = '_so';
                break;

            case 11:
                $path = '_partner';
                break;

            case 12:
                $path = '_so_bill';
                break;

            case 13:
                $path = '_so_cob';
                break;

            case 14:
                $path = '_ruangan';
                break;

            case 15:
                $path = '_uom';
                break;

            case 16:
                $path = '_patientdebt';
                break;
            
            case 20:
                $path = '_dailycutoff';
                break;

            default:
                $path = false;
                break;
        }

        return $path;
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = $cache = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $type = $request->get('type', null);
        try {
            $yiiRestfulParams['model'] = $type;
            $response = $this->_restKasir->get('integration/get-data-transaksi?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['sync_id_api']);
                    $value['primary'] = $primaryKey;
                    $disabled = (empty($value['sync_id_api'])) ? false : true;
                    if(isset($value['amount'])) {
                        $value['amount'] = DocoHelpers::formatNumber($value['amount']);
                    }
                    if(isset($value['total_collect'])) {
                        $value['total_collect'] = DocoHelpers::formatNumber($value['total_collect']);
                    }
                    if(isset($value['trans_date'])) {
                        $value['trans_date'] = date("j-M-Y H:i:s", strtotime($value['trans_date']));
                    }
                    if(isset($value['tglproses'])) {
                        $value['tglproses'] = date("j-M-Y H:i:s", strtotime($value['tglproses']));
                    }
                    if(isset($value['facility_name'])) {
                        $value['facility_name'] = $value['facility_name'];
                    }
                    if(isset($value['nama_pasien'])) {
                        $value['nama_pasien'] = $value['nama_pasien'];
                    }

                    if(isset($value['price_subtotal'])) {
                        $value['price_subtotal'] = DocoHelpers::formatNumber($value['price_subtotal']);
                    }

                    if (!empty($value['additional_paket']) && is_array($value['additional_paket'])) {
                        $addonPaket = $this->setNameAndTotalAmount($value['additional_paket']);
                        $value['price_subtotal'] = DocoHelpers::formatNumber($addonPaket['total_detail']);
                        $value['name'] .= $addonPaket['name_detail'];
                    }

                    $value['select_item'] = Html::checkbox('select_item', false, [
                        'id' => 'select_item-'.$primaryKey,
                        'class' => 'select_item',
                        'value' => $value['sync_id_api'],
                    ]);
                    $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=>"/master/dashboard-odoo/detail-response?id=".$primaryKey.'&model='.$type,'onclick'=> 'docoHelper.detail(this)']);
                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }    

    private function setNameAndTotalAmount(array $data)
    {
        $result = [
            'name_detail' => '<ul>',
            'total_detail' => 0
        ];

        foreach ($data as $value) {
            $nameDetail = isset($value['name']) ? $value['name'] : "-";
            $result['name_detail'] .= "<li>{$nameDetail}</li>";

            $result['total_detail'] += isset($value['price_total']) ? $value['price_total'] : 0;
        }

        $result['name_detail'] .= '</ul>';

        return $result;
    }

    public function actionSaleOrderLine()
    {
        $request = Yii::$app->request;
        $type = $request->get('type', 'saleorderline');
        $path = ($type == 'saleorderline') ? '_so_tindakan' : '_so_obat';
        $statusProses = [
            'SUKSES' => 'SUKSES',
            'GAGAL' => 'GAGAL',
            'MENUNGGU PROSES' => 'MENUNGGU PROSES',
            'DALAM PROSES' => 'DALAM PROSES',
        ];
        $typeLine = [
            'ACCRUAL' => 'ACCRUAL',
            'ACCRUAL REVERSAL' => 'ACCRUAL REVERSAL',
            'BILLING' => 'BILLING',
            'BILLING CANCEL' => 'BILLING CANCEL',
            'BILLING_CANCEL' => 'BILLING_CANCEL',
            'BILL CANCEL' => 'BILL CANCEL',
            'CANCEL BILLING' => 'CANCEL BILLING',
            'DISCOUNT' => 'DISCOUNT',
            'DISCOUNT CANCEL' => 'DISCOUNT CANCEL',
        ];

        return $this->renderAjax($path, [
            'statusProses' => $statusProses,
            'typeLine' => $typeLine
        ]);
    }

    public function actionResend()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $response = [];
        try {
            $model = ($request->get('model')) ? $request->get('model') : null;
            $syncIdApi = $post['sync_id_api'];
            $syncIdApi = !is_array($syncIdApi) ? array($syncIdApi) : $syncIdApi;
            $result = $this->_restKasir->post('integration/resync-transaction?model='.$model, [
                'form_params' => [
                    'sync_id_api' => $syncIdApi,
                ]
            ]);
            $result = json_decode($result->getBody(),true);
            return DocoHelpers::response($result['response']);
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
            return DocoHelpers::response($response, 500);
        }
    }

    public function actionDetailResponse($id, $model)
    {
        $request = $this->_restKasir->request('GET', 'integration/get-data-transaksi?id='.DocoHelpers::decrypt($id).'&model='.$model);
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];
        
        if(!empty($attributes) && is_array($attributes)) {
            if(!empty($attributes['header']['sync_respon']) && isset($attributes['detail'])) {
                $payload = [];
                if (in_array($model, [self::TND_LINE, self::OBT_LINE])) {
                    $payload = $this->buildPayloadSaleOrderLine($attributes['detail']);
                } else if ($model == self::PAISEN_DEPOSIT) {
                    $payload = $this->buildPayloadInpatientDeposit($attributes['detail']);
                } else if ($model == self::PAISEN_DEBT) {
                    $payload = $this->buildPayloadPatientDebt($attributes['detail']);
                } else if ($model == self::SCROLL_CASHIER) {
                    $payload = $this->buildPayloadScrollCashier($attributes['detail']);
                }else if ($model == self::SO_BILL) {
                    $payload = $this->buildPayloadSaleOrderBill($attributes['detail']);
                }else if ($model == self::SO_COB) {
                    $payload = $this->buildPayloadSaleOrderCob($attributes['detail']);
                }
                $sync_respon = [
                    'payload' =>  $payload ,
                    'response' => json_decode($attributes['header']['sync_respon']),
                ];

            } else {
                $sync_respon = [
                    'payload' => [],
                    'response' => []
                ];
            }

            $response = json_encode($sync_respon, JSON_PRETTY_PRINT);
        }
        else {
            $response = '<center><h3><strong>Tidak Ada Response</strong><h3></center>';
        }
        
        return $this->renderAjax('_detail', get_defined_vars());
    }

    private function buildPayloadScrollCashier($value)
    {
        return [
            "sync_id_api" => $value['sync_id_api'],
            "user_name" => $value['user_name'],
            "trans_type" => $value['trans_type'],
            "facility_name" => $value['facility_name'],
            "tglproses" => $value['tglproses'],
            "payment_name" => $value['payment_name'],
            "edc_machine" => $value['edc_machine'],
            "total_collect" => $value['total_collect'],
            "note" => $value['note'],
            "state" => $value['state'],
            "admission_id" => (string) $value['admission_id'],
            "admission_no" => $value['admission_no'],
        ];
    }

    private function buildPayloadInpatientDeposit($value)
    {
        return [
            "sync_id_api" => $value['sync_id_api'],
            "tglproses" => $value['tglproses'],
            "trans_no" => $value['trans_no'],
            "trans_date" => $value['trans_date'],
            "trans_type" => $value['trans_type'],
            "reference_no" => $value['reference_no'],
            "admission_no" => $value['admission_no'],
            "patient_name" => $value['patient_name'],
            "user_name" => $value['user_name'],
            "payment_name" => $value['payment_name'],
            "edc_machine" => $value['edc_machine'],
            "amount" => $value['amount'],
            "note" => $value['note'],
            "state" => $value['state'],
            "patient_type" => $value['patient_type'],
            "partner_id" => $value['partner_id'],
            "admission_id" => (string) $value['admission_id']
        ];
    }

    private function buildPayloadPatientDebt($value)
    {
        return [
            "sync_id_api" => (string) $value['sync_id_api'],
            "tglproses" => ($value['tglproses']),
            "trans_no" => isset($value['trans_no']) ? (string) $value['trans_no'] : null,
            "trans_date" => ($value['trans_date']),
            "trans_type" => (string) $value['trans_type'],
            "reference_no" => isset($value['reference_no']) ? (string) $value['reference_no'] : null,
            "admission_id" => (string) $value['admission_id'],
            "admission_no" => (string) $value['admission_no'],
            "partner_id" => isset($value['partner_id']) ? (string) $value['partner_id'] : null,
            "billing_id" => (string) $value['billing_id'],
            "billing_no" => (string) $value['billing_no'],
            "patient_name" => (string) $value['patient_name'],
            "user_name" => (string) $value['user_name'],
            "payment_name" => isset($value['payment_name']) ? (string) $value['payment_name'] : null,
            "edc_machine" => isset($value['edc_machine']) ? (string) $value['edc_machine'] : null,
            "amount" => (float) $value['amount'],
            "note" => isset($value['note']) ? (string) $value['note'] : null,
            "state" => isset($value['state']) ? (string) $value['state'] : null,
            "patient_type" => (string) $value['patient_type']
        ];
    }

    private function buildPayloadSaleOrderLine($value)
    {
        return [
            "sync_id_api" => $value['sync_id_api'],
            "tglproses" => $this->formatDate($value['tglproses']),
            "product_id" => $value['product_id'],
            "partner_id" => $value['partner_id'],
            "name" => $value['name'],
            "product_uom" => (string) $value['product_uom'],
            "product_uom_qty" => $value['product_uom_qty'],
            "price_unit" => $value['price_unit'],
            "price_subtotal" => $value['price_subtotal'],
            "price_total" => $value['price_total'],
            "personal_amount" => $value['personal_amount'],
            "payer_amount" => $value['payer_amount'],
            "order_id" => (string) $value['order_id'],
            "service_categ_id" => (string) $value['service_categ_id'],
            "primary_doc_id" => (string) $value['primary_doc_id'],
            "prescribe_doc_id" => $value['prescribe_doc_id'],
            "perform_doc_id" => (string) $value['perform_doc_id'],
            "location_id" => $value['location_id'],
            "department_id" => $value['department_id'],
            "billno" => $value['billno'],
            "type_line" => $value['type_line'],
            "revenue_type" => $value['revenue_type'],
            "item_specialisation" => $value['item_specialisation'],
            "patient_group" => $value['patient_group'],
            "special_group" => $value['special_group'],
            "special_group2" => $value['special_group2'],
            "service_group" => $value['service_group'],
            "bed_type" => $value['bed_type'],
            "payer" => $value['payer'],
            "payer_code" => $value['payer_code'],
            "payer_type" => $value['payer_type'],
            "payer_name" => $value['payer_name'],
            "order_no" => $value['order_no'],
            "order_date" => $this->formatDate($value['order_date']),
            "is_package" => $value['is_package'],
            "package_name" => $value['package_name'],
            "cost_unit" => $value['cost_unit'],
            "cost_total" => $value['cost_total'],
            "account_analytic_id" => (string) $value['account_analytic_id'],
            "backup_analytic_id" => (string) $value['backup_analytic_id'],
            "bill_date" => $this->formatDate($value['bill_date']),
            "kota" => $value['kota'],
            "kecamatan" => $value['kecamatan'],
            "kelurahan" => $value['kelurahan'],
            "partner_id" => (string) $value['partner_id'],
            "registration_code" => $value['registration_code'],
            "manufacture" => $value['manufacture'],
            "discharge_date" => $this->formatDate($value['discharge_date']),
            "specialization_primary" => $value['specialization_primary'],
            "status_bill" => isset($value['status_bill']) ? $value['status_bill'] : null,
            "state" => isset($value['state']) ? $value['state'] : null,
            "parent_analytic_primary_id" => (string) $value['account_analytic_id'],
        ];
    }

    private function buildPayloadSaleOrderBill($value)
    {
        return [
            "sync_type" => $value['sync_type'],
            "sync_id_api" => $value['sync_id_api'],
            "admission_id" => $value['admission_id'],
            "admission_no" => $value['admission_no'],
            "billno" => $value['billno'],
            "confirmation_date" => $value['confirmation_date'],
            "cancel_date" => $value['cancel_date'],
            "partner_id" => $value['partner_id'],
            "payer_id" => $value['payer_id'],
            "payer_code" => $value['payer_code'],
            "payer_type" => $value['payer_type'],
            "patient_type" => $value['patient_type'],
            "state" => $value['state'],
            "personal_amount" => $value['personal_amount'],
            "payer_amount" => $value['payer_amount'],
            "total_amount" => $value['total_amount'],
            "nama_asuransi" => $value['nama_asuransi'],
            "no_asuransi" => $value['no_asuransi'],
            "status_proses" => $value['status_proses'],
            "tgl_proses" => $this->formatDate($value['tgl_proses']),
            
        ];
    }

    private function buildPayloadSaleOrderCob($value)
    {
        return [
            "sync_type" => $value['sync_type'],
            "sync_id_api" => $value['sync_id_api'],
            "admission_id" => $value['admission_id'],
            "cob_no" => $value['cob_no'],
            "cancel_date" => $value['cancel_date'],
            "payer_id" => $value['payer_id'],
            "state" => $value['state'],
            "payer_amount" => $value['payer_amount'],
            "status_proses" => $value['status_proses'],
            "cob_date" => $this->formatDate($value['cob_date']),
            
        ];
    }

    private function formatDate($value)
    {
        return !empty($value) ? date('Y-m-d H:i:s', strtotime($value)) : null;
    }

    public function actionResendDailyTrx()
    {
        $request = Yii::$app->request;
        $response = [];
        try {
            $date = $request->post('date');
            $key = $request->post('key');
            $result = $this->_restMaster->get('acc-entry/'.$key, [
                'query' => [
                    'date' => $date,
                ]
            ]);
            $result = json_decode($result->getBody(),true);
            return DocoHelpers::response($result['response']);
        } catch (RequestException $e) {
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
            return DocoHelpers::response($response, 500);
        }
    }

    public function actionResendDailyMaster()
    {
        $request = Yii::$app->request;
        $response = [];
        try {
            $key = $request->post('key');
            $result = $this->_restMaster->get('acc-entry/'.$key, [
                'query' => []
            ]);
            $result = json_decode($result->getBody(),true);
            return DocoHelpers::response($result['response']);
        } catch (RequestException $e) {
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
            return DocoHelpers::response($response, 500);
        }
    }
}