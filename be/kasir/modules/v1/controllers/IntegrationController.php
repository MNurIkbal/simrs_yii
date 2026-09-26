<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\SaleOrderLine;
use app\modules\v1\models\IntObatAlkesView;
use app\modules\v1\models\InpatientDeposit;
use app\modules\v1\models\IntPembayaranView;
use app\modules\v1\models\IntSaleOrderUpdateView;
use app\modules\v1\models\Pembayaran;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\FreezeBilling;
use app\modules\v1\models\IntObatAlkesPasienRekap;
use app\modules\v1\models\TindakanPelayananRekap;
use app\modules\v1\models\SaleOrderLineDetail;
use app\modules\v1\models\SaleOrderBilling;
use app\modules\v1\models\SaleOrderBillingRekap;
use app\modules\v1\models\TindakanPelayananUpdate;
use app\modules\v1\models\SaleOrderLineUpdate;
use app\modules\v1\models\IntPemberianPiutangView;
use Doco\components\DocoMessages;
use Doco\components\DocoHelpers;
use app\components\object\FreezeBillingObject;

use app\modules\v1\payload\SerconPayload;
use Doco\Services\InternalService;

class IntegrationController extends DocoActiveController
{
    const ERROR_MESSAGES = 'Tidak ada data tang di proses';
    const T_PEMBAYARAN = 'pembayaran_r';
    const DEPOSIT = 'bayaruangmuka_r';
    const REFUND = 'pengembalianuangmuka_r';
    const AVAILED = 'pemakaianuangmuka_r';
    const TINDAKAN_REKAP = 'tindakanpelayanan_r';
    const TINDAKAN_UPDATE_REKAP = 'tindakanpelayananupdate_r';
    const PEMBERIAN_PIUTANG = 'pemberianpiutang_r';
    const PEMBAYARAN_PIUTANG = 'pembayaranpiutang_r';
    const OBAT_REKAP = 'obatalkespasien_r';
    const BILLING_REKAP = 'int_billing_r';

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["tagihan"] = ["GET"];
        $verbs["call-back-tagihan"] = ["POST"];
        $verbs["obat-pasien"] = ["GET"];
        $verbs["call-back-obat-pasien"] = ["POST"];
        $verbs["uang-muka"] = ["GET"];
        $verbs["call-back-uang-muka"] = ["POST"];
        $verbs["billing"] = ["GET"];
        $verbs["call-back-billing"] = ["POST"];
        $verbs["sale-order"] = ["GET"];
        $verbs["call-back-sale-order"] = ["POST"];
        $verbs["resync-transaction"] = ["POST"];
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionPiutangPasien()
    {
        $sModel = IntPemberianPiutangView::find()
            ->findByIsSending(false)
            ->orderByTglProses()
            ->getDataArray();

        if (empty($sModel)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => self::ERROR_MESSAGES
            ]);
        }

        $listData = $listIdPemberian = $listIdPembayaran = [];
        foreach ($sModel as $key => $value) {
            $listData[] = $value;
            if (strpos($value['sync_id_api'], 'BPU') === false) {
                $listIdPemberian[] = $value['sync_id_api'];
            } else {
                $listIdPembayaran[] = str_replace('BPU', '', $value['sync_id_api']);
            }
        }
        if (!empty($listIdPemberian)) {
            $this->updateIsSending(self::PEMBERIAN_PIUTANG, $listIdPemberian);
        }
        if (!empty($listIdPembayaran)) {
            $this->updateIsSending(self::PEMBAYARAN_PIUTANG, $listIdPembayaran);
        }

        return $listData;
    }

    public function actionCallBackPiutangPasien()
    {
        $request = Yii::$app->request;
        $payload = new SerconPayload;
        $payload->attributes = $request->post();
        $uidSercon = $payload->uid;
        $data = $payload->data;
        if (!empty($data) && is_array($data)) {
            foreach ($data as $value) {
                $tmp = $value;
                if (strpos($value['sync_id_api'], 'BPU') === false) {
                    $tmp['id'] = $value['sync_id_api'];
                    $this->prosesUpdate(self::PEMBERIAN_PIUTANG, $tmp, $uidSercon);
                } else {
                    $tmp['id'] = str_replace('BPU', '', $value['sync_id_api']);
                    $this->prosesUpdate(self::PEMBAYARAN_PIUTANG, $tmp, $uidSercon);
                }
            }
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
            'text' => self::ERROR_MESSAGES
        ]);
    }

    public function actionTagihan()
    {
        $sModel = SaleOrderLine::find()
            ->findByIsSending(false)
            ->findBySendingBill()
            ->orderByTglProses()
            ->getDataArray();

        if (empty($sModel)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => self::ERROR_MESSAGES
            ]);
        }

        $listData = $listId = [];
        foreach ($sModel as $key => $value) {
            $idRekap = $value['id'];
            $listId[] = $idRekap;
            $row = $value;
            $listData[] = $row;
        }
        $this->updateIsSending(self::TINDAKAN_REKAP, $listId);

        $cekJumlah = SaleOrderLine::find()
            ->findByIsSending(false)
            ->findBySendingBill()
            ->orderByTglProses()
            ->count();
        if(!empty($cekJumlah)){
            $listSending = [
                'Odoo' => ['Saleorderlinetindakan'=>[]]
            ];
            (new InternalService)->sendTo($listSending);
        }

        return $listData;
    }

    public function actionCallBackTagihan()
    {
        $request = Yii::$app->request;
        $payload = new SerconPayload;
        $payload->attributes = $request->post();
        $uidSercon = $payload->uid;
        $data = $payload->data;
        if (!empty($data) && is_array($data)) {
            foreach ($data as $value) {
                $this->prosesUpdate(self::TINDAKAN_REKAP, $value, $uidSercon);
            }
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
            'text' => self::ERROR_MESSAGES
        ]);
    }

    public function actionUpdateTindakan()
    {
        $model = TindakanPelayananUpdate::find()
            ->select([
                'tindakanpelayanan_id',
                'dokterpenanggungjawab_id',
                'id',
            ])
            ->findByIsSending(false)
            ->orderBy(['id' => SORT_ASC])
            ->getDataArray();

        if (empty($model)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => self::ERROR_MESSAGES
            ]);
        }

        $parentIds = ArrayHelper::getColumn($model, 'tindakanpelayanan_id');

        $sModel = SaleOrderLineUpdate::find()
                    ->andWhere(['parent_id' => $parentIds])
                    ->orderByTglProses()
                    ->findByIsSending(true)
                    ->findByIsSent(true)
                    ->getDataArray();

        if (empty($sModel)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => self::ERROR_MESSAGES
            ]);
        }

        $listUpdate = $skipTrans = [];
        foreach ($sModel as $value) {
            $parentId = isset($value['parent_id']) ? $value['parent_id'] : null;
            $row = $value;
            if (!empty($parentId)) {
                if (!empty($value['billing_id'])) {
                    if (!empty($value['is_sent_billing'])) {
                        $listUpdate[$parentId][] = $value;
                    } else {
                        $skipTrans[] = $parentId;
                    }
                } else {
                    $listUpdate[$parentId][] = $value;
                }
            }
        }
        // return $listUpdate;

        $packetDataUpdate = [];
        foreach ($model as $value) {
            $idParent = $value['tindakanpelayanan_id'];
            $idRekap = $value['id'];
            if (in_array($idParent, $skipTrans)) {
                continue;
            } else {
                if (isset($listUpdate[$idParent])) {
                    $ids[] = $idRekap;
                    $packetDataUpdate[$idRekap] = $listUpdate[$idParent];
                }
            }
        }

        if (!empty($ids)) {
            $this->updateIsSending(self::TINDAKAN_UPDATE_REKAP, $ids);
        }

        return $packetDataUpdate;

    }

    public function actionCallBackUpdateTindakan()
    {
        $request = Yii::$app->request;
        $payload = new SerconPayload;
        $payload->attributes = $request->post();
        $uidSercon = $payload->uid;
        $data = $payload->data;
        if (!empty($data) && is_array($data)) {
            foreach ($data as $value) {
                $this->prosesUpdate(self::TINDAKAN_UPDATE_REKAP, $value, $uidSercon);
            }
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
            'text' => self::ERROR_MESSAGES
        ]);
    }

    public function actionObatPasien()
    {
        $sModel = IntObatAlkesView::find()
            ->findByIsSending(false)
            ->findBySendingBill()
            ->orderByTglProses()
            ->getDataArray();

        if (empty($sModel)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => self::ERROR_MESSAGES
            ]);
        }

        $listId = ArrayHelper::getColumn($sModel, 'id');
        $this->updateIsSending(self::OBAT_REKAP, $listId);

        $cekJumlah = IntObatAlkesView::find()
            ->findByIsSending(false)
            ->findBySendingBill()
            ->orderByTglProses()
            ->count();
        if(!empty($cekJumlah)){
            $listSending = [
                'Odoo' => ['Saleorderlineobat'=>[]]
            ];
            (new InternalService)->sendTo($listSending);
        }

        return $sModel;
    }

    public function actionCallBackObatPasien()
    {
        $request = Yii::$app->request;
        $payload = new SerconPayload;
        $payload->attributes = $request->post();
        $uidSercon = $payload->uid;
        $data = $payload->data;
        if (!empty($data) && is_array($data)) {
            foreach ($data as $value) {
                $this->prosesUpdate(self::OBAT_REKAP, $value, $uidSercon);
            }
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
            'text' => self::ERROR_MESSAGES
        ]);
    }

    public function actionUangMuka()
    {
        $sModel = InpatientDeposit::find()
            ->findByIsSending(false)
            ->findBySendingBill()
            ->orderByTglProses()
            ->getDataArray();

        if (empty($sModel)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => self::ERROR_MESSAGES
            ]);
        }

        $listIdUm = $listIdPum = $listIdPkum = [];
        $mInstance = new InpatientDeposit;
        foreach ($sModel as $value) {
            $syncId = $value['sync_id_api'];
            $mInstance->setIdRekap($syncId);
            switch ($mInstance->tipe_rekap) {
                case InpatientDeposit::DEPOSIT :
                    $listIdUm[] = $mInstance->id_rekap;
                    break;
                case InpatientDeposit::REFUND:
                    $listIdPum[] = $mInstance->id_rekap;
                    break;
                case InpatientDeposit::AVAILED:
                    $listIdPkum[] = $mInstance->id_rekap;
                    break;
                default:

                    break;
            }
        }

        if (!empty($listIdUm)) {
            $this->updateIsSending(self::DEPOSIT, $listIdUm);
        }

        if (!empty($listIdPum)) {
            $this->updateIsSending(self::REFUND, $listIdPum);
        }

        if (!empty($listIdPkum)) {
            $this->updateIsSending(self::AVAILED, $listIdPkum);
        }

        return $sModel;
    }

    public function actionCallBackUangMuka()
    {
        $request = Yii::$app->request;
        $payload = new SerconPayload;
        $payload->attributes = $request->post();
        $uidSercon = $payload->uid;
        $data = $payload->data;

        if (!empty($data) && is_array($data)) {
            $mInstance = new InpatientDeposit;
            foreach ($data as $value) {
                $syncId = isset($value['sync_id_api']) ? $value['sync_id_api'] : null;
                $mInstance->setIdRekap($syncId);
                switch ($mInstance->tipe_rekap) {
                    case InpatientDeposit::DEPOSIT:
                        $this->prosesUpdate(self::DEPOSIT, $value, $uidSercon);
                        break;
                    case InpatientDeposit::REFUND:
                        $this->prosesUpdate(self::REFUND, $value, $uidSercon);
                        break;
                    case InpatientDeposit::AVAILED:
                        $this->prosesUpdate(self::AVAILED, $value, $uidSercon);
                        break;
                    default:

                        break;
                }
            }
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
            'text' => self::ERROR_MESSAGES
        ]);
    }

    public function actionBilling()
    {
        $sModel = IntPembayaranView::find()
            ->findByIsSending(false)
            ->findBySendingBill()
            ->getDataArray();

        if (empty($sModel)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => self::ERROR_MESSAGES
            ]);
        }

        $listIdByr = $listIdUm = $listIdPum = [];
        $mInstance = new IntPembayaranView;
        foreach ($sModel as $value) {
            $syncId = $value['sync_id_api'];
            $mInstance->setIdRekap($syncId);
            switch ($mInstance->tipe_rekap) {
                case IntPembayaranView::BAYAR:
                    $listIdByr[] = $mInstance->id_rekap;
                    break;
                case IntPembayaranView::DEPOSIT :
                    $listIdUm[] = $mInstance->id_rekap;
                    break;
                case IntPembayaranView::REFUND:
                    $listIdPum[] = $mInstance->id_rekap;
                    break;
                default:

                    break;
            }
        }


        if (!empty($listIdByr)) {
            $this->updateIsSending(self::T_PEMBAYARAN, $listIdByr);
        }

        if (!empty($listIdUm)) {
            $this->updateIsSendingSrc(self::DEPOSIT, $listIdUm);
        }

        if (!empty($listIdPum)) {
            $this->updateIsSendingSrc(self::REFUND, $listIdPum);
        }

        return $sModel;
    }

    public function actionCallBackBilling()
    {
        $request = Yii::$app->request;
        $payload = new SerconPayload;
        $payload->attributes = $request->post();
        $uidSercon = $payload->uid;
        $data = $payload->data;
        if (!empty($data) && is_array($data)) {
            $mInstance = new IntPembayaranView;
            foreach ($data as $value) {
                $syncId = isset($value['sync_id_api']) ? $value['sync_id_api'] : null;
                $mInstance->setIdRekap($syncId);
                switch ($mInstance->tipe_rekap) {
                    case IntPembayaranView::BAYAR:
                        $this->prosesUpdate(self::T_PEMBAYARAN, $value, $uidSercon);
                        break;
                    case IntPembayaranView::DEPOSIT:
                        $this->prosesUpdateSrc(self::DEPOSIT, $value, $uidSercon);
                        break;
                    case IntPembayaranView::REFUND:
                        $this->prosesUpdateSrc(self::REFUND, $value, $uidSercon);
                        break;
                    default:

                        break;
                }
            }
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
            'text' => self::ERROR_MESSAGES
        ]);
    }

    public function actionSaleOrderBill()
    {
        $sModel = SaleOrderBilling::find()
            ->andWhere(['and',
                ['is_update' => false],
                ['is_sending' => false]
            ])
            ->orWhere(['and',
                ['is_update' => true],
                ['is_update_sending' => false]
            ])
            ->getDataArray();

        if (empty($sModel)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => self::ERROR_MESSAGES
            ]);
        }

        $listIdCreate = $listIdUpdate = [];
        foreach ($sModel as $value) {
            if (!empty($value['is_update'])) {
                $listIdUpdate[] = $value['sync_id_api'];
            } else {
                $listIdCreate[] = $value['sync_id_api'];
            }
        }

        if (!empty($listIdCreate)) {
            $this->updateIsSending(self::BILLING_REKAP, $listIdCreate);
        }

        if (!empty($listIdUpdate)) {
            $this->updateIsSending(self::BILLING_REKAP, $listIdUpdate, 'is_update_sending');
        }

        return $sModel;
    }

    public function actionCallBackSaleOrderBill()
    {
        $request = Yii::$app->request;
        $payload = new SerconPayload;
        $payload->attributes = $request->post();
        $uidSercon = $payload->uid;
        $data = $payload->data;
        if (!empty($data) && is_array($data)) {
            foreach ($data as $value) {
                $idRekap = !empty($value['id']) ? $value['id'] : null;
                $isError = !empty($value['is_error']) ? $value['is_error'] : false;
                if (!empty($idRekap)) {
                    $qBill = SaleOrderBillingRekap::find()->andWhere([
                        'id' => $idRekap
                    ])->asArray()->one();
                    $isUpdate = isset($qBill['is_update']) ? $qBill['is_update'] : false;
                    $default = ['is_update' => $isError ? false : true];
                    if (!empty($isUpdate)) {
                        $default['is_update_sent'] = true;
                    }
                    $this->prosesUpdate(self::BILLING_REKAP, $value, $uidSercon, $isUpdate, $default);
                }
            }
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
            'text' => self::ERROR_MESSAGES
        ]);
    }

    public function actionSaleOrder()
    {
        $sModel = IntSaleOrderUpdateView::find()->orderBy([
            'id' => SORT_ASC
        ])->getDataArray();

        if (empty($sModel)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => self::ERROR_MESSAGES
            ]);
        }

        $listId = ArrayHelper::getColumn($sModel, 'id');

        $this->updateSync(self::T_PEMBAYARAN, ['is_update' => true], ['id' => $listId]);
        return $sModel;
    }

    public function actionCallBackSaleOrder()
    {
        $request = Yii::$app->request;
        $payload = new SerconPayload;
        $payload->attributes = $request->post();
        $uidSercon = $payload->uid;
        $data = $payload->data;
        if (!empty($data) && is_array($data)) {
            foreach ($data as $value) {
                $this->prosesUpdate(self::T_PEMBAYARAN, $value, $uidSercon, true);
            }
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
            'text' => self::ERROR_MESSAGES
        ]);
    }

    private function prosesUpdateSrc($tabel, $value, $uidSercon)
    {
        $isError = (!empty($value['is_error']) && isset($value['is_error'])); 
        $idRekap = isset($value['id']) ? $value['id'] : null;
        $response = isset($value['response']) ? json_encode($value['response']) : null;
        $updateAttr = [
            'is_sent_scr' => !$isError,
            'id_sync_sercon_scr' => $uidSercon,
            'sync_respon_scr' => $response
        ];

        $condition = [
            'id' => $idRekap
        ];
        $this->updateSync($tabel, $updateAttr, $condition);
    }

    /**
     * @param  string $tabel
     * @param  array $value
     * @param  string $uidSercon
     * @return void
     */
    private function prosesUpdate($tabel, $value, $uidSercon, $isUpdate = false, $extra = [])
    {
        $isError = (!empty($value['is_error']) && isset($value['is_error'])); 
        $idRekap = isset($value['id']) ? $value['id'] : null;
        $response = isset($value['response']) ? json_encode($value['response']) : null;
        $updateAttr = [
            'is_sent' => !$isError,
            'id_sync_sercon' => $uidSercon,
            'sync_respon' => $response
        ];

        if ($isUpdate) {
            $updateAttr = [
                'id_sync_sercon_update' => $uidSercon,
                'sync_respon_update' => $response,
            ];
        }

        $condition = [
            'id' => $idRekap
        ];

        $this->updateSync($tabel, array_merge($updateAttr, $extra), $condition);
    }

    private function updateIsSendingSrc($tabel, $id)
    {
        $this->updateIsSending($tabel, $id, 'is_sending_scr');
    }

    /**
     * update is_sending
     * @param  string $tabel
     * @param  integer|array $id   
     * @return void
     */

    private function updateIsSending($tabel, $id, $attrIsSend = 'is_sending')
    {
        $this->updateSync($tabel, [$attrIsSend => true], ['id' => $id]);
    }

    private function syncUpdateSrc($tabel, $id)
    {
        $this->syncUpdateIsSending($tabel, $id, [
            'is_sending_scr' => false,
            'id_sync_sercon_scr' => null,
            'sync_respon_scr' => null,
        ], [
            'is_sent_scr' => false
        ]);
    }

    private function syncUpdateIsSending($tabel, $id, $attributes = [], $extends = [])
    {
        if (empty($attributes)) {
            $attributes = [
                'is_sending' => false,
                'id_sync_sercon' => null,
                'sync_respon' => null
            ];
        }

        $baseCond = ['id' => $id];
        if (!empty($extends)) {
            $baseCond = array_merge($baseCond, $extends);
        } else {
            $baseCond = array_merge($baseCond, [
                'is_sent' => false
            ]);
        }
        $this->updateSync($tabel, $attributes, $baseCond);
    }

    /**
     * Helper Update Sync
     * @param  string $tabel    
     * @param  bool $isSent   
     * @param  string $uidSercon
     * @param  integer $idRekap  
     * @return void
     */
    private function updateSync($tabel, $attr = [], $condition = [])
    {
        Yii::$app->db->createCommand()->update($tabel,$attr, $condition)->execute();
    }

    public function actionFreezeBilling()
    {
        $request = Yii::$app->request;
        $payload = $request->post() != null ? $request->post() : [] ;
        $objFreez = new FreezeBillingObject();
        $objFreez->setAttributes($payload);
        $payloadDetails = $objFreez->details;
        $strinvoice = 'invoice_id';
        $strsuccess = 'success';
        $strerror = 'error';
        $status_response = 'success';
        $strorderid = 'order_id';
        $strorderno = 'order_no';
        $strpendaftaranid = 'pendaftaran_id';
        $detailsResponse = [];
        
        if($objFreez->invoice_status == 'confirm'){
            $objFreez->invoice_status = 1;
            $freeze_status = "freeze";
        }elseif ($objFreez->invoice_status == 'cancel'){
            $objFreez->invoice_status = 0;
            $freeze_status = "unfreeze";
        }else{
            $freeze_status = $strerror;
        }
        if($payloadDetails != null){
            foreach($payloadDetails as $pd){
                $str_order_sync_id_api = 'order_sync_id_api';
                $q_order_sync_id_api = isset($pd[$str_order_sync_id_api]) && preg_replace( '/[^0-9]/', '', $pd[$str_order_sync_id_api]) == $pd[$str_order_sync_id_api] ? $pd[$str_order_sync_id_api]  : null;
                $order_sync_id_api = isset($pd[$str_order_sync_id_api]) ? $pd[$str_order_sync_id_api]   : null;
                if(!empty($q_order_sync_id_api)){
                    $q_pendaftaran = Pendaftaran::find()->where([$strpendaftaranid => $q_order_sync_id_api])->one();
                    $pendaftaran_id = isset($q_pendaftaran[$strpendaftaranid]) ? $q_pendaftaran[$strpendaftaranid]: null ;
                    $objFreez->pendaftaran_id = '' . $pendaftaran_id . '';
                    $objFreez->details = $pd;
                    if($pendaftaran_id){ 
                        $freezeBilling = FreezeBilling::find()->where([$strpendaftaranid => $objFreez->pendaftaran_id])->one();
                        $freezeBilling = !empty($freezeBilling) ? $freezeBilling : new FreezeBilling;
                        if($freeze_status != $strerror){
                            try {
                                $freezeBilling->attributes = $objFreez->buildArray();
                                $freezeBilling->save();
                                $messagedetails = 'Successfully to '. $freeze_status .' bills';
                                $status = $strsuccess;
                                
                            } catch(\yii\db\Exception $e) {
                                $message = "Failed to  '. $freeze_status .' bill, something wrong!";
                            } 
                        }else{
                            $messagedetails = 'Status frezze tidak dikenali.';
                            $status = $strerror;
                            $status_response = $strerror;
                            
                        }
                    }else{
                        $message = 'Failed invoice no. ' . $order_sync_id_api . ' not found';
                        $messagedetails = 'Failed bill not found';
                        $status = $strerror;
                        $status_response = $strerror;
                    }
                }else{
                    $message = 'Failed invoice no. ' . $order_sync_id_api . ' not found';
                    $messagedetails = 'Failed bill not found';
                    $status = $strerror;
                    $status_response = $strerror;
                }
                
                $detailsResponse[] = [
                    $strorderid => isset($pd[$strorderid]) ? $pd[$strorderid] : null,
                    $str_order_sync_id_api =>  $order_sync_id_api,
                    $strorderno => isset($pd[$strorderno]) ? $pd[$strorderno] : null,
                    'status' => $status,
                    'message' => isset($messagedetails) ? $messagedetails : $message,
                ];
            }
        }else{
            $message = "Failed bill not found";
            $status_response = $strerror;
        }
        return [
                    $strinvoice => $objFreez->invoice_id,
                    'status' => 200,
                    'status_response' => $status_response,
                    'message' => isset($message) ? $message : $messagedetails,
                    'details' => $detailsResponse,
                ];
    }

    public function actionResyncTransaction($model) 
    {
        $request = Yii::$app->request;
        $listSyncIdApi = $request->post('sync_id_api', []);
        $listSyncIdApi = !empty($listSyncIdApi) && is_array($listSyncIdApi) ? $listSyncIdApi : [];
        $listError = [];
        switch ($model) {
            case 'saleorderline':
                $model = new SaleOrderLine;
                $listTindakan = [];
                foreach ($listSyncIdApi as $syncId) {
                    $model->setIdRekap($syncId);
                    switch ($model->tipe_rekap) {
                        case SaleOrderLine::TINDAKAN :
                            $listTindakan[] = $model->id_rekap;
                            break;
                        default:
                            $listError[] = $syncId;
                            break;
                    }
                }

                if (!empty($listTindakan)) {
                    $this->syncUpdateIsSending(self::TINDAKAN_REKAP, $listTindakan);
                }

                break;
            case 'obatalkespasien':
                $model = new IntObatAlkesView;
                $listObatAlkes = [];
                foreach ($listSyncIdApi as $syncId) {
                    $model->setIdRekap($syncId);
                    switch ($model->tipe_rekap) {
                        case IntObatAlkesView::OBAT :
                            $listObatAlkes[] = $model->id_rekap;
                            break;
                        default:
                            $listError[] = $syncId;
                            break;
                    }
                }

                if (!empty($listObatAlkes)) {
                    $this->syncUpdateIsSending(self::OBAT_REKAP, $listObatAlkes);
                }

                break;
            case 'patientdebt':

                $pemberianPiutang = [];
                $pembayaranPiutang = [];

                if (!empty($listSyncIdApi)) {
                    foreach ($listSyncIdApi as $_id) {
                        if (strpos($_id, 'BPU') === false) {
                            $pemberianPiutang[] = $_id;
                        } else {
                            $pembayaranPiutang[] = str_replace('BPU', '', $_id);
                        }
                    }
                    if (!empty($pemberianPiutang)) {
                        $this->syncUpdateIsSending(self::PEMBERIAN_PIUTANG, $listSyncIdApi);
                    }
                    if (!empty($pembayaranPiutang)) {
                        $this->syncUpdateIsSending(self::PEMBAYARAN_PIUTANG, $pembayaranPiutang);
                    }
                }

                break;
            case 'pasiendeposit':
                $model = new InpatientDeposit;
                $listIdUm = $listIdPum = $listIdPkum = [];
                foreach ($listSyncIdApi as $syncId) {
                    $model->setIdRekap($syncId);
                    switch ($model->tipe_rekap) {
                        case InpatientDeposit::DEPOSIT :
                            $listIdUm[] = $model->id_rekap;
                            break;
                        case InpatientDeposit::REFUND:
                            $listIdPum[] = $model->id_rekap;
                            break;
                        case InpatientDeposit::AVAILED:
                            $listIdPkum[] = $model->id_rekap;
                            break;
                        default:
                            $listError[] = $syncId;
                            break;
                    }
                }

                if (!empty($listIdUm)) {
                    $this->syncUpdateIsSending(self::DEPOSIT, $listIdUm);
                }

                if (!empty($listIdPum)) {
                    $this->syncUpdateIsSending(self::REFUND, $listIdPum);
                }

                if (!empty($listIdPkum)) {
                    $this->syncUpdateIsSending(self::AVAILED, $listIdPkum);
                }

                break;
            case 'scrolkasir':
                $model = new IntPembayaranView;
                $listIdByr = $listIdUm = $listIdPum = [];
                foreach ($listSyncIdApi as $syncId) {
                    $model->setIdRekap($syncId);
                    switch ($model->tipe_rekap) {
                        case IntPembayaranView::BAYAR:
                            $listIdByr[] = $model->id_rekap;
                            break;
                        case IntPembayaranView::DEPOSIT :
                            $listIdUm[] = $model->id_rekap;
                            break;
                        case IntPembayaranView::REFUND:
                            $listIdPum[] = $model->id_rekap;
                            break;
                        default:
                            $listError[] = $syncId;
                            break;
                    }
                }

                if (!empty($listIdByr)) {
                    $this->syncUpdateIsSending(self::T_PEMBAYARAN, $listIdByr);
                }

                if (!empty($listIdUm)) {
                    $this->syncUpdateSrc(self::DEPOSIT, $listIdUm);
                }

                if (!empty($listIdPum)) {
                    $this->syncUpdateSrc(self::REFUND, $listIdPum);
                }

                break;
            case 'saleorderbill':
                $model = new SaleOrderBilling;
                $listBilling = [];
                $attributes = [
                    'is_sending' => false,
                    'id_sync_sercon' => null,
                    'sync_respon' => null,
                    'is_update_sending' => false
                ];
                foreach ($listSyncIdApi as $syncId) {
                    $listBilling = [];
                    $model->setIdRekap($syncId);
                    $listBilling[] = $model->id_rekap;
                    $query = SaleOrderBillingRekap::find()->where(['id' => $model->id_rekap])->asArray()->one();
                    if(!empty($query['is_update'] && $query['is_update'] ==  true )){
                        $attributes = [
                            'is_sending' => false,
                            'id_sync_sercon' => null,
                            'sync_respon' => null,
                            'is_update_sending' => false
                        ];
                    }elseif(!empty($query['is_update'] && $query['is_update'] ==  false )){
                        $attributes = [
                            'is_sending' => false,
                            'id_sync_sercon' => null,
                            'sync_respon' => null,
                            'is_update_sending' => true
                        ];
                    }
                    $exec = $this->syncUpdateIsSending(self::BILLING_REKAP, $listBilling, $attributes);
                }

                break;
            default:
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                    'text' => 'Nama Model tidak terdaftar'
                ]);
                break;
        }

        return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM_DATA,[
            'data' => [
                'failed' => $listError
            ]
        ]);
    }

    public function actionGetDataTransaksi($model, $tipe = null, $id = null){
        try{
            if($model){
                $strquery = "query";
                $strstatus = "status";
                $strtitle = "title";
                $strtext = "text";
                if($model == 'saleorderline'){
                    if($id == null) {
                        $model = new SaleOrderLine;
                        $query = $model::find(true);
                        $between = false;
                        $start = date('Y-m-d 00:00:00');
                        $end = date('Y-m-d 23:59:00');
                        if(isset($_GET['advanced-filter'])) {
                            if(isset($_GET['advanced-filter']['tglproses'])) {
                                $explode = explode(" - ", $_GET['advanced-filter']['tglproses']);
                                if(count($explode) == 2) {
                                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                                }
                                unset($_GET['advanced-filter']['tglproses']);
                                $between = true;
                            }
                            if (isset($_GET['advanced-filter']['status_proses'])) {
                                $query->andWhere(['status_proses' => trim($_GET['advanced-filter']['status_proses'])]);
                                unset($_GET['advanced-filter']['status_proses']);
                            }
                            if (isset($_GET['advanced-filter']['type_line'])) {
                                $query->andWhere(['type_line' => trim($_GET['advanced-filter']['type_line'])]);
                                unset($_GET['advanced-filter']['type_line']);
                            }
                        }
                        $query->andWhere(['between', 'tglproses', $start, $end]);
                        $query = DocoRestActiveFilter::advancedFilter($model, $query);
                        $activeRecord = (new ActiveDataProvider([
                            $strquery => $query,
                        ]));

                        $models = $activeRecord->getModels();
                        $listData = $listId = $prodTmp = [];
                        foreach ($models as $key => $value) {
                            $idRekap = $value['id'];
                            $prdId = $value['product_id'];
                            $listId[] = $idRekap;
                            $row = $value;
                            if (!empty($value['jenis']) && $value['jenis'] == 'PAKET' && !isset($prodTmp[$prdId])) {
                                $prodTmp[$prdId] = SaleOrderLineDetail::find()->select([
                                    'name',
                                    'price_total',
                                ])->andWhere([
                                    'id' => $idRekap
                                ])->asArray()->all();
                            }

                            $row['additional_paket'] = isset($prodTmp[$prdId]) ? $prodTmp[$prdId] : null;

                            $listData[] = $row;
                        }

                        return [
                            'data' => $listData,
                            '_meta' => [
                                'totalCount' => $activeRecord->getTotalCount(),
                                'pageCount' => $activeRecord->getPagination()->getPageCount(),
                                'currentPage' => $activeRecord->getPagination()->getPage() ? $activeRecord->getPagination()->getPage() : 1,
                                'perPage' => $activeRecord->getPagination()->getPageSize(),
                            ],
                        ];
                    }else{
                        $model_saleorder = new SaleOrderLine;
                        $detail = $model_saleorder::find()->andWhere([
                            'sync_id_api' => $id
                        ])->one();
                        $model_saleorder->setIdRekap($id);
                        $model = new TindakanPelayananRekap;
                        $query = $model::find()->where(['id' => $model_saleorder->id_rekap])->one();
                        return [
                            'header' => $query,
                            'detail' => $detail
                        ];
                    }
                }elseif($model == 'obatalkespasien'){
                    if($id == null) {
                        $model = new IntObatAlkesView;
                        $query = $model::find(true);
                        $between = false;
                        $start = date('Y-m-d 00:00:00');
                        $end = date('Y-m-d 23:59:00');
                        if(isset($_GET['advanced-filter'])) {
                            if(isset($_GET['advanced-filter']['tglproses'])) {
                                $explode = explode(" - ", $_GET['advanced-filter']['tglproses']);
                                if(count($explode) == 2) {
                                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                                }
                                unset($_GET['advanced-filter']['tglproses']);
                                $between = true;
                            }
                            if (isset($_GET['advanced-filter']['status_proses'])) {
                                $query->andWhere(['status_proses' => trim($_GET['advanced-filter']['status_proses'])]);
                                unset($_GET['advanced-filter']['status_proses']);
                            }
                            if (isset($_GET['advanced-filter']['type_line'])) {
                                $query->andWhere(['type_line' => trim($_GET['advanced-filter']['type_line'])]);
                                unset($_GET['advanced-filter']['type_line']);
                            }
                        }
                        $query->andWhere(['between', 'tglproses', $start, $end]);
                        $query = DocoRestActiveFilter::advancedFilter($model, $query);
                        return new ActiveDataProvider([
                            $strquery => $query,
                        ]);
                    }else{
                        $model_obatalkes = new IntObatAlkesView;
                        $detail = $model_obatalkes::find()->andWhere([
                            'sync_id_api' => $id
                        ])->one();
                        $model_obatalkes->setIdRekap($id);
                        $model = new IntObatAlkesPasienRekap;
                        $query = $model::find()->where(['id' => $model_obatalkes->id_rekap])->one();
                        return [
                            'header' => $query,
                            'detail' => $detail
                        ];
                    }
                } elseif ($model ==  'pasiendeposit') {
                    if($id == null) {
                        $model = new InpatientDeposit;
                        $query = $model::find(true);
                        $between = false;
                        $start = date('Y-m-d 00:00:00');
                        $end = date('Y-m-d 23:59:00');
                        if(isset($_GET['advanced-filter'])) {
                            if(isset($_GET['advanced-filter']['trans_date'])) {
                                $explode = explode(" - ", $_GET['advanced-filter']['trans_date']);
                                if(count($explode) == 2) {
                                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                                }
                                unset($_GET['advanced-filter']['trans_date']);
                                $between = true;
                            }
                        }
                        $query->andWhere(['between', 'trans_date', $start, $end]);
                        
                        $query = DocoRestActiveFilter::advancedFilter($model, $query);
                        return new ActiveDataProvider([
                            $strquery => $query,
                            ]);
                    }else{
                        $model_pasiendeposit = new InpatientDeposit;
                        $detail = $model_pasiendeposit::find()->andWhere([
                            'sync_id_api' => $id
                        ])->one();
                        $model_pasiendeposit->setIdRekap($id);
                        $id_rekap = $model_pasiendeposit->id_rekap;
                        $result = [];
                        if ($model_pasiendeposit->tipe_rekap) {
                            if( $model_pasiendeposit->tipe_rekap == InpatientDeposit::DEPOSIT){
                                $table = self::DEPOSIT;
                            }elseif($model_pasiendeposit->tipe_rekap == InpatientDeposit::REFUND ){
                                $table = self::REFUND;
                            }elseif($model_pasiendeposit->tipe_rekap ==  InpatientDeposit::AVAILED ){
                                $table = self::AVAILED;
                            }else{
                                return $result;
                            }
                            $q = 'select * from ' . $table . ' where id =' . $id_rekap;
                            $result = Yii::$app->db->createCommand($q)->queryOne();
                        }
                        return [
                            'header' => $result,
                            'detail' => $detail
                        ];
                    }
                } elseif($model ==  'scrollkasir') {
                    if($id == null) {
                        $model = new IntPembayaranView;
                        $query = $model::find(true);
                        $between = false;
                        $start = date('Y-m-d 00:00:00');
                        $end = date('Y-m-d 23:59:00');
                        if(isset($_GET['advanced-filter'])) {
                            if(isset($_GET['advanced-filter']['tglproses'])) {
                                $explode = explode(" - ", $_GET['advanced-filter']['tglproses']);
                                if(count($explode) == 2) {
                                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                                }
                                unset($_GET['advanced-filter']['tglproses']);
                                $between = true;
                            }
                        }
                        $query->andWhere(['between', 'tglproses', $start, $end]);
                        $query = DocoRestActiveFilter::advancedFilter($model, $query);
                        return new ActiveDataProvider([
                            $strquery => $query,
                        ]);
                    } else {
                        $model_scrollkasir = new IntPembayaranView;
                        $detail = $model_scrollkasir::find()->andWhere([
                            'sync_id_api' => $id
                        ])->one();
                        $model_scrollkasir->setIdRekap($id);
                        $id_rekap = $model_scrollkasir->id_rekap;
                        $result = [];
                        if ($model_scrollkasir->tipe_rekap) {
                            if( $model_scrollkasir->tipe_rekap == IntPembayaranView::DEPOSIT){
                                $table = self::DEPOSIT;
                            }elseif($model_scrollkasir->tipe_rekap == IntPembayaranView::REFUND ){
                                $table = self::REFUND;
                            }elseif($model_scrollkasir->tipe_rekap ==  IntPembayaranView::BAYAR ){
                                $table = self::T_PEMBAYARAN;
                            }else{
                                return $result;
                            }
                            $q = 'select * from ' . $table . ' where id =' . $id_rekap;
                            $result = Yii::$app->db->createCommand($q)->queryOne();
                        }
                        return [
                            'header' => $result,
                            'detail' => $detail
                        ];
                    }
                }elseif($model == 'patientdebt'){
                    if($id == null) {
                        $model = new IntPemberianPiutangView;
                        $query = $model::find(true);
                        $between = false;
                        $start = date('Y-m-d 00:00:00');
                        $end = date('Y-m-d 23:59:00');
                        if(isset($_GET['advanced-filter'])) {
                            if(isset($_GET['advanced-filter']['trans_date'])) {
                                $explode = explode(" - ", $_GET['advanced-filter']['trans_date']);
                                if(count($explode) == 2) {
                                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                                }
                                unset($_GET['advanced-filter']['trans_date']);
                                $between = true;
                            }
                        }
                        $query->andWhere(['between', 'trans_date', $start, $end]);
                        $query = DocoRestActiveFilter::advancedFilter($model, $query);
                        return new ActiveDataProvider([
                            $strquery => $query,
                        ]);
                    } else {
                        $model_piutang = new IntPemberianPiutangView;
                        $detail = $model_piutang::find()->andWhere([
                            'sync_id_api' => $id
                        ])->one();
                        if ($detail->trans_type == 'Debt') {
                            $q = 'select * from ' . self::PEMBERIAN_PIUTANG . ' where id =' . $id;
                        } else {
                            $q = 'select * from ' . self::PEMBAYARAN_PIUTANG . ' where id =' . str_replace('BPU', '', $id);
                        }
                        $result = Yii::$app->db->createCommand($q)->queryOne();
                        return [
                            'header' => $result,
                            'detail' => $detail
                        ];
                    }
                }elseif($model == 'saleorderbill'){
                    if($id == null) {
                        $model = new SaleOrderBilling;
                        $query = $model::find(true);
                        $between = false;
                        $start = date('Y-m-d 00:00:00');
                        $end = date('Y-m-d 23:59:00');
                        if(isset($_GET['advanced-filter'])) {
                            if(isset($_GET['advanced-filter']['tgl_proses'])) {
                                $explode = explode(" - ", $_GET['advanced-filter']['tgl_proses']);
                                if(count($explode) == 2) {
                                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                                }
                                unset($_GET['advanced-filter']['tgl_proses']);
                                $between = true;
                            }
                            if (isset($_GET['advanced-filter']['status_proses'])) {
                                $query->andWhere(['status_proses' => trim($_GET['advanced-filter']['status_proses'])]);
                                unset($_GET['advanced-filter']['status_proses']);
                            }
                        }
                        $query->andWhere(['between', 'tgl_proses', $start, $end]);
                        $query = DocoRestActiveFilter::advancedFilter($model, $query);
                        return new ActiveDataProvider([
                            $strquery => $query,
                            ]);
                    }else{
                        $model_salorderbill = new SaleOrderBilling;
                        $detail = $model_salorderbill::find()->andWhere([
                            'sync_id_api' => $id
                        ])->one();
                        $model_salorderbill->setIdRekap($id);
                        $model = new SaleOrderBillingRekap;
                        $query = $model::find()->where(['id' => $model_salorderbill->id_rekap])->one();
                        return [
                            'header' => $query,
                            'detail' => $detail
                        ];
                    }
                }else{
                    return [
                        $strstatus => 200,
                        $strtitle => 'Proses Gagal',
                        $strtext => 'Model transaksi tidak ditemukan'
                    ];
                }
            }
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return $response['response'] = [
                'message' => $e->getMessage(),
                'status'  => 500  
            ];
        }
    }

}
