<?php

use yii\db\Migration;

/**
 * Class m190401_032324_seed_bagiantubuhdetail_m
 */
class m190401_032324_seed_bagiantubuhdetail_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE bagiantubuhdetail_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."bagiantubuhdetail_m"("bagiantubuhdetail_id", "bagiantubuh_id", "nama_bagiantubuh", "nama_lainnya", "kordinat_x", "kordinat_y", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1, 5, \'Alis \', \'Alis \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (2, 5, \'Bibir\', \'Bibir\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (3, 5, \'Bola Mata\', \'Bola Mata\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (4, 5, \'Bulu Mata\', \'Bulu Mata\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (5, 5, \'Dagu\', \'Dagu\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (6, 5, \'Hidung \', \'Hidung \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (7, 5, \'Jenggot \', \'Jenggot \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (8, 5, \'Jidat / Dahi\', \'Jidat / Dahi\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (9, 5, \'Kumis \', \'Kumis \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (10, 5, \'Lesung Pipi\', \'Lesung Pipi\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (11, 5, \'Rambut \', \'Rambut \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (12, 5, \'Wajah \', \'Wajah \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (13, 5, \'Pipi \', \'Pipi \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (14, 5, \'Telinga \', \'Telinga \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (15, 1, \'Payudara \', \'Payudara \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (16, 1, \'Dada \', \'Dada \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (17, 1, \'Puting \', \'Puting \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (18, 2, \'Lubang Hidung\', \'Lubang Hidung\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (19, 3, \'Betis \', \'Betis \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (20, 3, \'Jari Kaki\', \'Jari Kaki\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (21, 3, \'Mata Kaki\', \'Mata Kaki\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (22, 3, \'Paha \', \'Paha \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (23, 3, \'Punggung Kaki\', \'Punggung Kaki\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (24, 3, \'Telapak Kaki\', \'Telapak Kaki\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (25, 3, \'Tumit\', \'Tumit\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (26, 3, \'Lutut \', \'Lutut \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (27, 4, \'Kemaluan \', \'Kemaluan \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (28, 4, \'Kemaluan Laki-Laki\', \'Kemaluan Laki-Laki\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (29, 4, \'Kemaluan Perempuan\', \'Kemaluan Perempuan\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (30, 6, \'Jakun \', \'Jakun \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (31, 6, \'Tenggorokan \', \'Tenggorokan \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (32, 6, \'Leher \', \'Leher \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (33, 7, \'Kelopak Mata\', \'Kelopak Mata\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (34, 8, \'Gigi \', \'Gigi \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (35, 8, \'Gusi \', \'Gusi \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (36, 8, \'Lidah \', \'Lidah \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (37, 8, \'Mulut \', \'Mulut \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (38, 9, \'Perut \', \'Perut \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (39, 9, \'Udel / Pusar\', \'Udel / Pusar\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (40, 9, \'Rusuk / Iga\', \'Rusuk / Iga\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (41, 9, \'Panggul \', \'Panggul \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (42, 9, \'Pinggang \', \'Pinggang \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (43, 9, \'Punggung \', \'Punggung \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (44, 10, \'Jari Kelingking\', \'Jari Kelingking\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (45, 10, \'Jari Manis\', \'Jari Manis\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (46, 10, \'Jari Telunjuk\', \'Jari Telunjuk\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (47, 10, \'Jari Tengah\', \'Jari Tengah\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (48, 10, \'Jempol \', \'Jempol \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (49, 10, \'Lengan Bawah\', \'Lengan Bawah\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (50, 10, \'Pergelangan Tangan\', \'Pergelangan Tangan\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (51, 10, \'Telapak Tangan\', \'Telapak Tangan\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (52, 10, \'Ketiak \', \'Ketiak \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (53, 10, \'Kuku \', \'Kuku \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (54, 10, \'Siku \', \'Siku \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (55, 10, \'Pundak / Bahu\', \'Pundak / Bahu\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (56, 4, \'Bokong / Pantat\', \'Bokong / Pantat\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (57, 4, \'Buah Pelir\', \'Buah Pelir\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (58, 10, \'Bulu \', \'Bulu \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (59, 4, \'Dubur \', \'Dubur \', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (60, 10, \'Kulit\', \'Kulit\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (61, 10, \'Pori-Pori\', \'Pori-Pori\', NULL, NULL, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_032324_seed_bagiantubuhdetail_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_032324_seed_bagiantubuhdetail_m cannot be reverted.\n";

        return false;
    }
    */
}
