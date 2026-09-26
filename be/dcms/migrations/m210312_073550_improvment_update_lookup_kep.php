<?php

use yii\db\Migration;

/**
 * Class m210312_073550_improvment_update_lookup_kep
 */
class m210312_073550_improvment_update_lookup_kep extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->execute('
            UPDATE "public"."lookupkeperawatan_m" SET "lookup_type" = \'asmen_nyeri\', "lookup_name" = \'NRS ( Numeric Rating Scale )\', "lookup_value" = \'NRS\' WHERE "lookupkeperawatan_id" = 17;
        ');

        $this->execute('
        	UPDATE "public"."lookupkeperawatan_m" SET "lookup_type" = \'asmen_nyeri\', "lookup_name" = \'WBF ( Wong Baker Faces )\', "lookup_value" = \'WBF\' WHERE "lookupkeperawatan_id" = 18;
    	');

    	$this->execute('
        	UPDATE "public"."lookupkeperawatan_m" SET "lookup_type" = \'asmen_nyeri\', "lookup_name" = \'NIPS ( Neonatal Infant Pain Scale )\', "lookup_value" = \'NIPS\' WHERE "lookupkeperawatan_id" = 19;
    	');

    	$this->execute('
        	UPDATE "public"."lookupkeperawatan_m" SET "lookup_type" = \'asmen_nyeri\', "lookup_name" = \'FLACC ( Face, Legs, Activity, Cry, and Consolability )\', "lookup_value" = \'FLACC\' WHERE "lookupkeperawatan_id" = 20;
    	');
    	
    	$this->execute('
        	UPDATE "public"."lookupkeperawatan_m" SET "lookup_type" = \'asmen_nyeri\', "lookup_name" = \'BPS ( Behavioral Pain Scale )\', "lookup_value" = \'BPS\' WHERE "lookupkeperawatan_id" = 21;
    	');
    	
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210312_073550_improvment_update_lookup_kep cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210312_073550_improvment_update_lookup_kep cannot be reverted.\n";

        return false;
    }
    */
}
