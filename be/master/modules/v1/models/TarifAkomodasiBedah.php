<?php

namespace app\modules\v1\models;
use Yii;


/**
 * This is the model class for table "infotarifbedah_v".
 *
 * @property string $kegiatanoperasi_nama
 * @property int $kegiatanoperasi_id
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $tarifbedah_id
 * @property int $kegiatanoperasi_id
 * @property int $perdatarif_id
 * @property string $perdanama_sk
 * @property int $persen_cyto
 * @property double $tarif
 * @property bool $is_active
 * @property datetime $created_date
 */

class TarifAkomodasiBedah extends  \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'tarifbedah_m';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kegiatanoperasi_id', 'kelaspelayanan_id', 'perdatarif_id'], 'required', 'message'=>'Tidak boleh kosong'],
            [['kegiatanoperasi_id'], 'validateUnique', 'message' => 'Kegiatan operasi untuk kelas ini sudah ada.'],
            [['tarif'], 'number'],
            [['is_active'], 'boolean'],
            [['persen_cyto'], 'safe'],
        ];
    }
    
    public function validateUnique(){
        $kegiatanoperasi_id = $this->kegiatanoperasi_id;
        if ($this->isNewRecord){
            $model = self::find()->where([
                'kegiatanoperasi_id' => $this->kegiatanoperasi_id,
                'kelaspelayanan_id'=>$this->kelaspelayanan_id
                ])->count();
            if($model!=0){
                $this->addError("message","Kegiatan operasi untuk kelas pelayanan tersebut sudah ada.");
                return false;
            }
         
        }else{
            $model = self::find()->where(['kegiatanoperasi_id' => $this->kegiatanoperasi_id,'kelaspelayanan_id'=>$this->kelaspelayanan_id])
                                    ->andWhere(['<>', 'tarifbedah_id', $this->tarifbedah_id])
                                    ->count();
            if($model != 0){
                $this->addError("message","Kegiatan operasi untuk kelas pelayanan tersebut sudah ada.");
                return false;
            }
        }
    
        return true;
    }
    public function attributeLabels()
    {
        return [
            'kegiatanoperasi_id' =>'Kegiatan Operasi',
            'kelaspelayanan_id' =>'Kelas Pelayanan',
            'perdatarif_id' => 'Perda / SK',
            'tarif' =>'Tarif',
            'is_active' =>'Status',
            'persen_cyto' =>'Persen Cyto',
        ];
    }

}
