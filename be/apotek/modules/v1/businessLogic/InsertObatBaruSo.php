<?php 

namespace app\modules\v1\businessLogic;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\modules\v1\models\StokOpnameDetail;
use app\modules\v1\models\InfoStokOpnameView;
use app\modules\v1\models\StokOpname;
use app\modules\v1\models\DetailFormulirStokOpnameView;
use app\modules\v1\models\FormStokOpname;
use app\components\ApotekComponent;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\InfoStokOpnameDetailView;

class InsertObatBaruSo {

    protected $formulirstokopname_id;
    protected $is_update_so;
    protected $ruangan_id;
    protected $list_obat_id = [];
    protected $list_obat = [];

    protected $payload_input_so = [];
    protected $payload_input_new_so = [];
    protected $input_detail_form_so = [];
    protected $input_detail_so = [];

    protected $message_error = [];

    public function __construct($formulirstokopname_id, $is_update_so = false)
    {
        $this->formulirstokopname_id = $formulirstokopname_id;
        $this->is_update_so = $is_update_so;
    }
    
    public function execute()
    {
        $this->payload();
        if ($this->payload_input_new_so) {
            $this->getListObat();
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
        $post = $request->post();
        $this->payload_input_so = ArrayHelper::getValue($post, 'inputan_so');
        $this->payload_input_new_so = ArrayHelper::getValue($post, 'list_obat_baru');
        $this->ruangan_id = ArrayHelper::getValue($post, 'ruangan_id');
    }

    /**
     * get list Master Obat 
     * $this->list_obat_id = list obatalkes_id
     * $this->list_obat = list master Obat
     */
    private function getListObat()
    {
        $this->list_obat_id = ArrayHelper::getColumn($this->payload_input_new_so, 'obatalkes_id', []);
        $masterObat = ObatAlkes::find()->select(['obatalkes_id', 'satuankecil_id', 'harganetto'])->where(['in', 'obatalkes_id', $this->list_obat_id])->asArray()->all();
        $this->list_obat = ArrayHelper::index($masterObat, 'obatalkes_id');
    }

    /**
     * insert formstokopname_t
     */
    private function insertFormSoDetail()
    {
        $input_detail_form_so = [];
        $input = $this->payload_input_new_so;

        if (is_array($input) && !empty($input)) {
            $formBObat = FormStokOpname::find()->where(['formulirstokopname_id' => $this->formulirstokopname_id])->asArray()->one();
            foreach($input as $key => $value) {
                $obat = $this->list_obat[ArrayHelper::getValue($value, 'obatalkes_id')];
                $input_detail_form_so[] = [
                    'obatalkes_id' => ArrayHelper::getValue($value, 'obatalkes_id'),
                    'formulirstokopname_id' => ArrayHelper::getValue($formBObat, 'formulirstokopname_id'),
                    'ruangan_id' => ArrayHelper::getValue($formBObat, 'ruangan_id'),
                    'volume_stok' => DocoHelpers::convertToNumber(DocoHelpers::convertCommaToPoint(DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok_fisik', 0)))),
                    'revisi_stok' => DocoHelpers::convertToNumber(DocoHelpers::convertCommaToPoint(DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'revisi_stok ', 0)))),
                    'is_newso' => true,
                ];
            }
            FormStokOpname::batchInsert($input_detail_form_so, true);
        }
        $this->input_detail_form_so = $input_detail_form_so;
    }

    /**
     * proses insert ke stokopnamedetail_t hanya ketika proses update / sudah pernah insert ke detail
     * is_update_so default false
     */
    private function insertSoDetail()
    {
        $input_detail_so = [];
        $input = ArrayHelper::index($this->payload_input_new_so, 'obatalkes_id', []);

        if (is_array($input) && !empty($input) && $this->is_update_so) {
            $soObat = StokOpname::find()->select([
                'stokopname_id', 'formulirstokopname_id'
            ])->where(['formulirstokopname_id' => $this->formulirstokopname_id])->one();
            $stokopname_id = ArrayHelper::getValue($soObat, 'stokopname_id');

           $detail = DetailFormulirStokOpnameView::find()
                    ->where(['formulirstokopname_id' => $this->formulirstokopname_id])
                    ->andWhere(['in', 'obatalkes_id', $this->list_obat_id])->asArray()->all();
                    
            $tmpDetail = [];
            foreach ($detail as $value) {
                $idDetail = ArrayHelper::getValue($value, 'formulirstokopname_id');
                $obatId = ArrayHelper::getValue($value, 'obatalkes_id');
                $dataDetail = isset($input[$obatId]) ? $input[$obatId] : [];
                $stokFisik = ArrayHelper::getValue($dataDetail, 'stok_fisik',0) > 0 ? ArrayHelper::getValue($dataDetail, 'stok_fisik',0) : ArrayHelper::getValue($dataDetail, 'stok_fisik',0);
                $selisihStok = $stokFisik - ArrayHelper::getValue($value, 'stok_sistem',0);
                $stokRevisi = !empty($dataDetail['revisi_stok']) ? $dataDetail['revisi_stok'] : null;
                $tmpDetail[$obatId] = $value;
                $input_detail_so[] = [
                    'formstokopname_id' => ArrayHelper::getValue($value, 'formulirstokopname_id'),
                    'satuankecil_id' => ArrayHelper::getValue($value, 'satuankecil_id'),
                    'stokopname_id' => $stokopname_id,
                    'obatalkes_id' => ArrayHelper::getValue($value, 'obatalkes_id'),
                    'volume_fisik' => $stokFisik,
                    'revisi_stok' => $stokRevisi,
                    'volume_sistem' => ArrayHelper::getValue($value, 'stok_saatini', 0),
                    'hargasatuan' => ArrayHelper::getValue($value, 'harganetto', 0),
                    'jumlahharga' => $stokFisik * ArrayHelper::getValue($value, 'harganetto', 0),
                    'harganetto' => ArrayHelper::getValue($value, 'harganetto'),
                    'jumlahnetto' => $stokFisik * ArrayHelper::getValue($value, 'harganetto', 0),
                    'tglperiksafisik' => date('Y-m-d H:i:s'),
                    'jmlselisihstok' => $selisihStok,
                    'kondisibarang' => '9999',
                    'is_newso' => true,
                ];
            }
            StokOpnameDetail::batchInsert($input_detail_so, false);

            $querySoDetail = $this->getDetailSoAfterSave($stokopname_id);
            $index = 0;
            foreach ($querySoDetail as $key => $value) {
                $indexArr[$index] = ArrayHelper::getValue($value, 'formstokopname_id');
                $updateDataArr[$index] = ArrayHelper::getValue($value, 'stokopnamedetail_id');

                $index++;
            }
            $condition = ['formstokopname_id' => $indexArr];
            $dataUpdate = ['stokopnamedetail_id' => $updateDataArr];
            ApotekComponent::updateMultiple('formstokopname_t', $dataUpdate, $condition);
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
                $formSo = isset($newFormSo[ArrayHelper::getValue($value, 'obatalkes_id')]) ? $newFormSo[ArrayHelper::getValue($value, 'obatalkes_id')] : [];
                $value['stokopnamedetail_id'] = ArrayHelper::getValue($formSo, 'stokopnamedetail_id');
                $value['formstokopname_id'] = ArrayHelper::getValue($formSo, 'formstokopname_id');
                $value['harganetto'] = ArrayHelper::getValue($formSo, 'harganetto');
                $value['is_newso'] = true;
                $mapp_new_so[] = $value;
            }
            $this->payload_input_so = array_merge($this->payload_input_so, $mapp_new_so);
        }
    }

