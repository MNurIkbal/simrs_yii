<?php

namespace app\modules\apotek\models;

use Yii;

class MutasiObatRuanganForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */

     public $mutasiobatruangan_id;
     public $pesanobatalkes_id;
     public $terimamutasi_id;
     public $tglmutasioa;
     public $nomutasioa;
     public $ruanganasal_id;
     public $ruangantujuan_id;
     public $keteranganmutasi;
     public $totalharganettomutasi;
     public $totalhargajual;
     public $additional_data;
     public $created_date;
     public $created_by;
     public $modified_count;
     public $last_modified_date;
     public $last_modified_by;
     public $is_deleted;
     public $is_active;
     public $deleted_date;
     public $deleted_by;
     public $status_mutasi;
     public $pegawaimengetahui_id;
     public $pegawai_nama;
     public $nopemesanan;
     public $pegawaimenyetujui_id;
     public $tglpesan;

    public static function tableName()
    {
        return 'mutasiobatruangan_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['mutasiobatruangan_id'], 'required'],
            [['pesanobatalkes_id', 'terimamutasi_id', 'ruanganasal_id', 'ruangantujuan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'status_mutasi', 'pegawaimengetahui_id'], 'default', 'value' => null],
            [['pesanobatalkes_id', 'terimamutasi_id', 'ruanganasal_id', 'ruangantujuan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'status_mutasi', 'pegawaimengetahui_id'], 'integer'],
            [['tglmutasioa', 'nopemesanan','pegawaimengetahui_id', 'created_date', 'last_modified_date', 'deleted_date', 'mutasiobatruangan_id'], 'safe'],
            [['nomutasioa', 'keteranganmutasi', 'additional_data'], 'string'],
            [['totalharganettomutasi', 'totalhargajual'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [[], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasiobatruangan_id' => Yii::t('fe','Mutasiobatruangan ID'),
            'pesanobatalkes_id' => Yii::t('fe','Pesanobatalkes ID'),
            'terimamutasi_id' => Yii::t('fe','Terimamutasi ID'),
            'tglmutasioa' => Yii::t('fe','Tanggal dikirim'),
            'nomutasioa' => Yii::t('fe','Nomutasioa'),
            'ruanganasal_id' => Yii::t('fe','Ruanganasal ID'),
            'ruangantujuan_id' => Yii::t('fe','Ruangantujuan ID'),
            'keteranganmutasi' => Yii::t('fe','Keteranganmutasi'),
            'totalharganettomutasi' => Yii::t('fe','Totalharganettomutasi'),
            'totalhargajual' => Yii::t('fe','Totalhargajual'),
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
            'status_mutasi' => Yii::t('fe','Status Mutasi'),
            'pegawaimengetahui_id' => Yii::t('fe','Pegawai Mengetahui'),
            'tglpesan' =>  Yii::t('fe','Tanggal Pesan')
        ];
    }
}
