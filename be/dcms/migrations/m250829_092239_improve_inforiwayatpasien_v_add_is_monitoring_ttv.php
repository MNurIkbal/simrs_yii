<?php

use yii\db\Migration;

/**
 * Class m250829_092239_improve_inforiwayatpasien_v_add_is_monitoring_ttv
 */
class m250829_092239_improve_inforiwayatpasien_v_add_is_monitoring_ttv extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute("DROP VIEW IF EXISTS inforiwayatpasien_v");
        $inforiwayatpasien_v = file_get_contents(__DIR__ . '/definitions/inforiwayatpasien_v_add_is_monitoringttv_29082025.sql');
        $this->execute($inforiwayatpasien_v);
        $this->execute('ALTER TABLE "public"."inforiwayatpasien_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250829_092239_improve_inforiwayatpasien_v_add_is_monitoring_ttv cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250829_092239_improve_inforiwayatpasien_v_add_is_monitoring_ttv cannot be reverted.\n";

        return false;
    }
    */
}
