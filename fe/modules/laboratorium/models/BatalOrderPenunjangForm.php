<?php

namespace app\modules\laboratorium\models;

use Yii;

/**
 * This is the model class for table "batalorderpenunjang_t".
 *
 * @property int $batalorderpenunjang_id
 * @property int $pasienkirimkeunitlain_id
 * @property string $no_batalorder
 * @property string $tgl_batalorder
 * @property int $peg_menyetujui_id
 * @property string $alasan
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
 */
class BatalOrderPenunjangForm extends \yii\base\Model
{
        public $batalorderpenunjang_id;
        public $pasienkirimkeunitlain_id;
        public $no_batalorder;
        public $tgl_batalorder;
        public $peg_menyetujui_id;
        public $alasan;
        public $additional_data;
        public $created_date;
        public $created_by;
        public $modified_count;
        public $last_modified_date;
        public $last_modified_by;
        public $is_deleted;
        public $is_active;
        public $deleted_date;
        public $deleted_by;
    /**
     * {@inheritdoc}
     */
    public static function tableName () {
        return 'batalorderpenunjang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_batalorder'],'required','message'=>'Tanggal Batal tidak boleh kosong'],
            [['peg_menyetujui_id'],'required','message'=>'Disestujui Oleh tidak boleh kosong'],
            [['alasan'],'required','message'=>'Alasan Pembatalan Oleh tidak boleh kosong'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'batalorderpenunjang_id' => 'Batalorderpenunjang ID',
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'no_batalorder' => 'No Batalorder',
            'tgl_batalorder' => 'Tgl Batalorder',
            'peg_menyetujui_id' => 'Peg Menyetujui ID',
            'alasan' => 'Alasan',
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
}
