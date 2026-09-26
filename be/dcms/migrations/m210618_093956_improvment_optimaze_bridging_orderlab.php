<?php

use yii\db\Migration;

/**
 * Class m210618_093956_improvment_optimaze_bridging_orderlab
 */
class m210618_093956_improvment_optimaze_bridging_orderlab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE INDEX IF NOT EXISTS "pasienmasukpen_pasien_id" ON "public"."pasienmasukpenunjang_t" USING btree (
              "pasien_id"
            );
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS  "pasienmasukpen_pendaftaran_id" ON "public"."pasienmasukpenunjang_t" USING btree (
                  "pendaftaran_id"
                );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210618_093956_improvment_optimaze_bridging_orderlab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210618_093956_improvment_optimaze_bridging_orderlab cannot be reverted.\n";

        return false;
    }
    */
}
