<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "hariperawatan_r".
 *
 * @property int $hariperawatan_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $kamarruangan_id
 * @property int $periode
 * @property int $jan
 * @property int $feb
 * @property int $mar
 * @property int $apr
 * @property int $mei
 * @property int $jun
 * @property int $jul
 * @property int $agus
 * @property int $sept
 * @property int $okt
 * @property int $nov
 * @property int $des
 */
class HariPerawatan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hariperawatan_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'kamarruangan_id'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'kamarruangan_id', 'periode', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agus', 'sept', 'okt', 'nov', 'des'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'kamarruangan_id', 'periode', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agus', 'sept', 'okt', 'nov', 'des'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hariperawatan_id' => 'Hariperawatan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'kamarruangan_id' => 'Kamarruangan ID',
            'periode' => 'Periode',
            'jan' => 'Jan',
            'feb' => 'Feb',
            'mar' => 'Mar',
            'apr' => 'Apr',
            'mei' => 'Mei',
            'jun' => 'Jun',
            'jul' => 'Jul',
            'agus' => 'Agus',
            'sept' => 'Sept',
            'okt' => 'Okt',
            'nov' => 'Nov',
            'des' => 'Des',
        ];
    }
}