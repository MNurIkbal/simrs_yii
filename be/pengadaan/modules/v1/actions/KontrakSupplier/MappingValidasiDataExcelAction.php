<?php
namespace app\modules\v1\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Payterm;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\KontrakSupplierTmpForm;

class MappingValidasiDataExcelAction extends Action
{
    protected $payload = [];
    protected $list_supplier = [];
    protected $list_obatAlkes = [];
    protected $list_payterm = [];
    protected $list_ppn = [];
    protected $result_data = [];

    public function run()
    {
        $request = Yii::$app->request;
        $this->payload = json_decode($request->post('payload', json_encode([], true)), true);
        
        $this->listSupplier();
        $this->listObatAlkes();
        $this->listPayterm();
        $this->listPajak();
    
        $this->mappingValidasiData();

        return $this->controller->responseJson(200, 'Data berhasil divalidasi', $this->result_data);
    }

    private function mappingValidasiData()
    {
        $cekUnique = [];
        $result = [];
        if (is_array($this->payload) && !empty($this->payload)) {
            foreach ($this->payload as $key => $value) {
                $listSupplier = isset($this->list_supplier[ArrayHelper::getValue($value, 'kode_supplier')]) ? $this->list_supplier[ArrayHelper::getValue($value, 'kode_supplier')] : [];
                $listObatAlkes = isset($this->list_obatAlkes[ArrayHelper::getValue($value, 'kode_obat')]) ? $this->list_obatAlkes[ArrayHelper::getValue($value, 'kode_obat')] : [];
                $listPayterm = isset($this->list_payterm[trim(ArrayHelper::getValue($value, 'payterm'))]) ? $this->list_payterm[trim(ArrayHelper::getValue($value, 'payterm'))] : [];
                $listPpn = isset($this->list_ppn[trim(ArrayHelper::getValue($value, 'persen_ppn'))]) ? $this->list_ppn[trim(ArrayHelper::getValue($value, 'persen_ppn'))] : [];

                $model = new KontrakSupplierTmpForm;
                $model->supplier_id = ArrayHelper::getValue($listSupplier, 'supplier_id');
                $model->payterm_id = ArrayHelper::getValue($listPayterm, 'payterm_id');
                $model->jumlah_hari = ArrayHelper::getValue($listPayterm, 'jumlah_hari');
                $model->pajak_id = ArrayHelper::getValue($listPpn, 'pajak_id');
                
                $model->obatalkes_id = ArrayHelper::getValue($listObatAlkes, 'obatalkes_id');
                $model->nama_obat = ArrayHelper::getValue($listObatAlkes, 'nama_obat');
                $model->satuankecil_id = ArrayHelper::getValue($listObatAlkes, 'satuankecil_id');
                $model->attributes = $value;
    
                $model->validate();
                // cek kode supplier
                if (empty($listSupplier)) {
                    $model->addError('kode_supplier','kode Supplier Tidak Sesuai');
                }
                // cek kode obat
                if (empty($listObatAlkes)) {
                    $model->addError('kode_obat','kode Obat Tidak Sesuai');
                }
                // cek payterm
                if (empty($listPayterm)) {
                    $model->addError('payterm','Payterm Tidak Sesuai');
                }
                // cek ppn
                if (!empty($model->persen_ppn) && empty($listPpn)) {
                    $model->addError('Persen PPN','PPN Tidak Sesuai');
                }

                // cek duplikat kode obat dengan no kontrak berbeda
                if (ArrayHelper::getValue($cekUnique, 'kontraksupplier_no') != ArrayHelper::getValue($value, 'kontraksupplier_no') 
                    && ArrayHelper::getValue($cekUnique, 'kode_obat') == ArrayHelper::getValue($value, 'kode_obat')
                ) {
                    $model->addError('kode_obat','Duplikat Kode Obat '.ArrayHelper::getValue($value, 'kode_obat'));
                } else {
                    $cekUnique['kontraksupplier_no'] = ArrayHelper::getValue($value, 'kontraksupplier_no');
                    $cekUnique['kode_obat'] = ArrayHelper::getValue($value, 'kode_obat');
                }
    
                $model->status = $model->getErrors() ? false : true;
                $model->keterangan = str_replace(',', '<br/>', implode(",", array_column($model->getErrors(), 0)));
                $result[] = $model;
            }
        }
        $this->result_data = $result;
    }

    private function listSupplier()
    {
        $list_kode_supplier = ArrayHelper::getColumn($this->payload, 'kode_supplier');
        $model = Supplier::find()->select(['supplier_id', 'supplier_kode', 'supplier_nama'])
            ->where(['IN', 'supplier_kode', $list_kode_supplier])
            ->andWhere(['is_active' => true, 'is_deleted' => false])
            ->asArray()->all();
        
        $this->list_supplier = ArrayHelper::index($model, 'supplier_kode');
    }

    private function listObatAlkes()
    {
        $list_kode_obat = ArrayHelper::getColumn($this->payload, 'kode_obat');
        $model = ObatAlkes::find()->select(['obatalkes_id', 'obatalkes_kode', 'obatalkes_nama', 'satuankecil_id'])
            ->where(['IN', 'obatalkes_kode', $list_kode_obat])
            ->andWhere(['is_active' => true, 'is_deleted' => false])
            ->asArray()->all();
        
        $this->list_obatAlkes = ArrayHelper::index($model, 'obatalkes_kode');
    }

    private function listPayterm()
    {
        $list_payterm = ArrayHelper::getColumn($this->payload, function ($element) {
            return trim(ArrayHelper::getValue($element, 'payterm'));
        });
        $model = Payterm::find()->select(['payterm_id', 'payterm_kode', 'payterm_nama', 'jumlah_hari'])
            ->where(['IN', 'trim(payterm_nama)', $list_payterm])
            ->andWhere(['is_active' => true, 'is_deleted' => false])
            ->asArray()->all();

        $this->list_payterm = ArrayHelper::index($model, 'payterm_nama');
    }

    private function listPajak()
    {
        $list_ppn = ArrayHelper::getColumn($this->payload, function ($element) {
            return (int)trim(ArrayHelper::getValue($element, 'persen_ppn'));
        });
        
        $model = Pajak::find()->select(['pajak_id', 'pajak_name', 'pajak_persen'])
            ->where(['IN', 'pajak_persen', $list_ppn])
            ->andWhere(['is_active' => true, 'is_deleted' => false])
            ->asArray()->all();
        
        $this->list_ppn = ArrayHelper::index($model, 'pajak_persen');
    }   
}