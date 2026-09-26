<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Lookup;

/**
 * This is the model class for table "carabayar_m".
 *
 * @property integer $carabayar_id
 * @property string $carabayar_nama
 * @property string $carabayar_namalainnya
 * @property string $metode_pembayaran
 * @property string $carabayar_loket
 * @property string $carabayar_singkatan
 * @property boolean $is_subsidiasuransi
 * @property boolean $is_subsidipemerintah
 * @property boolean $is_subsidirs
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $groupcarabayar_id
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 * @property integer $penjamindefault_id
 */
class CaraBayar extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'carabayar_nama',
        'carabayar_namalainnya'
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'carabayar_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['carabayar_nama', 'metode_pembayaran'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'penjamindefault_id'], 'integer'],
            [['is_subsidiasuransi', 'is_subsidipemerintah', 'is_subsidirs', 'is_deleted', 'is_active','is_online'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['carabayar_nama', 'carabayar_namalainnya', 'carabayar_loket'], 'string', 'max' => 50],
            [['kode_antrian','carabayar_singkatan'], 'string', 'max' => 10],
            [['carabayar_nama'], 'checkUnique'],
        ];
    }

     public function checkUnique($attribute, $params)
    {
        $carabayar_nama = $this->carabayar_nama;
        $metode_pembayaran = $this->metode_pembayaran;
        $query = CaraBayar::find()->where([
           'LOWER (carabayar_nama)' => strtolower($carabayar_nama),
           'metode_pembayaran' => $metode_pembayaran,
        ]);
        $query->andWhere(['is_deleted' => false]);
        $result = $query->one();
       
        $getLookupMetodePembayaran = Lookup::find()->Where(['lookup_id'=> $metode_pembayaran])->one();

        if (!empty($result)) {
            if ($this->carabayar_id != $result->carabayar_id) {
                $this->addError('carabayar_nama','"'.'Nama : '.$carabayar_nama.' , '.'<br> Metode Pembayaran : '.$getLookupMetodePembayaran->lookup_name.', telah dipergunakan.');
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'carabayar_id' => Yii::t('app', 'Cara bayar'),
            'carabayar_nama' => Yii::t('app', 'Nama Cara Bayar'),
            'carabayar_namalainnya' => Yii::t('app', 'Nama Lainnya'),
            'metode_pembayaran' => Yii::t('app', 'Metode Pembayaran'),
            'carabayar_loket' => Yii::t('app', 'Cara Bayar Loket'),
            'carabayar_singkatan' => Yii::t('app', 'Singkatan'),
            'is_subsidiasuransi' => Yii::t('app', 'Subsidi Asuransi'),
            'is_subsidipemerintah' => Yii::t('app', 'Subsidi Pemerintah'),
            'is_subsidirs' => Yii::t('app', 'Subsidi Rumah Sakit'),
            'groupcarabayar_id' => Yii::t('app', 'Grup Cara Bayar'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'is_online' => Yii::t('app', 'Is Online'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
            'penjamindefault_id' => Yii::t('app', 'Penjamin Default'),
        ];
    }

    public function getLookupMetodePembayaran()
    {
        return $this->belongsTo(Lookup::className(), ['lookup_id' => 'metode_pembayaran']);
    }

    public function getMetodePembayaran()
    {
        return $this->hasOne(Lookup::className(), ['lookup_id' => 'metode_pembayaran']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjamin()
    {
        return $this->hasOne(Penjamin::className(), ['penjamin_id' => 'penjamindefault_id']);
    }
    
    public function getNamaAndSingkatan() 
    {
        return $this->carabayar_singkatan . ' - ' . $this->carabayar_nama;
    }

    public function extraFields()
    {
        return ['penjamin_m' => function($item){
            return $item->penjamin;
        }];
    }
}
