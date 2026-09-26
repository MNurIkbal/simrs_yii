<?php 

namespace app\modules\v1\businessLogic;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\modules\v1\models\TransaksiFormulirBarangDetail;
use app\modules\v1\models\InfoFormSoBarang;
use app\modules\v1\models\StokOpnameBarang;
use app\modules\v1\models\StokOpnameBarangDetail;
use app\modules\v1\models\InfoFormSoBarangDetail;
use app\components\GudangComponent;
use app\modules\v1\models\Barang;
use app\modules\v1\models\InfoStokOpnameBarangDetailView;

class InsertBarangBaruSo {

    protected $formsobarang_id;
    protected $is_update_so;
    protected $ruangan_id;
    protected $list_barang_id = [];
    protected $list_barang = [];

    protected $payload_input_so = [];
    protected $payload_input_new_so = [];
    protected $input_detail_form_so = [];
    protected $input_detail_so = [];

    protected $message_error = [];

    public function __construct($formsobarang_id, $is_update_so = false)
    {
        $this->formsobarang_id = $formsobarang_id;
        $this->is_update_so = $is_update_so;
    }
    
    public function execute()
    {
        $this->payload();
        if ($this->payload_input_new_so) {
            $this->getListBarang();
            $this->validate();
            $this->insertFormSoDetail();
            $this->insertSoDetail();
            $this->setReloadPayload();
        }
        return $this->payload_input_so;
    }

    /**
     * mapping data paylaod SO existing dan new SO
     * $payload_input_so = paylaod SO existing
     * payload_input_new_so = paylaod new SO
     */
    private function payload()
    {
        $request = Yii::$app->request;
        $inputSo = $request->post('inputan_so',[]);
        $payload_input_so = $payload_input_new_so = [];
        foreach($inputSo as $value) {
            $data = json_decode($value, true);
            if (!empty(ArrayHelper::getValue($data, 'formsobarangdetail_id')) && !empty(ArrayHelper::getValue($data, 'formsobarang_id'))) {
                $payload_input_so[] = $data;
            } else {
                $payload_input_new_so[] = $data;
            }
        }
        $this->payload_input_so = $payload_input_so;
        $this->payload_input_new_so = $payload_input_new_so;
        $this->ruangan_id = $request->get('ruangan_id');
    }

    /**
     * get list Master Barang 
     * $this->list_barang_id = list barang_id
     * $this->list_barang = list master barang
     */
    private function getListBarang()
    {
        $this->list_barang_id = ArrayHelper::getColumn($this->payload_input_new_so, 'barang_id', []);
        $masterBarang = Barang::find()->select(['barang_id', 'satuankecil_id', 'barang_harganetto'])->where(['in', 'barang_id', $this->list_barang_id])->asArray()->all();
        $this->list_barang = ArrayHelper::index($masterBarang, 'barang_id');
    }

