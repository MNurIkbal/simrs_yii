<?php

/**
 * @Author: afil
 * @Date:   2018-01-16 17:49:06
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-22 17:21:22
 * @Description: 
 */

namespace app\modules\rajal\models;

use Yii;

class HasilPemeriksaanLabForm extends \yii\base\Model
{
    public $hasilpemeriksaanlab_id;
    public $pasien_id;
    public $pasienmasukpenunjang_id;
    public $pasienadmisi_id;
    public $pendaftaran_id;
    public $nohasilperiksalab;
    public $tgl_hasilpemeriksaanlab;
    public $tgl_pengambilanhasil;
    public $hasil_kelompokumur;
    public $hasil_jeniskelamin;
    public $status_periksahasil;
    public $catatan_labklinik;
    public $printhasillab;
    public $kesimpulan;
    public $dokterpj_luarrs;
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

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hasilpemeriksaanlab_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'pasienmasukpenunjang_id', 'pendaftaran_id', 'nohasilperiksalab', 'tgl_hasilpemeriksaanlab', 'hasil_kelompokumur', 'hasil_jeniskelamin', 'status_periksahasil'], 'required'],
            [['pasien_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'pendaftaran_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasien_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'pendaftaran_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_hasilpemeriksaanlab', 'tgl_pengambilanhasil', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['catatan_labklinik', 'kesimpulan', 'additional_data'], 'string'],
            [['printhasillab', 'is_deleted', 'is_active'], 'boolean'],
            [['nohasilperiksalab'], 'string', 'max' => 20],
            [['hasil_kelompokumur', 'hasil_jeniskelamin', 'status_periksahasil'], 'string', 'max' => 50],
            [['dokterpj_luarrs'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hasilpemeriksaanlab_id' => Yii::t('fe', 'hasilpemeriksaanlab_id'),
            'pasien_id' => Yii::t('fe', 'pasien_id'),
            'pasienmasukpenunjang_id' => Yii::t('fe', 'pasienmasukpenunjang_id'),
            'pasienadmisi_id' => Yii::t('fe', 'pasienadmisi_id'),
            'pendaftaran_id' => Yii::t('fe', 'pendaftaran_id'),
            'nohasilperiksalab' => Yii::t('fe', 'nohasilperiksalab'),
            'tgl_hasilpemeriksaanlab' => Yii::t('fe', 'tgl_hasilpemeriksaanlab'),
            'tgl_pengambilanhasil' => Yii::t('fe', 'tgl_pengambilanhasil'),
            'hasil_kelompokumur' => Yii::t('fe', 'hasil_kelompokumur'),
            'hasil_jeniskelamin' => Yii::t('fe', 'hasil_jeniskelamin'),
            'status_periksahasil' => Yii::t('fe', 'status_periksahasil'),
            'catatan_labklinik' => Yii::t('fe', 'catatan_labklinik'),
            'printhasillab' => Yii::t('fe', 'printhasillab'),
            'kesimpulan' => Yii::t('fe', 'kesimpulan'),
            'dokterpj_luarrs' => Yii::t('fe', 'dokterpj_luarrs'),
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
        ];
    }
}