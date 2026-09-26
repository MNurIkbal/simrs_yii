<?php

use yii\db\Migration;

/**
 * Class m201020_071651_oddo_penyesuaiantrigger_20201020
 */
class m201020_071651_oddo_penyesuaiantrigger_20201020 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('COMMENT ON TRIGGER "pengembalianuangmuka_r_insert" ON "public"."pengembalianuangmuka_t" IS \'rekap int_uangmuka_v\';');

        $this->execute('DROP TRIGGER "upd_noresep" ON "public"."penjualanresep_t";');

        $this->execute('CREATE TRIGGER "upd_noresep" BEFORE INSERT ON "public"."penjualanresep_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."upd_noresep"();');

        $this->execute('DROP TRIGGER "no_retur" ON "public"."returresep_t";');
        $this->execute('CREATE TRIGGER "no_retur" BEFORE INSERT ON "public"."returresep_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."trgr_no_retur"();');
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201020_071651_oddo_penyesuaiantrigger_20201020 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201020_071651_oddo_penyesuaiantrigger_20201020 cannot be reverted.\n";

        return false;
    }
    */
}
