<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "cppt_v".
 *
 * @property int $cppt_id
 * @property int $pegawai_id
 */

class NotifikasiCpptView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notifikasicppt_v';
    }


    public static function unfinishedSoap($id_pegawai)
    {
        
        return self::find()->Where(['pegawai_id' => $id_pegawai])
            ->andWhere([
                'is_belum' => true,
            ]);;
    }

}
