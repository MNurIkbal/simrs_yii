<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Obat Alkes Jenis Kasus Penyakit
 * @copyright 3 January 2018 aweutist
 */

namespace Doco\apotek\models;

use Yii;
use yii\validators\UniqueValidator;
use yii\db\Query;

class ObatAlkesDiagnosaForm extends \yii\base\Model
{
    public $obatalkes_id;
    public $diagnosa_id;
    public $selectObat;
    public $diagnosa_namalainnya;
    public $obatalkes_nama;

    /**
     * @inheritdoc
     */
    // public static function tableName()
    // {
    //     return 'kasuspenyakitobat_mp';
    // }

    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            [['diagnosa_id', 'obatalkes_id'], 'required'],
            [['diagnosa_id', 'obatalkes_id'], 'safe'],
        ];
    }


    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'obatalkes_id' => Yii::t('fe','Obat Alkes'),
            'diagnosa_id' => Yii::t('fe','Diagnosa Penyakit'),
        ];
    }

    public function checkUnique() {
        $obat_alkes_id = $this->obatalkes_id;
        $jenis_kasus_id = $this->diagnosa_id;

        $obat = (new Query)
                ->select(['obatalkes_id', 'diagnosa_id', 'is_deleted'])
                ->from('kasuspenyakitobat_mp')
                ->where([
                    'obatalkes_id' => $obat_alkes_id,
                    'diagnosa_id' => $jenis_kasus_id,
                    'is_deleted' => false
                ])
                ->all();
        if ($obat) {
            $this->addError('diagnosa_id', 'Jenis kasus penyakit telah di gunakan');
            $this->addError('obatalkes_id', 'Obat alkes telah di gunakan');
        }
    }
}


