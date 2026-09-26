<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\GroupMargin;

/**
 * This is the model class for table "penjamin_m".
 *
 * @property integer $penjamin_id
 * @property integer $carabayar_id
 * @property string $penjamin_nama
 * @property string $penjamin_namalainnya
 * @property string $alamat_penjamin
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 * @property integer $groupmargin_id
 */
class Penjamin extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'penjamin_m';
    }

    /**
     * @inheritdoc
     */

    //  Validasi XSS di form
    protected $xssProtected = [
        'additional_data',
        'penjamin_namalainnya',
        'penjamin_nama',
        'alamat_penjamin',
        'groupmargin_id',
        'penjamin_kode'
    ];

    public function rules()
    {
        return [
            [['carabayar_id', 'groupmargin_id', 'penjamin_kode'], 'required'],
            [['carabayar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'groupmargin_id'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'penjamin_namalainnya', 'penjamin_nama', 'groupmargin_id', 'konfigasuransi_id'], 'safe'],
            [['is_deleted', 'is_active', 'is_online'], 'boolean'],
            [['penjamin_kode'], 'string', 'max' => 50],
            [['penjamin_nama', 'penjamin_namalainnya'], 'string', 'max' => 70],
            [['alamat_penjamin'], 'string', 'max' => 200],
            [['penjamin_kode'], 'unique'],
            [['carabayar_id'], 'checkUnique'],
            [['penjamin_nama'], 'trimWhitespace'],
            /*[['carabayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => CaraBayar::className(), 'targetAttribute' => ['carabayar_id' => 'carabayar_id']],*/
        ];
    }

    public function checkUnique($attribute, $params)
    {
        $carabayar_id = $this->carabayar_id;
        $penjamin_nama = $this->penjamin_nama;
        $query = Penjamin::find()->where([
            'carabayar_id' => $carabayar_id,
            'LOWER (penjamin_nama)' => strtolower($penjamin_nama),
        ]);
        $query->andWhere(['is_deleted' => false]);
        $result = $query->one();
        $getCaraBayar = CaraBayar::find()->Where(['carabayar_id' => $carabayar_id])->one();

        if (!empty($result)) {
            if ($this->penjamin_id != $result->penjamin_id) {
                $this->addError('carabayar_id', '"' . 'Cara Bayar : ' . $getCaraBayar->carabayar_nama . ' , ' . '<br> Nama penjamin : ' . $penjamin_nama . ', telah dipergunakan.');
                return false;
            }
        }

        return true;
    }

    public function trimWhitespace()
    {
        $penjamin_nama = $this->penjamin_nama;
        // echo "<pre>";var_dump($penjamin_nama);die();
        $return = true;
        if (strpos(substr($penjamin_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('penjamin_nama', 'Nama Penjamin mengandung spasi di awal kata');
            $return = false;
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'penjamin_id' => Yii::t('app', 'Penjamin'),
            'carabayar_id' => Yii::t('app', 'Cara bayar'),
            'penjamin_nama' => Yii::t('app', 'Nama penjamin'),
            'penjamin_kode' => Yii::t('app', 'Kode penjamin'),
            'penjamin_namalainnya' => Yii::t('app', 'Nama lainnya'),
            'alamat_penjamin' => Yii::t('app', 'Alamat penjamin'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
            'groupmargin_id' => Yii::t('app', 'Group Margin')
        ];
    }

    public function getCaraBayar()
    {
        return $this->hasOne(CaraBayar::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarifTindakan()
    {
        return $this->hasMany(TarifTindakan::className(), ['penjamin_id' => 'penjamin_id']);
    }

    public function extraFields()
    {
        return ['carabayar_m' => function ($item) {
            return $item->caraBayar;
        }];
    }
}
