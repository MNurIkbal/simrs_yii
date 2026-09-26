<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "konfigsystem_k".
 *
 * @property int $konfigsystem_id
 * @property bool $is_tgltransaksimundur
 * @property bool $is_smsgateway
 * @property bool $is_bayarlangsung
 * @property bool $is_jurnalotomatis
 * @property bool $is_postingotomatis
 * @property bool $is_bridgingbpjs
 * @property string $bpjs_url
 * @property string $bpjs_consid
 * @property string $bpjs_secretkey
 * @property string $bpjs_ppkpelayanan
 * @property int $bpjs_port
 * @property bool $is_akomodasiotomatis
 * @property bool $is_pembulatankeatas
 * @property int $satuanpembulatan
 * @property int $jatuhtempo_tagihan
 */
class KonfigSystem extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'konfigsystem_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['konfigsystem_id'], 'required'],
            [['konfigsystem_id', 'bpjs_port', 'satuanpembulatan', 'jatuhtempo_tagihan'], 'default', 'value' => null],
            [['konfigsystem_id', 'bpjs_port', 'satuanpembulatan', 'jatuhtempo_tagihan'], 'integer'],
            [['is_tgltransaksimundur', 'is_smsgateway', 'is_bayarlangsung', 'is_jurnalotomatis', 'is_postingotomatis', 'is_bridgingbpjs', 'is_akomodasiotomatis', 'is_pembulatankeatas'], 'boolean'],
            [['bpjs_url'], 'string', 'max' => 255],
            [['bpjs_consid', 'bpjs_secretkey', 'bpjs_ppkpelayanan'], 'string', 'max' => 100],
            [['konfigsystem_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konfigsystem_id' => 'Konfigsystem ID',
            'is_tgltransaksimundur' => 'Is Tgltransaksimundur',
            'is_smsgateway' => 'Is Smsgateway',
            'is_bayarlangsung' => 'Is Bayarlangsung',
            'is_jurnalotomatis' => 'Is Jurnalotomatis',
            'is_postingotomatis' => 'Is Postingotomatis',
            'is_bridgingbpjs' => 'Is Bridgingbpjs',
            'bpjs_url' => 'Bpjs Url',
            'bpjs_consid' => 'Bpjs Consid',
            'bpjs_secretkey' => 'Bpjs Secretkey',
            'bpjs_ppkpelayanan' => 'Bpjs Ppkpelayanan',
            'bpjs_port' => 'Bpjs Port',
            'is_akomodasiotomatis' => 'Is Akomodasiotomatis',
            'is_pembulatankeatas' => 'Is Pembulatankeatas',
            'satuanpembulatan' => 'Satuanpembulatan',
            'jatuhtempo_tagihan' => 'Jatuhtempo Tagihan',
        ];
    }
}
