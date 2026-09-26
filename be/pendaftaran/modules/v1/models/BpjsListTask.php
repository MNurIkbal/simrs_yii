<?php

namespace app\modules\v1\models;

class BpjsListTask extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpjs_list_task_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'wakturs',
                'waktu',
                'taskname',
                'taskid',
                'kodebooking',
                'wakturs_time',
                'waktu_time',
                'created_date',
                'created_by',
                'is_deleted',
                'last_sync',
            ], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }
}