    /**
     * validasi so 
     */
    private function validate() {
        $infoFormSo = InfoStokOpnameDetailView::find()->select([
            'obatalkes_id', 'obatalkes_nama', 'ruangan_nama', 'noformulir'
        ])
        ->where(['in', 'obatalkes_id', $this->list_obat_id])
        ->andWhere(['ruangan_id' => $this->ruangan_id])->groupBy(['obatalkes_id', 'obatalkes_nama', 'ruangan_nama', 'noformulir'])->asArray()->one();
            
        $inCondition = "(" . implode(",", $this->list_obat_id) . ")";
        $data = Yii::$app->db->createCommand("select * from stokopnamedetail_t a
                    join stokopname_t b on b.stokopname_id = a.stokopname_id
                    where a.obatalkes_id IN {$inCondition}
                    and b.ruangan_id = {$this->ruangan_id}
                    and b.is_verifikasi = false")->queryAll();

        if(!empty($data)){
            $message = [];
            if (is_array($message) && !empty($infoFormSo)) {
                throw new \Exception("Obat dengan nama " . $infoFormSo['obatalkes_nama'] . " masih dalam proses Stok Opname");
            }
        }

    }

    /**
     * get data detail formulir so after save formulir  So
     */
    private function getDetailFormAfterSaveForm()
    {
        $newFormSo = DetailFormulirStokOpnameView::find()
        ->where(['formulirstokopname_id' => $this->formulirstokopname_id])
        ->andWhere(['in', 'obatalkes_id', $this->list_obat_id])->asArray()->all();

        return ArrayHelper::index($newFormSo, 'obatalkes_id');
    }

    /**
     * get Data Detail So after Save Detail
     */
    private function getDetailSoAfterSave($stokopname_id)
    {
        return StokOpnameDetail::find()->select([
            'stokopnamedetail_id', 'formstokopname_id','kondisibarang', 
            'obatalkes_id', 'tglkadaluarsa', 'harganetto', 'jmlselisihstok', 'volume_fisik', 'volume_sistem'
        ])->where(['stokopname_id' => $stokopname_id])
        ->andWhere(['in', 'obatalkes_id', $this->list_obat_id])->asArray()->all();
    }
}