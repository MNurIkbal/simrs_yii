<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoinstruksi_v".
 *
 * @property string $tipe_instruksi
 * @property int $instruksi_id
 * @property int $cppt_id
 * @property string $catatan_instruksi
 * @property int $instruksitindakan_id
 * @property string $tgl_instruksi
 * @property int $daftartindakan_id
 * @property string $instruksi
 * @property string $paket
 * @property string $tindakan
 * @property double $qty
 * @property bool $is_cyto
 * @property int $qty_sisa
 * @property int $dokter_id
 * @property string $dokter
 * @property string $status_implementasi
 * @property string $status
 */
class InfoInstruksiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoinstruksi_v';
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ["tgl_instruksi"];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipe_instruksi', 'catatan_instruksi', 'instruksi', 'paket', 'tindakan', 'status_implementasi'], 'string'],
            [['instruksi_id', 'cppt_id', 'instruksitindakan_id', 'daftartindakan_id', 'qty_sisa', 'dokter_id'], 'default', 'value' => null],
            [['instruksi_id', 'cppt_id', 'instruksitindakan_id', 'daftartindakan_id', 'qty_sisa', 'dokter_id'], 'integer'],
            [['tgl_instruksi'], 'safe'],
            [['qty'], 'number'],
            [['is_cyto'], 'boolean'],
            [['dokter'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tipe_instruksi' => 'Tipe Instruksi',
            'instruksi_id' => 'Instruksi ID',
            'cppt_id' => 'Cppt ID',
            'catatan_instruksi' => 'Catatan Instruksi',
            'instruksitindakan_id' => 'Instruksitindakan ID',
            'tgl_instruksi' => 'Tgl Instruksi',
            'daftartindakan_id' => 'Daftartindakan ID',
            'instruksi' => 'Instruksi',
            'paket' => 'Paket',
            'tindakan' => 'Tindakan',
            'qty' => 'Qty',
            'is_cyto' => 'Is Cyto',
            'qty_sisa' => 'Qty Sisa',
            'dokter_id' => 'Dokter ID',
            'dokter' => 'Dokter',
            'status_implementasi' => 'Status Implementasi',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpptview()
    {
        return $this->hasOne(CpptView::className(), ['cppt_id' => 'cppt_id']);
    }

    public function getInstructionData($columns)
    {
        return $this->find()->select($columns);
    }
}
