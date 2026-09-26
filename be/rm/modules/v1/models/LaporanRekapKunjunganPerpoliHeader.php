<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanrekapkunjunganperpoli_v".
 *
 */
class LaporanRekapKunjunganPerpoliHeader extends \Doco\components\DocoActiveRecord
{
	/**
     * @inheritdoc
     */
	public $tgl_pendaftaran;
	public $status_bayar;
	public $status_periksa;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanrekapkunjunganperpoliheader_v';
    }
}
