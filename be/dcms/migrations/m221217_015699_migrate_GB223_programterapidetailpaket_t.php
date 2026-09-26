<?php

use yii\db\Migration;

/**
 * Class m221217_015699_migrate_GB223_programterapidetailpaket_t
 */
class m221217_015699_migrate_GB223_programterapidetailpaket_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE public.programterapidetailpaket_t ADD IF NOT EXISTS qty_pemeriksaan int4 NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221217_015699_migrate_GB223_programterapidetailpaket_t cannot be reverted.\n";
        return false;
    }
}