    /**
     * insert formsobarangdetail_t
     */
    private function insertFormSoDetail()
    {
        $input_detail_form_so = [];
        $input = $this->payload_input_new_so;

        if (is_array($input) && !empty($input)) {
            $formBarang = InfoFormSoBarang::find()->where(['formsobarang_id' => $this->formsobarang_id])->asArray()->one();
            foreach($input as $key => $value) {
                $barang = $this->list_barang[ArrayHelper::getValue($value, 'barang_id')];
                $input_detail_form_so[] = [
                    'barang_id' => ArrayHelper::getValue($value, 'barang_id'),
                    'formsobarang_id' => ArrayHelper::getValue($formBarang, 'formsobarang_id'),
                    'stok' => DocoHelpers::convertToNumber(DocoHelpers::convertCommaToPoint(DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok_sistem', 0)))),
                    'harganetto' => ArrayHelper::getValue($barang, 'barang_harganetto', 0),
                    'satuankecil_id' => ArrayHelper::getValue($barang, 'satuankecil_id'),
                    'is_newso' => true
                ];
            }
            TransaksiFormulirBarangDetail::batchInsert($input_detail_form_so, false);
        }
        $this->input_detail_form_so = $input_detail_form_so;
    }

    /**
     * proses insert ke stokopnamebarangdetail_t hanya ketika proses update / sudah pernah insert ke detail
     * is_update_so default false
     */
    private function insertSoDetail()
    {
        $input_detail_so = [];
        $input = ArrayHelper::index($this->payload_input_new_so, 'barang_id', []);

        if (is_array($input) && !empty($input) && $this->is_update_so) {
            $soBarang = StokOpnameBarang::find()->select([
                'stokopnamebarang_id', 'formsobarang_id'
            ])->where(['formsobarang_id' => $this->formsobarang_id])->one();
            $stokopnamebarang_id = ArrayHelper::getValue($soBarang, 'stokopnamebarang_id');

            $detail = $this->getDetailFormAfterSaveForm();
            $tmpDetail = [];
            foreach ($detail as $value) {
                $idDetail = ArrayHelper::getValue($value, 'formsobarangdetail_id');
                $barangId = ArrayHelper::getValue($value, 'barang_id');
                $dataDetail = isset($input[$barangId]) ? $input[$barangId] : [];
                $stokFisik = !empty($dataDetail['stok_fisik']) ? $dataDetail['stok_fisik'] : 0;
                $selisihStok = ArrayHelper::getValue($value, 'stok',0) - $stokFisik;
                $stokRevisi = !empty($dataDetail['revisi_stok']) ? $dataDetail['revisi_stok'] : null;
                $tmpDetail[$barangId] = $value;
                $input_detail_so[] = [
                    'formsobarangdetail_id' => ArrayHelper::getValue($value, 'formsobarangdetail_id'),
                    'satuankecil_id' => ArrayHelper::getValue($value, 'satuankecil_id'),
                    'stokopnamebarang_id' => $stokopnamebarang_id,
                    'barang_id' => ArrayHelper::getValue($value, 'barang_id'),
                    'volume_fisik' => $stokFisik,
                    'volume_sistem' => ArrayHelper::getValue($value, 'stok', 0),
                    'hargasatuan' => ArrayHelper::getValue($value, 'harganetto', 0),
                    'jumlahharga' => $stokFisik * ArrayHelper::getValue($value, 'harganetto', 0),
                    'harganetto' => ArrayHelper::getValue($value, 'harganetto'),
                    'jumlahnetto' => $stokFisik * ArrayHelper::getValue($value, 'harganetto', 0),
                    'tglperiksafisik' => date('Y-m-d H:i:s'),
                    'jmlselisihstok' => $selisihStok,
                    'revisi_stok' => $stokRevisi,
                    'is_newso' => true
                ];
            }
            StokOpnameBarangDetail::batchInsert($input_detail_so, false);

            $querySoDetail = $this->getDetailSoAfterSave($stokopnamebarang_id);
            $index = 0;
            foreach ($querySoDetail as $key => $value) {
                $indexArr[$index] = ArrayHelper::getValue($value, 'formsobarangdetail_id');
                $updateDataArr[$index] = ArrayHelper::getValue($value, 'stokopnamebarangdetail_id');

                $index++;
            }
            $condition = ['formsobarangdetail_id' => $indexArr];
            $dataUpdate = ['stokopnamebarangdetail_id' => $updateDataArr];
            GudangComponent::updateMultiple('formsobarangdetail_t', $dataUpdate, $condition);
        }
    }

    /**
     * mapping data payload setelah di insert ke form so
     */
    private function setReloadPayload()
    {
        if (!$this->is_update_so) {
            $newFormSo =  $this->getDetailFormAfterSaveForm();
            $mapp_new_so = [];
            foreach ($this->payload_input_new_so as $value) {
                $formSo = isset($newFormSo[ArrayHelper::getValue($value, 'barang_id')]) ? $newFormSo[ArrayHelper::getValue($value, 'barang_id')] : [];
                $value['formsobarangdetail_id'] = ArrayHelper::getValue($formSo, 'formsobarangdetail_id');
                $value['formsobarang_id'] = ArrayHelper::getValue($formSo, 'formsobarang_id');
                $value['harganetto'] = ArrayHelper::getValue($formSo, 'harganetto');
                $value['is_newso'] = ArrayHelper::getValue($formSo, 'is_newso');
                $mapp_new_so[] = $value;
            }
            $this->payload_input_so = array_merge($this->payload_input_so, $mapp_new_so);
        }
    }

    /**
     * validasi so 
     */
    private function validate()
    {
        $infoFormSo = InfoFormSoBarangDetail::find()->select([
            'barang_id', 'barang_nama', 'ruangan_nama', 'noformulir'
        ])
        ->where(['in', 'barang_id', $this->list_barang_id])
        ->andWhere(['ruangan_id' => $this->ruangan_id])->asArray()->all();

        $message = [];
        if (!empty($this->payload_input_new_so)) {
            foreach ($infoFormSo as $key => $value) {
                $message[] = "<b> ". ArrayHelper::getValue($value, 'barang_nama') . " (". ArrayHelper::getValue($value, 'noformulir') . ")</b>";
            }
        }
        if (is_array($message) && $message) {
            throw new \Exception("Barang dengan nama " . implode(", ", $message) . " masih dalam proses Stok Opname");
        }
    }

    /**
     * get data detail formulir so after save formulir  So
     */
    private function getDetailFormAfterSaveForm()
    {
        $newFormSo = InfoFormSoBarangDetail::find()->select([
            'formsobarangdetail_id', 'formsobarang_id', 'stokbarang_id','barang_id','stok','harganetto', 'satuankecil_id','is_newso'
        ])->where(['formsobarang_id' => $this->formsobarang_id, 'is_newso' => true])
        ->andWhere(['in', 'barang_id', $this->list_barang_id])->asArray()->all();

        return ArrayHelper::index($newFormSo, 'barang_id');
    }

    /**
     * get Data Detail So after Save Detail
     */
    private function getDetailSoAfterSave($stokopnamebarang_id)
    {
        return StokOpnameBarangDetail::find()->select([
            'stokopnamebarangdetail_id', 'formsobarangdetail_id', 
            'barang_id', 'tglkadaluarsa', 'harganetto', 'jmlselisihstok', 'volume_fisik', 'volume_sistem', 'is_newso'
        ])->where(['stokopnamebarang_id' => $stokopnamebarang_id, 'is_newso' => true])
        ->andWhere(['in', 'barang_id', $this->list_barang_id])->asArray()->all();
    }
}