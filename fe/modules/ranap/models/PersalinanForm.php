<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-06 14:07:13
 */

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class PersalinanForm extends Model
{
    /**
     * {@inheritdoc}
     */
    public $persalinan_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $tgl_persalinan;
    public $penolong;
    public $tempat_persalinan;
    public $jenis_persalinan;
    public $rujuk_kala;
    public $alasan_merujuk;
    public $tempat_rujukan;
    public $pendamping;
    public $masalah_persalinan;
    public $kala_1;
    public $kala_2;
    public $kala_3;
    public $kala_4;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['alasan_merujuk', 'required', 'when' => function ($model) {
                return $model->rujuk_kala != '';
            }, 'whenClient' => "function (attribute, value) {
                return $('#persalinanform-rujuk_kala').val() != '';
            }"],
            ['tempat_rujukan', 'required', 'when' => function ($model) {
                return $model->rujuk_kala != '';
            }, 'whenClient' => "function (attribute, value) {
                return $('#persalinanform-rujuk_kala').val() != '';
            }"],
            ['pendamping', 'required', 'when' => function ($model) {
                return $model->rujuk_kala != '';
            }, 'whenClient' => "function (attribute, value) {
                return $('#persalinanform-rujuk_kala').val() != '';
            }"],
            ['masalah_persalinan', 'required', 'when' => function ($model) {
                return $model->rujuk_kala != '';
            }, 'whenClient' => "function (attribute, value) {
                return $('#persalinanform-rujuk_kala').val() != '';
            }"],
            [['pendaftaran_id', 'pasienadmisi_id', 'rujuk_kala', 'pendamping', 'masalah_persalinan'], 'default', 'value' => null],
            [['persalinan_id', 'pendaftaran_id', 'pasienadmisi_id', 'rujuk_kala', 'pendamping', 'masalah_persalinan'], 'integer'],
            [['tgl_persalinan', 'jenis_persalinan'], 'safe'],
            [['kala_1', 'kala_2', 'kala_3', 'kala_4'], 'string'],
            [['penolong', 'tempat_persalinan', 'alasan_merujuk', 'tempat_rujukan'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_persalinan' => Yii::t('fe', 'Tanggal Persalinan'),
            'penolong' => Yii::t('fe', 'Penolong'),
            'tempat_persalinan' => Yii::t('fe', 'Tempat Persalinan'),
            'rujuk_kala' => Yii::t('fe', 'Catatan'),
            'alasan_merujuk' => Yii::t('fe', 'Alasan Merujuk'),
            'tempat_rujukan' => Yii::t('fe', 'Tempat Rujukan'),
            'pendamping' => Yii::t('fe', 'Pendamping'),
            'masalah_persalinan' => Yii::t('fe', 'Masalah Persalinan'),
            'kala_1' => Yii::t('fe', 'Kala 1'),
            'kala_2' => Yii::t('fe', 'Kala 2'),
            'kala_3' => Yii::t('fe', 'Kala 3'),
            'kala_4' => Yii::t('fe', 'Kala 4'),
        ];
    }
}
