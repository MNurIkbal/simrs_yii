<?php

use yii\db\Migration;

/**
 * Class m251206_102000_add_tahanan_url_column_to_rujukanbantaran_t
 */
class m251206_102000_add_tahanan_url_column_to_rujukanbantaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE rujukanbantaran_t ADD COLUMN tahanan_url text NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE rujukanbantaran_t DROP COLUMN tahanan_url");
    }
}
