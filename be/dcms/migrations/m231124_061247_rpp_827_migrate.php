<?php

use yii\db\Migration;

/**
 * Class m231124_061247_rpp_827_migrate
 */
class m231124_061247_rpp_827_migrate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $pendaftaranol_hapuskuota = file_get_contents(__DIR__ . '/definitions/pendaftaranol_hapuskuota.fn.sql');
        $this->execute($pendaftaranol_hapuskuota);

        $this->execute('DROP TRIGGER IF EXISTS "return_kuota" ON "public"."pendaftaranol_t";');

        $this->execute('
        CREATE TRIGGER "return_kuota" AFTER UPDATE ON "public"."pendaftaranol_t"
        FOR EACH ROW
        EXECUTE PROCEDURE "public"."pendaftaranol_hapuskuota"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231124_061247_rpp_827_migrate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231124_061247_rpp_827_migrate cannot be reverted.\n";

        return false;
    }
    */
}
