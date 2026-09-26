<?php

use yii\db\Migration;

/**
 * Class m190515_022621_sync_category_create
 */
class m190515_022621_sync_category_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
     DROP VIEW IF exists sync_category;
        ');

        $this->execute('
    CREATE OR REPLACE VIEW sync_category AS 
 SELECT jenisobatalkes_m.jenisobatalkes_kode AS code,
    jenisobatalkes_m.jenisobatalkes_nama AS name,
    COALESCE(jenisobatalkes_m.last_modified_date, jenisobatalkes_m.created_date) AS date
   FROM jenisobatalkes_m;

        ');

        $this->execute('
ALTER TABLE sync_category
  OWNER TO postgres;
        ');
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190515_022621_sync_category_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190515_022621_sync_category_create cannot be reverted.\n";

        return false;
    }
    */
}
