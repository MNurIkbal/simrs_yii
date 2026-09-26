<?php
/**
 * @Author: Iqbal@docotel.om
 * @Date:   2018-09-03 16:36:56
 * @Last Modified by:   
 * @Last Modified time: 
 * @Description: 
 */

namespace app\modules\master\models;

use app\components\DocoHelpers;
use Yii;

/**
 *
 * @property int $jenisdiet_id
 * @property string $jenisdiet_nama
 * @property string $makanandiet_id
 * @property string $makanandiet_nama
 * @property string $makanandiet_keterangan
 */
class MenuDietForm extends \yii\base\Model
{
    public $jenisdiet_id;
    public $makanandiet_id;

    public function rules()
    {
        return [
            [['jenisdiet_id', 'makanandiet_id'], 'required', 'message'=>'{attribute} Tidak boleh kosong'],
            [['jenisdiet_id', 'makanandiet_id'], 'default', 'value' => null],
            [['jenisdiet_id', 'makanandiet_id'], 'safe'],
            [['jenisdiet_id'], 'integer'],
            [['makanandiet_id'],'checkValidate','on'=>'create'],
            [['makanandiet_id'],'checkValidate', 'on'=> 'edit'],
        ];
    }

    public function checkValidate($attribute, $params){
        if (empty($this->makanandiet_id)) {
            DocoHelpers::multipleParseError($this,'Tidak boleh kosong.', 'makanandiet_id[]',0);
        }

    }

    public function attributeLabels()
    {
        return [
            'jenisdiet_id' => Yii::t('fe', 'Nama jenis diet'),
            'jenisdiet_nama' => Yii::t('fe', 'Nama jenis diet'), 
            'makanandiet_id' => Yii::t('fe', 'Nama makanan'),
            'makanandiet_nama' => Yii::t('fe', 'Nama makanan'),
            'makanandiet_keterangan' => Yii::t('fe', 'Keterangan')
        ];
    }
}
