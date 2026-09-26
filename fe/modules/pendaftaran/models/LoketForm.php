<?php

namespace app\modules\pendaftaran\models;

use Yii;

class LoketForm extends \yii\base\Model
{
    public $loket_id;
    public $carabayar_id;
    public $loket_nama;
    public $loket_namalain;
    public $loket_fungsi;
    public $loket_singkatan;
    public $loket_nourut;
    public $loket_formatnomor;
    public $loket_maxantrian;
    public $filesuara;
    public $fungsiantrian_id;
    public $is_pendaftaran;
    public $is_kasir;
    public $additional_data;
    public $is_active;

    public $konfigantrian_id;
    public $jenisantrian_id;
    public $layarantrian_id;
    public $no_loket;
    public $nama_loket;
    public $status;
    public $ruangan_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jenisantrian_id', 'no_loket','nama_loket'], 'required'],
            [['jenisantrian_id', 'konfigantrian_id', 'layarantrian_id', 'konfigantrian_id','no_loket','nama_loket','status'], 'safe'],
            // [['loket_maxitem', 'loket_itemhigh', 'loket_itemwidth', 'loket_intrefresh'], 'integer'],
            // [['loket_nama', 'loket_namalain'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'loket_id' => Yii::t('fe', 'loket_id'),
            'carabayar_id' => Yii::t('fe', 'carabayar_id'),
            'layarantrian_id' => Yii::t('fe', 'layarantrian_id'),
            'loket_nama' => Yii::t('fe', 'loket_nama'),
            'loket_namalain' => Yii::t('fe', 'loket_namalain'),
            'loket_fungsi' => Yii::t('fe', 'loket_fungsi'),
            'loket_singkatan' => Yii::t('fe', 'loket_singkatan'),
            'loket_nourut' => Yii::t('fe', 'loket_nourut'),
            'loket_formatnomor' => Yii::t('fe', 'loket_formatnomor'),
            'loket_maxantrian' => Yii::t('fe', 'loket_maxantrian'),
            'filesuara' => Yii::t('fe', 'filesuara'),
            'is_pendaftaran' => Yii::t('fe', 'is_pendaftaran'),
            'is_kasir' => Yii::t('fe', 'is_kasir'),
            'fungsiantrian_id' => Yii::t('fe', 'fungsiantrian_id'),

            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
            'jenisantrian_id' => Yii::t('fe','Jenis Antrian'),
            'layarantrian_id' => Yii::t('fe','Layar Antrian'),
            'nama_loket' => Yii::t('fe','Nama Loket'),
            'konfigantrian_id' => Yii::t('fe','Jenis Pengambilan Antrian'),
            'ruangan_id' => Yii::t('fe','Ruangan'),
        ];
    }
}
