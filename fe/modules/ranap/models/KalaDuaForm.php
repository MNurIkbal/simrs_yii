<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-07 17:29:27
 */

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class KalaDuaForm extends Model
{
    /**
     * {@inheritdoc}
     */
    public $persalinan_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $k2_pendamping;
    public $k2_episitomi;
    public $k2_gawatjanin;
    public $k2_distosiabahu;
    public $k2_tindakanjanin;
    public $k2_tindakandistosia;
    public $k2_masalah;
    public $k2_indikasi;
    public $k2_hasil;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['k2_pendamping', 'k2_episitomi', 'k2_gawatjanin', 'k2_distosiabahu'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'k2_pendamping'], 'default', 'value' => null],
            [['persalinan_id', 'pendaftaran_id', 'pasienadmisi_id', 'k2_pendamping'], 'integer'],
            [['k2_episitomi', 'k2_gawatjanin', 'k2_distosiabahu'], 'boolean'],
            [['k2_tindakanjanin', 'k2_tindakandistosia', 'k2_masalah'], 'string'],
            [['k2_indikasi', 'k2_hasil'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'k2_pendamping' => Yii::t('fe', 'Pendamping Persalinan'),
            'k2_episitomi' => Yii::t('fe', 'Episitomi'),
            'k2_gawatjanin' => Yii::t('fe', 'Gawat Janin'),
            'k2_distosiabahu' => Yii::t('fe', 'Distosia Bahu'),
            'k2_tindakanjanin' => Yii::t('fe', 'Tindakan Janin'),
            'k2_tindakandistosia' => Yii::t('fe', 'Tindakan Distosia'),
            'k2_masalah' => Yii::t('fe', 'Masalah Lain'),
            'k2_indikasi' => Yii::t('fe', 'Indikasi Episitomi'),
            'k2_hasil' => Yii::t('fe', 'Pemantauan DJJ selama 5-10 menit'),
        ];
    }
}
