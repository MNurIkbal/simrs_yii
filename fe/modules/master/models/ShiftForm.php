<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Shift Form
 * @copyright 21 Mei 2018 aweutist
 */

namespace Doco\master\models;

use Yii;
use yii\validators\UniqueValidator;
use yii\db\Query;
class ShiftForm extends \yii\db\ActiveRecord
{
    public $shift_nama;
    public $shift_namalainnya;
    public $shift_jamawal;
    public $shift_jamakhir;
    public $shift_kode;
    public $is_active;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kamarruangan_m';
    }

    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            // [['kamarruangan_nokamar'], 'checkUnique'],
            [['shift_kode'], 'trimWhitespace'],
            [['shift_kode'], 'string', 'max' => 25],
            [['shift_jamawal', 'shift_jamakhir', 'shift_kode', 'shift_nama'], 'safe'],
            [['shift_jamawal', 'shift_jamakhir', 'shift_kode', 'shift_nama'], 'required'],
        ];
    }

    public function trimWhitespace(){
        $shift_kode = $this->shift_kode;
        if (strpos(substr($shift_kode, 0, 1), ' ') !== FALSE) {
            $this->addError('shift_kode', 'Kode Shift mengandung spasi di awal kata');
            return false;
        }
        return true;
    }


    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'shift_nama' => \Yii::t('fe', 'Nama shift'),
            'shift_namalainnya' => \Yii::t('fe', 'Nama shift lainnya'),
            'shift_jamawal' => \Yii::t('fe', 'Jam awal'),
            'shift_jamakhir' => \Yii::t('fe', 'Jam akhir'),
            'shift_kode' => \Yii::t('fe', 'Kode shift'),
            'is_active' => \Yii::t('fe', 'Status'),
        ];
    }

}
