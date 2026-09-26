<?php

use yii\db\Migration;

/**
 * Class m240603_112517_migrate_hotfix_indesing_peformance_reseptur
 */
class m240603_112517_migrate_hotfix_indesing_peformance_reseptur extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->execute('
	               CREATE INDEX IF NOT EXISTS "resepturdetail_reseptur_id_idx" ON "public"."resepturdetail_t" (
	                 "reseptur_id"
	               );
	           ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240603_112517_migrate_hotfix_indesing_peformance_reseptur cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240603_112517_migrate_hotfix_indesing_peformance_reseptur cannot be reverted.\n";

        return false;
    }
    */
}
