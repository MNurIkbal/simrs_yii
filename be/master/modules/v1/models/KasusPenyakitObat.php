<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kasuspenyakitobat_mp".
 *
 * @property integer $jeniskasuspenyakit_id
 * @property integer $obatalkes_id
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
 *
 * @property JeniskasuspenyakitM $jeniskasuspenyakit
 * @property ObatalkesM $obatalkes
 */
class KasusPenyakitObat extends \Doco\components\DocoActiveRecord
{
    public $jeniskasuspenyakit_old;
    public $obatalkes_old;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kasuspenyakitobat_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskasuspenyakit_id', 'obatalkes_id'], 'required'],
            [['jeniskasuspenyakit_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['obatalkes_id'], 'validObatAlkes'],
            [['created_date', 'last_modified_date', 'deleted_date','is_active','is_deleted'], 'safe'],
        ];
    }

    public function validObatAlkes($attribute, $params)
    {
        $request = Yii::$app->request;
        $penyakitId = $request->post('jeniskasuspenyakit_id');
        $obatId = $request->post('obatalkes_id');

        if ($this->jeniskasuspenyakit_id == $this->jeniskasuspenyakit_old 
            && $this->obatalkes_id == $this->obatalkes_old) {
            return true;
        }

        $query = KasusPenyakitObat::find(false)->select([
                'kasuspenyakitobat_mp.jeniskasuspenyakit_id',
                'kasuspenyakitobat_mp.obatalkes_id',
            ])->joinWith([
                'obatalkes' => function ($query) {
                    $query->select([
                        'obatalkes_m.obatalkes_id',
                        'obatalkes_m.obatalkes_kode',
                        'obatalkes_m.obatalkes_namalain',
                        'obatalkes_m.obatalkes_nama']);
                }
            ])->where([
                'kasuspenyakitobat_mp.jeniskasuspenyakit_id' => $penyakitId,
                'kasuspenyakitobat_mp.obatalkes_id' => $obatId
            ])->all();

        if (!empty($query)) {
            $listDuplicate = [];
            foreach ($query as $key => $value) {
                $listDuplicate[] = !empty($value->obatalkes->obatalkes_nama) ? $value->obatalkes->obatalkes_nama : null;
            }
            $implode = implode(", ", $listDuplicate);
            $this->addError('obatalkes_id', '( ' . $implode . ') Sudah Termapping' );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'obatalkes_id' => 'Obatalkes ID',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJeniskasuspenyakit()
    {
        return $this->hasOne(JenisKasusPenyakit::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatalkes()
    {
        return $this->hasOne(ObatAlkes::className(), ['obatalkes_id' => 'obatalkes_id']);
    }

    public static function primaryKey()
    {
        return ['jeniskasuspenyakit_id', 'obatalkes_id'];
    }
}
