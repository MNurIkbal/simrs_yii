<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-04 11:37:39
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-08-03 14:34:11
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "cpptdetail_v".
 *
 * @property string $jns_instruksi
 * @property int $cppt_id
 * @property int $instruksi_id
 * @property string $tgl_instruksi
 * @property string $nama_instruksi
 */
class CpptDetailView extends \yii\db\ActiveRecord
{
    /* Public variables */
    public $paket;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cpptdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jns_instruksi', 'nama_instruksi'], 'string'],
            [['cppt_id', 'instruksi_id'], 'default', 'value' => null],
            [['cppt_id', 'instruksi_id'], 'integer'],
            [['tgl_instruksi'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jns_instruksi' => 'Jns Instruksi',
            'cppt_id' => 'Cppt ID',
            'instruksi_id' => 'Instruksi ID',
            'tgl_instruksi' => 'Tgl Instruksi',
            'nama_instruksi' => 'Nama Instruksi',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCppt()
    {
        return $this->hasOne(CpptView::className(), ['cppt_id' => 'cppt_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaketDetail()
    {
        return $this->hasMany(PaketDetailView::className(), ['tipepaket_id' => 'tipepaket_id']);
    }
}
?>