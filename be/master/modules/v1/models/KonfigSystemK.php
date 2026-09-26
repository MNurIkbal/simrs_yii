<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "konfigsystem_k".
 *
 * @property int $konfigsystem_id
 * @property bool $is_tgltransaksimundur back date
 * @property bool $is_smsgateway sms gateway
 * @property bool $is_bayarlangsung bayar langsung
 * @property bool $is_jurnalotomatis jurnal otomatis
 * @property bool $is_postingotomatis posting otomatis
 * @property bool $is_bridgingbpjs bridging
 * @property string $bpjs_url bpjs url
 * @property string $bpjs_consid cons id
 * @property string $bpjs_secretkey secret
 * @property string $bpjs_ppkpelayanan ppk pelayanan
 * @property int $bpjs_port port
 * @property bool $is_akomodasiotomatis akomodasi otomatis
 * @property bool $is_pembulatankeatas pembulatan keatas
 * @property int $satuanpembulatan pecahan terkecil
 * @property int $jatuhtempo_tagihan jatuh tempo
 * @property string $kso_url kso url
 * @property string $kso_consid cons id
 * @property string $kso_secretkey secret
 * @property int $start_antrian start_antrian
 * @property string $tglberlaku_dari tgl_berlaku dari
 * @property string $tglberlaku_sampai tgl_berlaku sampai
 * @property int $rincian_tagihan 0=detail, 1=per instalasi
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property string $mail_protocol
 * @property string $mail_parameter
 * @property string $smtp_hostname
 * @property string $smtp_username
 * @property string $smtp_password
 * @property int $smtp_port
 * @property int $smtp_timeout
 * @property int $expired_time_program_fisio
 * @property bool $is_expired_time_program_fisio
 */
class KonfigSystemK extends \Doco\components\DocoActiveRecord
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
            [['konfigsystem_id', 'bpjs_port', 'satuanpembulatan', 'jatuhtempo_tagihan', 'start_antrian', 'rincian_tagihan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'smtp_port', 'smtp_timeout', 'expired_time_program_fisio'], 'default', 'value' => null],
            [['konfigsystem_id', 'bpjs_port', 'satuanpembulatan', 'jatuhtempo_tagihan', 'start_antrian', 'rincian_tagihan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'smtp_port', 'smtp_timeout','kelola_tagihan', 'expired_time_program_fisio'], 'integer'],
            [['is_tgltransaksimundur', 'is_smsgateway', 'is_bayarlangsung', 'is_jurnalotomatis', 'is_postingotomatis', 'is_bridgingbpjs', 'is_akomodasiotomatis', 'is_pembulatankeatas', 'is_deleted','edit_billing','is_active', 'is_expired_time_program_fisio','is_set_plafon','is_print_automatic','is_set_tindakan'], 'boolean'],
            [['tglberlaku_dari', 'tglberlaku_sampai', 'created_date', 'last_modified_date', 'deleted_date','url_print','kelola_tagihan', 'adm_persen', 'adm_tindakan_id'], 'safe'],
            [['additional_data','konfig_kelompok_tindakan'], 'string'],
            [['bpjs_url', 'kso_url', 'mail_protocol', 'mail_parameter'], 'string', 'max' => 255],
            [['bpjs_consid', 'bpjs_secretkey', 'bpjs_ppkpelayanan', 'kso_consid', 'kso_secretkey', 'smtp_hostname', 'smtp_username', 'smtp_password'], 'string', 'max' => 100],
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
            'kso_url' => 'Kso Url',
            'kso_consid' => 'Kso Consid',
            'kso_secretkey' => 'Kso Secretkey',
            'start_antrian' => 'Start Antrian',
            'tglberlaku_dari' => 'Tglberlaku Dari',
            'tglberlaku_sampai' => 'Tglberlaku Sampai',
            'rincian_tagihan' => 'Rincian Tagihan',
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
            'mail_protocol' => 'Mail Protocol',
            'mail_parameter' => 'Mail Parameter',
            'smtp_hostname' => 'Smpt Hostname',
            'smtp_username' => 'Smtp Username',
            'smtp_password' => 'Smtp Password',
            'smtp_port' => 'Smtp Port',
            'smtp_timeout' => 'Smtp Timeout',
            'url_print' => 'Url Print'
        ];
    }
}
