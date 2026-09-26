<?php

use yii\db\Migration;

/**
 * Class m220715_111248_migrate_ACN70_ACN64_insertdatalookup
 */
class m220715_111248_migrate_ACN70_ACN64_insertdatalookup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookupkeperawatan_m 
            WHERE lookupkeperawatan_id IN (
                128,
                129,
                130
            );
        ');


        $this->execute("INSERT INTO public.lookupkeperawatan_m (lookupkeperawatan_id, lookup_type,lookup_name,lookup_value,lookup_urutan) VALUES
            (128, 'anestesi_result','Agetated','Agetated',1),
            (129, 'anestesi_result','Calm','Calm',2),
            (130, 'anestesi_result','Sedated','Sedated',3);");

        $this->execute('
            DELETE FROM lookupkeperawatan_m 
            WHERE lookupkeperawatan_id IN (
                131,
                132,
                133,
                134
            );
        ');


        $this->execute("INSERT INTO public.lookupkeperawatan_m (lookupkeperawatan_id, lookup_type,lookup_name,lookup_value,lookup_urutan) VALUES
            (131, 'anestesi_regional','Epidural','Epidural',1),
            (132, 'anestesi_regional','Subdural','Subdural',2),
            (133, 'anestesi_regional','Epi+Subdural','Epi+Subdural',3),
            (134, 'anestesi_regional','Other','Other',4);");


        $this->execute('
            DELETE FROM lookupkeperawatan_m 
            WHERE lookupkeperawatan_id IN (
                135,
                136,
                137
            );
        ');

        $this->execute("INSERT INTO public.lookupkeperawatan_m (lookupkeperawatan_id, lookup_type,lookup_name,lookup_value,lookup_urutan) VALUES
            (135, 'anestesi_general','IV','IV',1),
            (136, 'anestesi_general','Inhalational','Inhalational',2),
            (137, 'anestesi_general','IV+Inhalational','IV+Inhalational',3);");

        $this->execute('
            DELETE FROM lookupkeperawatan_m 
            WHERE lookupkeperawatan_id IN (
                138,
                139
            );
        ');

        $this->execute("INSERT INTO public.lookupkeperawatan_m (lookupkeperawatan_id, lookup_type,lookup_name,lookup_value) VALUES
        (138, 'surgical_status','Active','Active'),
        (139, 'surgical_status','InActive','InActive');");


        $this->execute('
            DELETE FROM lookup_m 
            WHERE lookup_id IN (
                1245,
                1246,
                1247,
                1248,
                1249,
                1250
            );
        ');

        $this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type,lookup_name,lookup_value) VALUES
                (1245, 'status_asa','status_asa','I'),
                (1246, 'status_asa','status_asa','II'),
                (1247, 'status_asa','status_asa','III'),
                (1248, 'status_asa','status_asa','IV'),
                (1249, 'status_asa','status_asa','V'),
                (1250, 'status_asa','status_asa','E');");

        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'ruangan_anestesi\';
        ');

        $this->execute('
            INSERT INTO public.lookuptransaksi_m (kode_transaksi,kode_id,kode_fungsi,additional_value) VALUES
        (\'ruangan_anestesi\',12,\'Ruangan yang tampil di anestesi\',\'[984, 128, 1016, 1014, 1015]\');
        ');

        
        
        $this->execute('
            DELETE FROM lookup_m 
            WHERE lookup_id IN (
                1277,
                1278,
                1279,
                1280,
                1281,
                1282,
                1283,
                1284,
                1285,
                1286,
                1287,
                1288,
                1289
            );
        ');

        $this->execute("
            INSERT INTO lookup_m (lookup_id, lookup_type,lookup_name,lookup_value,lookup_urutan) VALUES
                (1277, 'patient_monitor','SP02','SP02',1),
                (1278, 'patient_monitor','ECG','ECG',2),
                (1279, 'patient_monitor','NIBP','NIBP',3),
                (1280, 'patient_monitor','FiO2','FiO2',4),
                (1281, 'patient_monitor','etCO2','etCO2',5),
                (1282, 'patient_monitor','IBP','IBP',6),
                (1283, 'patient_monitor','CVP','CVP',7),
                (1284, 'patient_monitor','PAP','PAP',8),
                (1285, 'patient_monitor','PCWP','PCWP',9),
                (1286, 'patient_monitor','BIS','BIS',10),
                (1287, 'patient_monitor','Temperature','Temperature',11),
                (1288, 'patient_monitor','Urine Catheter','UrineCatheter',12),
                (1289, 'patient_monitor','NGT','NGT',13);

        ");

        $this->execute('
            DELETE FROM lookup_m 
            WHERE lookup_id IN (
                1251,
                1265,
                1266,
                1267
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan") VALUES 
                (1251, \'aldretescore\', \'Activity\', \'act\', 1),
                (1265, \'aldretescore\', \'Respiration\', \'resp\', 2),
                (1266, \'aldretescore\', \'Circulation\', \'circ\', 3),
                (1267, \'aldretescore\', \'Consciousness\', \'cons\', 4);
        ');

        $this->execute('
            DELETE FROM lookup_m 
            WHERE lookup_id IN (
                1252,
                1253,
                1254
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan") VALUES 
                (1252, \'aldretescore_itemact\', \'Able to move 4 extrimitis\', \'2\', 1),
                (1253, \'aldretescore_itemact\', \'Able to move 2 extrimitis\', \'1\', 2),
                (1254, \'aldretescore_itemact\', \'Able to move 0 extrimitis\', \'0\', 3);
        ');

        $this->execute('
            DELETE FROM lookup_m 
            WHERE lookup_id IN (
                1255,
                1256,
                1262,
                1263,
                1264
            );
        ');

        $this->execute('
            INSERT INTO "lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan") VALUES 
                (1255, \'aldretescore_arrived\', \'15 Minute\', \'15 Minute\', 1),
                (1256, \'aldretescore_arrived\', \'30 Minute\', \'30 Minute\', 2),
                (1262, \'aldretescore_arrived\', \'1 Hour\', \'1 Hour\', 3),
                (1263, \'aldretescore_arrived\', \'2 Hour\', \'2 Hour\', 4),
                (1264, \'aldretescore_arrived\', \'Discharge from RR\', \'Discharge from RR\', 5);
        ');

        $this->execute('
            DELETE FROM lookup_m 
            WHERE lookup_id IN (
                1268,
                1269,
                1270
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan") VALUES 
            (1268, \'aldretescore_itemresp\', \'Able to breath deep and cough\', \'2\', 1),
            (1269, \'aldretescore_itemresp\', \'Limited breathing good airway\', \'1\', 2),
            (1270, \'aldretescore_itemresp\', \'Apnoea\', \'0\', 3);
        ');

        $this->execute('
            DELETE FROM lookup_m 
            WHERE lookup_id IN (
                1271,
                1272,
                1273
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan") VALUES 
                (1271, \'aldretescore_itemcirc\', \'SBP < 20 preep\', \'2\', 1),
                (1272, \'aldretescore_itemcirc\', \'SBP 20-50 preep\', \'1\', 2),
                (1273, \'aldretescore_itemcirc\', \'SBP 50 preep\', \'0\', 3);
        ');

        $this->execute('
            DELETE FROM lookup_m 
            WHERE lookup_id IN (
                1274,
                1275,
                1276
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan") VALUES 
                (1274, \'aldretescore_itemcons\', \'Awake (answer question)\', \'2\', 1),
                (1275, \'aldretescore_itemcons\', \'Arousable (by name)\', \'1\', 2),
                (1276, \'aldretescore_itemcons\', \'Nonresponsive\', \'0\', 3);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220715_111248_migrate_ACN70_ACN64_insertdatalookup cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220715_111248_migrate_ACN70_ACN64_insertdatalookup cannot be reverted.\n";

        return false;
    }
    */
}
