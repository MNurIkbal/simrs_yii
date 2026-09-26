<?php

/**
 * @Author: afil
 * @Date:   2018-01-17 11:50:38
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-16 10:11:37
 * @Description: 
 */

namespace app\modules\rajal\models;

use Yii;

class BuatJanjiPoliForm extends \yii\base\Model
{
    public $buatjanjipoli_id;
    public $pendaftaran_id;
    public $pegawai_id;
    public $ruangan_id;
    public $pasien_id;
    public $tgl_buatjanji;
    public $hari_jadwal;
    public $tgl_jadwal;
    public $by_phone;
    public $keterangan_buatjanji;
    public $antrian_id;
    public $no_buatjanji;
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
    public $status_janjipoli;
    public $carabayar_id;
    public $penjamin_id;

    //additional
    public $no_rekam_medik;
    public $no_pendaftaran;
    public $nama_pasien;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'buatjanjipoli_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['ruangan_id', 'pasien_id', 'tgl_buatjanji', 'hari_jadwal', 'tgl_jadwal', 'antrian_id'], 'required'],
            [['tgl_buatjanji', 'tgl_jadwal', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['by_phone', 'is_deleted', 'is_active'], 'boolean'],
            [['keterangan_buatjanji', 'additional_data'], 'string'],
            [['hari_jadwal'], 'string', 'max' => 20],
            [['status_janjipoli'], 'string', 'max' => 32],
            [['no_buatjanji'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'buatjanjipoli_id' => Yii::t('fe', 'buatjanjipoli_id'),
            'pendaftaran_id' => Yii::t('fe', 'pendaftaran_id'),
            'pegawai_id' => Yii::t('fe', 'pegawai_id'),
            'ruangan_id' => Yii::t('fe', 'ruangan_id'),
            'pasien_id' => Yii::t('fe', 'pasien_id'),
            'tgl_buatjanji' => Yii::t('fe', 'tgl_buatjanji'),
            'hari_jadwal' => Yii::t('fe', 'hari_jadwal'),
            'tgl_jadwal' => Yii::t('fe', 'Tanggal Kontrol'),
            'by_phone' => Yii::t('fe', 'by_phone'),
            'keterangan_buatjanji' => Yii::t('fe', 'keterangan_buatjanji'),
            'antrian_id' => Yii::t('fe', 'antrian_id'),
            'no_buatjanji' => Yii::t('fe', 'no_buatjanji'),
            'additional_data' => Yii::t('fe', 'additional_data'),
            'created_date' => Yii::t('fe', 'created_date'),
            'created_by' => Yii::t('fe', 'created_by'),
            'modified_count' => Yii::t('fe', 'modified_count'),
            'last_modified_date' => Yii::t('fe', 'last_modified_date'),
            'last_modified_by' => Yii::t('fe', 'last_modified_by'),
            'is_deleted' => Yii::t('fe', 'is_deleted'),
            'is_active' => Yii::t('fe', 'is_active'),
            'deleted_date' => Yii::t('fe', 'deleted_date'),
            'deleted_by' => Yii::t('fe', 'deleted_by'),
            'status_janjipoli' => Yii::t('fe', 'Status Janji Poli'),
            'carabayar_id' => Yii::t('fe', 'Cara Bayar ID'),
            'penjamin_id' => Yii::t('fe', 'Penjamin ID'),
        ];
    }
}