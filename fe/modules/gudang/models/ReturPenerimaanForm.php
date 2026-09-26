<?php

namespace app\modules\gudang\models;

use Yii;

class ReturPenerimaanForm extends \yii\base\Model
{

    public $tanggal_retur;
    public $pegawai_retur;
    public $alasan_retur;

    public $data_retur;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tanggal_retur','pegawai_retur','alasan_retur','data_retur'],'safe'],
            [['tanggal_retur','pegawai_retur','alasan_retur','data_retur'],'required'],
            [['data_retur'],'checkData']
        ];
    }

    public function checkData($attributes, $params)
    {
        $data = json_decode($this->data_retur, true);
        if (is_array($data) && count($data)) {
            foreach ($data as $id_penerimaan => $value) {
                if (isset($value['qty_retur']) && $value['qty_retur'] < 1) {
                    $this->addError('data_retur', 'Qty retur minimal harus 1');
                }
            }
        } else {
            $this->addError('data_retur', 'Data Retur tidak boleh kosong');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tanggal_retur' => 'Tanggal Retur',
            'pegawai_retur' => 'Pegawai Retur',
            'alasan_retur' => 'Alasan Retur',
        ];
    }
}
