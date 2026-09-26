<?php

use yii\db\Migration;

/**
 * Class m211130_091257_improvment_bmhp_tanpa_tindakan
 */
class m211130_091257_improvment_bmhp_tanpa_tindakan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."instruksitindakanbmhp_t" ALTER COLUMN "instruksi_id" DROP NOT NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211130_091257_improvment_bmhp_tanpa_tindakan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211130_091257_improvment_bmhp_tanpa_tindakan cannot be reverted.\n";

        return false;
    }
    */
}
