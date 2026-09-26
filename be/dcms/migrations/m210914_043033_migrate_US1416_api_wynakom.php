<?php

use yii\db\Migration;

/**
 * Class m210914_043033_migrate_US1416_api_wynakom
 */
class m210914_043033_migrate_US1416_api_wynakom extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."negara_m" (
            "negara_id" serial8,
            "kode_negara" varchar(100) COLLATE "pg_catalog"."default",
            "nama_negara" varchar(255) COLLATE "pg_catalog"."default",
            "additional_data" text COLLATE "pg_catalog"."default",
            "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
            "created_by" int4,
            "modified_count" int4,
            "last_modified_date" timestamp(6),
            "last_modified_by" int4,
            "is_deleted" bool DEFAULT false,
            "is_active" bool DEFAULT true,
            "deleted_date" timestamp(6),
            "deleted_by" int4,
            CONSTRAINT "negara_m_pkey" PRIMARY KEY ("negara_id")
            );
        ');

        $this->execute('TRUNCATE TABLE negara_m RESTART IDENTITY;');

        $this->execute("
            INSERT INTO public.negara_m(negara_id, kode_negara, nama_negara, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(1, '93', 'Afganistan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(2, '27', 'Afrika Selatan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(3, '236', 'Afrika Tengah', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(4, '355', 'Albania', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(5, '213', 'Algeria', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(6, '1', 'Amerika Serikat', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(7, '376', 'Andorra', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(8, '244', 'Angola', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(9, '1-268', 'Antigua & Barbuda', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(10, '966', 'Arab Saudi', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(11, '54', 'Argentina', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(12, '374', 'Armenia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(13, '61', 'Australia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(14, '43', 'Austria', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(15, '994', 'Azerbaijan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(16, '1-242', 'Bahama', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(17, '973', 'Bahrain', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(18, '880', 'Bangladesh', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(19, '1-246', 'Barbados', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(20, '31', 'Belanda', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(21, '375', 'Belarus', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(22, '32', 'Belgia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(23, '501', 'Belize', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(24, '229', 'Benin', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(25, '975', 'Bhutan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(26, '591', 'Bolivia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(27, '387', 'Bosnia & Herzegovina', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(28, '267', 'Botswana', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(29, '55', 'Brasil', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(30, '44', 'Britania Raya (Inggris)', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(31, '673', 'Brunei Darussalam', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(32, '359', 'Bulgaria', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(33, '226', 'Burkina Faso', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(34, '257', 'Burundi', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(35, '420', 'Ceko', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(36, '235', 'Chad', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(37, '56', 'Chili', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(38, '86', 'China', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(39, '45', 'Denmark', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(40, '253', 'Djibouti', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(41, '1-767', 'Domikia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(42, '593', 'Ekuador', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(43, '503', 'El Salvador', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(44, '291', 'Eritrea', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(45, '372', 'Estonia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(46, '251', 'Ethiopia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(47, '679', 'Fiji', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(48, '63', 'Filipina', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(49, '358', 'Finlandia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(50, '241', 'Gabon', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(51, '220', 'Gambia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(52, '995', 'Georgia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(53, '233', 'Ghana', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(54, '1-473', 'Grenada', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(55, '502', 'Guatemala', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(56, '224', 'Guinea', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(57, '245', 'Guinea Bissau', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(58, '240', 'Guinea Khatulistiwa', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(59, '592', 'Guyana', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(60, '509', 'Haiti', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(61, '504', 'Honduras', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(62, '36', 'Hongaria', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(63, '852', 'Hongkong', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(64, '91', 'India', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(65, '62', 'Indonesia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(66, '964', 'Irak', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(67, '98', 'Iran', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(68, '353', 'Irlandia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(69, '354', 'Islandia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(70, '972', 'Israel', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(71, '39', 'Italia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(72, '1-876', 'Jamaika', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(73, '81', 'Jepang', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(74, '49', 'Jerman', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(75, '962', 'Jordan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(76, '855', 'Kamboja', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(77, '237', 'Kamerun', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(78, '1', 'Kanada', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(79, '7', 'Kazakhstan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(80, '254', 'Kenya', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(81, '996', 'Kirgizstan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(82, '686', 'Kiribati', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(83, '57', 'Kolombia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(84, '269', 'Komoro', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(85, '243', 'Republik Kongo', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(86, '82', 'Korea Selatan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(87, '850', 'Korea Utara', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(88, '506', 'Kosta Rika', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(89, '385', 'Kroasia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(90, '53', 'Kuba', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(91, '965', 'Kuwait', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(92, '856', 'Laos', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(93, '371', 'Latvia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(94, '961', 'Lebanon', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(95, '266', 'Lesotho', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(96, '231', 'Liberia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(97, '218', 'Libya', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(98, '423', 'Liechtenstein', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(99, '370', 'Lituania', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(100, '352', 'Luksemburg', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(101, '261', 'Madagaskar', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(102, '853', 'Makao', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(103, '389', 'Makedonia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(104, '960', 'Maladewa', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(105, '265', 'Malawi', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(106, '60', 'Malaysia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(107, '223', 'Mali', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(108, '356', 'Malta', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(109, '212', 'Maroko', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(110, '692', 'Marshall (Kep.)', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(111, '222', 'Mauritania', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(112, '230', 'Mauritius', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(113, '52', 'Meksiko', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(114, '20', 'Mesir', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(115, '691', 'Mikronesia (Kep.)', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(116, '373', 'Moldova', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(117, '377', 'Monako', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(118, '976', 'Mongolia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(119, '382', 'Montenegro', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(120, '258', 'Mozambik', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(121, '95', 'Myanmar', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(122, '264', 'Namibia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(123, '674', 'Nauru', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(124, '977', 'Nepal', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(125, '227', 'Niger', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(126, '234', 'Nigeria', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(127, '505', 'Nikaragua', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(128, '47', 'Norwegia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(129, '968', 'Oman', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(130, '92', 'Pakistan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(131, '680', 'Palau', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(132, '507', 'Panama', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(133, '225', 'Pantai Gading', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(134, '675', 'Papua Nugini', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(135, '595', 'Paraguay', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(136, '33', 'Perancis', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(137, '51', 'Peru', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(138, '48', 'Polandia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(139, '351', 'Portugal', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(140, '974', 'Qatar', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(141, '242', 'Rep. Dem. Kongo', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(142, '1-809; 1-829', 'Republik Dominika', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(143, '40', 'Rumania', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(144, '7', 'Rusia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(145, '250', 'Rwanda', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(146, '1-869', 'Saint Kitts and Nevis', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(147, '1-758', 'Saint Lucia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(148, '1-784', 'Saint Vincent & the Grenadines', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(149, '685', 'Samoa', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(150, '378', 'San Marino', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(151, '239', 'Sao Tome & Principe', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(152, '64', 'Selandia Baru', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(153, '221', 'Senegal', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(154, '381', 'Serbia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(155, '248', 'Seychelles', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(156, '232', 'Sierra Leone', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(157, '65', 'Singapura', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(158, '357', 'Siprus', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(159, '386', 'Slovenia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(160, '421', 'Slowakia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(161, '677', 'Solomon (Kep.)', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(162, '252', 'Somalia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(163, '34', 'Spanyol', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(164, '94', 'Sri Lanka', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(165, '249', 'Sudan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(166, '211', 'Sudan Selatan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(167, '963', 'Suriah', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(168, '597', 'Suriname', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(169, '268', 'Swaziland', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(170, '46', 'Swedia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(171, '41', 'Swiss', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(172, '992', 'Tajikistan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(173, '238', 'Tanjung Verde', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(174, '255', 'Tanzania', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(175, '886', 'Taiwan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(176, '66', 'Thailand', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(177, '670', 'Timor Leste', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(178, '228', 'Togo', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(179, '676', 'Tonga', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(180, '1-868', 'Trinidad & Tobago', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(181, '216', 'Tunisia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(182, '90', 'Turki', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(183, '993', 'Turkmenistan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(184, '688', 'Tuvalu', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(185, '256', 'Uganda', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(186, '380', 'Ukraina', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(187, '971', 'Uni Emirat Arab', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(188, '598', 'Uruguay', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(189, '998', 'Uzbekistan', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(190, '678', 'Vanuatu', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(191, '58', 'Venezuela', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(192, '84', 'Vietnam', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(193, '967', 'Yaman', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(194, '30', 'Yunani', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(195, '260', 'Zambia', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(196, '263', 'Zimbabwe', NULL, '2021-09-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");

        $this->execute('ALTER TABLE "public"."pasien_m" ADD COLUMN if not exists "alergi" text COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."pasien_m" ADD COLUMN if not exists "kode_pos" varchar(100) COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."pasien_m" ADD COLUMN if not exists "negara_id" int4;');
        $this->execute('COMMENT ON COLUMN "public"."pasien_m"."alamatdepan" IS \'\';');

        $this->execute('DROP VIEW if exists "public"."bridging_orderlab_v";');

        $this->execute("
            CREATE VIEW \"public\".\"bridging_orderlab_v\" AS  SELECT pasien_m.no_rekam_medik,
    jeniskelamin.lookup_kode AS gender_id,
    jeniskelamin.lookup_value AS gender_name,
    pasien_m.tanggal_lahir AS dateofbirth,
    pasien_m.nama_pasien AS patient_name,
    pasien_m.alamat_pasien AS patient_address,
    pasien_m.kabupaten_id AS city_id,
    kabupaten_m.kabupaten_nama AS city_name,
    pasien_m.no_telepon_pasien AS phone_number,
    pasien_m.no_mobile_pasien AS mobile_number,
    pasien_m.alamatemail AS email,
    pendaftaran_t.no_pendaftaran AS visit_number,
    pasienmasukpenunjang_t.no_masukpenunjang AS no_order,
    pasienmasukpenunjang_t.tglmasukpenunjang AS order_datetime,
    pasienmasukpenunjang_t.instalasiasal_id AS service_unit_id,
    instalasi_m.instalasi_nama AS service_unit_name,
    pendaftaran_t.penjamin_id AS guarantor_id,
    penjamin_m.penjamin_nama AS guarantor_name,
    pasienmasukpenunjang_t.kelaspelayanan_id AS aggreement_id,
    kelaspelayanan_m.kelaspelayanan_nama AS aggreement_name,
    pegawai_m.pegawai_id AS doctor_id,
    pegawai_m.nama_pegawai AS doctor_name,
    pendaftaran_t.kelaspelayanan_id AS class_id,
    kelas.kelaspelayanan_nama AS class_name,
    pasienmasukpenunjang_t.ruanganasal_id AS ward_id,
    ruangan_m.ruangan_nama AS ward_name,
    pasienadmisi_t.kamarruangan_id AS room_id,
    kamarruangan_m.kamarruangan_nokamar AS room_name,
    pasienadmisi_t.kamartempattidur_id AS bed_id,
    kamartempattidur_m.no_tempattidur AS bed_name,
    diagnosa.diagnosa_utama AS diagnosa_id,
    diagnosa.diagnosa_utama AS diagnosa_name,
    pasienmasukpenunjang_t.created_by AS reg_user_id,
    loginpemakai_k.nama_pemakai AS reg_user_name,
    pasienmasukpenunjang_t.created_date,
    NULL::text AS fax_number,
        CASE
            WHEN (( SELECT count(countcyto.permintaankepenunjang_id) AS count
               FROM permintaankepenunjang_t countcyto
              WHERE countcyto.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id AND countcyto.is_cyto IS TRUE)) > 0 THEN true
            ELSE false
        END AS is_cyto,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'lis_reg_no'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'lis_reg_no'::text
            ELSE NULL::text
        END AS lis_reg_no,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_dt'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_dt'::text
            ELSE NULL::text
        END AS retrieved_dt,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'status'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'status'::text
            ELSE NULL::text
        END AS status,
        CASE
            WHEN ((pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_flag'::text) = '1'::text THEN true
            WHEN ((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_flag'::text) = '1'::text THEN true
            ELSE false
        END AS retrieved_flag,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) <> 0 THEN true
            ELSE
            CASE
                WHEN ((pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) = '1'::text THEN true
                WHEN ((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) = '1'::text THEN true
                ELSE false
            END
        END AS received_flag,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
           FROM ( SELECT pasienmasukpenunjang_t_1.no_masukpenunjang AS no_order,
                    permintaankepenunjang_t.daftartindakan_id AS no_pemeriksaan,
                    daftartindakan_m.daftartindakan_kode AS order_item_id,
                    daftartindakan_m.daftartindakan_nama AS order_item_name,
                    permintaankepenunjang_t.permintaankepenunjang_id
                   FROM permintaankepenunjang_t
                     JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN pasienkirimkeunitlain_t pasienkirimkeunitlain_t_1 ON permintaankepenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t_1.pasienkirimkeunitlain_id
                     JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON pasienkirimkeunitlain_t_1.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
                  WHERE pasienkirimkeunitlain_t_1.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id) d) AS ordered_items,
        CASE instalasi_m.instalasi_id
            WHEN 2 THEN true
            WHEN 3 THEN true
            ELSE
            CASE pendaftaran_t.penjamin_id
                WHEN 1 THEN pasienmasukpenunjang_t.is_bayar
                ELSE true
            END
        END AS is_bayar,
    pasien_m.additional_pasien,
    propinsi_m.kode_propinsi AS kode_kemendag_propinsi,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten) AS kode_kemendag_kabupaten,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan) AS kode_kemendag_kecamatan,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan, kelurahan_m.kode_kelurahan) AS kode_kemendag_kelurahan,
    pasien_m.rt,
    pasien_m.rw,
    propinsi_m.propinsi_nama AS propinsi,
    kabupaten_m.kabupaten_nama AS kabupaten,
    kecamatan_m.kecamatan_nama AS kecamatan,
    kelurahan_m.kelurahan_nama AS kelurahan,
    negara_m.nama_negara AS negara,
    negara_m.kode_negara
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN kelaspelayanan_m ON pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN loginpemakai_k ON pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id
     JOIN kelaspelayanan_m kelas ON pendaftaran_t.kelaspelayanan_id = kelas.kelaspelayanan_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN pegawai_m ON COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN lookup_m jeniskelamin ON pasien_m.jeniskelamin::integer = jeniskelamin.lookup_id
     JOIN pasienkirimkeunitlain_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_wynacom_t.his_reg_no
           FROM hasilpemeriksaanlab_wynacom_t
          GROUP BY hasilpemeriksaanlab_wynacom_t.his_reg_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.his_reg_no::text
     LEFT JOIN propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
     LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
     LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
     LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
     LEFT JOIN negara_m ON pasien_m.negara_id = negara_m.negara_id
  WHERE pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NOT NULL AND pasienkirimkeunitlain_t.instalasi_id = 4
UNION ALL
 SELECT pasien_m.no_rekam_medik,
    jeniskelamin.lookup_kode AS gender_id,
    jeniskelamin.lookup_value AS gender_name,
    pasien_m.tanggal_lahir AS dateofbirth,
    pasien_m.nama_pasien AS patient_name,
    pasien_m.alamat_pasien AS patient_address,
    pasien_m.kabupaten_id AS city_id,
    kabupaten_m.kabupaten_nama AS city_name,
    pasien_m.no_telepon_pasien AS phone_number,
    pasien_m.no_mobile_pasien AS mobile_number,
    pasien_m.alamatemail AS email,
    pendaftaran_t.no_pendaftaran AS visit_number,
    pasienmasukpenunjang_t.no_masukpenunjang AS no_order,
    pasienmasukpenunjang_t.tglmasukpenunjang AS order_datetime,
    pasienmasukpenunjang_t.instalasiasal_id AS service_unit_id,
    instalasi_m.instalasi_nama AS service_unit_name,
    pendaftaran_t.penjamin_id AS guarantor_id,
    penjamin_m.penjamin_nama AS guarantor_name,
    pasienmasukpenunjang_t.kelaspelayanan_id AS aggreement_id,
    kelaspelayanan_m.kelaspelayanan_nama AS aggreement_name,
    pendaftaran_t.pegawai_id AS doctor_id,
    pegawai_m.nama_pegawai AS doctor_name,
    pendaftaran_t.kelaspelayanan_id AS class_id,
    kelaspelayanan_m.kelaspelayanan_nama AS class_name,
    pasienmasukpenunjang_t.ruanganasal_id AS ward_id,
    ruangan_m.ruangan_nama AS ward_name,
    ruangan_m.ruangan_id AS room_id,
    ruangan_m.ruangan_nama AS room_name,
    NULL::integer AS bed_id,
    NULL::character varying AS bed_name,
    diagnosa.diagnosa_utama AS diagnosa_id,
    diagnosa.diagnosa_utama AS diagnosa_name,
    pasienmasukpenunjang_t.created_by AS reg_user_id,
    loginpemakai_k.nama_pemakai AS reg_user_name,
    pasienmasukpenunjang_t.created_date,
    NULL::text AS fax_number,
        CASE
            WHEN (( SELECT count(countcyto.permintaankepenunjang_id) AS count
               FROM permintaankepenunjang_t countcyto
              WHERE countcyto.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id AND countcyto.is_cyto IS TRUE)) > 0 THEN true
            ELSE false
        END AS is_cyto,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'lis_reg_no'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'lis_reg_no'::text
            ELSE NULL::text
        END AS lis_reg_no,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_dt'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_dt'::text
            ELSE NULL::text
        END AS retrieved_dt,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'status'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'status'::text
            ELSE NULL::text
        END AS status,
        CASE
            WHEN ((pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_flag'::text) = '1'::text THEN true
            WHEN ((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_flag'::text) = '1'::text THEN true
            ELSE false
        END AS retrieved_flag,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) <> 0 THEN true
            ELSE
            CASE
                WHEN ((pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) = '1'::text THEN true
                WHEN ((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) = '1'::text THEN true
                ELSE false
            END
        END AS received_flag,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
           FROM ( SELECT pasienmasukpenunjang.no_masukpenunjang AS no_order,
                    tindakanpelayanan_t.daftartindakan_id AS no_pemeriksaan,
                    daftartindakan_m.daftartindakan_kode AS order_item_id,
                    daftartindakan_m.daftartindakan_nama AS order_item_name,
                    pasienmasukpenunjang.pasienmasukpenunjang_id
                   FROM pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN tindakanpelayanan_t ON pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                  WHERE pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id) d) AS ordered_items,
        CASE instalasi_m.instalasi_id
            WHEN 2 THEN true
            WHEN 3 THEN true
            ELSE
            CASE pendaftaran_t.penjamin_id
                WHEN 1 THEN pasienmasukpenunjang_t.is_bayar
                ELSE true
            END
        END AS is_bayar,
    pasien_m.additional_pasien,
    propinsi_m.kode_propinsi AS kode_kemendag_propinsi,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten) AS kode_kemendag_kabupaten,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan) AS kode_kemendag_kecamatan,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan, kelurahan_m.kode_kelurahan) AS kode_kemendag_kelurahan,
    pasien_m.rt,
    pasien_m.rw,
    propinsi_m.propinsi_nama AS propinsi,
    kabupaten_m.kabupaten_nama AS kabupaten,
    kecamatan_m.kecamatan_nama AS kecamatan,
    kelurahan_m.kelurahan_nama AS kelurahan,
    negara_m.nama_negara AS negara,
    negara_m.kode_negara
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN lookup_m jeniskelamin ON pasien_m.jeniskelamin::integer = jeniskelamin.lookup_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN loginpemakai_k ON pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_wynacom_t.his_reg_no
           FROM hasilpemeriksaanlab_wynacom_t
          GROUP BY hasilpemeriksaanlab_wynacom_t.his_reg_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.his_reg_no::text
     LEFT JOIN propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
     LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
     LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
     LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
     LEFT JOIN negara_m ON pasien_m.negara_id = negara_m.negara_id
  WHERE pendaftaran_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL
UNION ALL
 SELECT pasien_m.no_rekam_medik,
    jeniskelamin.lookup_kode AS gender_id,
    jeniskelamin.lookup_value AS gender_name,
    pasien_m.tanggal_lahir AS dateofbirth,
    pasien_m.nama_pasien AS patient_name,
    pasien_m.alamat_pasien AS patient_address,
    pasien_m.kabupaten_id AS city_id,
    kabupaten_m.kabupaten_nama AS city_name,
    pasien_m.no_telepon_pasien AS phone_number,
    pasien_m.no_mobile_pasien AS mobile_number,
    pasien_m.alamatemail AS email,
    pendaftaran_t.no_pendaftaran AS visit_number,
    pasienmasukpenunjang_t.no_masukpenunjang AS no_order,
    pasienmasukpenunjang_t.tglmasukpenunjang AS order_datetime,
    pasienmasukpenunjang_t.instalasiasal_id AS service_unit_id,
    instalasi_m.instalasi_nama AS service_unit_name,
    pendaftaran_t.penjamin_id AS guarantor_id,
    penjamin_m.penjamin_nama AS guarantor_name,
    pasienmasukpenunjang_t.kelaspelayanan_id AS aggreement_id,
    kelaspelayanan_m.kelaspelayanan_nama AS aggreement_name,
    pendaftaran_t.pegawai_id AS doctor_id,
    pegawai_m.nama_pegawai AS doctor_name,
    pendaftaran_t.kelaspelayanan_id AS class_id,
    kelaspelayanan_m.kelaspelayanan_nama AS class_name,
    pasienmasukpenunjang_t.ruanganasal_id AS ward_id,
    ruangan_m.ruangan_nama AS ward_name,
    ruangan_m.ruangan_id AS room_id,
    ruangan_m.ruangan_nama AS room_name,
    NULL::integer AS bed_id,
    NULL::character varying AS bed_name,
    diagnosa.diagnosa_utama AS diagnosa_id,
    diagnosa.diagnosa_utama AS diagnosa_name,
    pasienmasukpenunjang_t.created_by AS reg_user_id,
    loginpemakai_k.nama_pemakai AS reg_user_name,
    pasienmasukpenunjang_t.created_date,
    NULL::text AS fax_number,
        CASE
            WHEN (( SELECT count(countcyto.permintaankepenunjang_id) AS count
               FROM permintaankepenunjang_t countcyto
              WHERE countcyto.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id AND countcyto.is_cyto IS TRUE)) > 0 THEN true
            ELSE false
        END AS is_cyto,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'lis_reg_no'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'lis_reg_no'::text
            ELSE NULL::text
        END AS lis_reg_no,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_dt'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_dt'::text
            ELSE NULL::text
        END AS retrieved_dt,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'status'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'status'::text
            ELSE NULL::text
        END AS status,
        CASE
            WHEN ((pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_flag'::text) = '1'::text THEN true
            WHEN ((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_flag'::text) = '1'::text THEN true
            ELSE false
        END AS retrieved_flag,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) <> 0 THEN true
            ELSE
            CASE
                WHEN ((pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) = '1'::text THEN true
                WHEN ((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) = '1'::text THEN true
                ELSE false
            END
        END AS received_flag,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
           FROM ( SELECT pasienmasukpenunjang.no_masukpenunjang AS no_order,
                    tindakanpelayanan_t.daftartindakan_id AS no_pemeriksaan,
                    daftartindakan_m.daftartindakan_kode AS order_item_id,
                    daftartindakan_m.daftartindakan_nama AS order_item_name,
                    pasienmasukpenunjang.pasienmasukpenunjang_id
                   FROM pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN tindakanpelayanan_t ON pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                  WHERE pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id) d) AS ordered_items,
        CASE instalasi_m.instalasi_id
            WHEN 2 THEN true
            ELSE
            CASE pendaftaran_t.penjamin_id
                WHEN 1 THEN pasienmasukpenunjang_t.is_bayar
                ELSE true
            END
        END AS is_bayar,
    pasien_m.additional_pasien,
    propinsi_m.kode_propinsi AS kode_kemendag_propinsi,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten) AS kode_kemendag_kabupaten,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan) AS kode_kemendag_kecamatan,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan, kelurahan_m.kode_kelurahan) AS kode_kemendag_kelurahan,
    pasien_m.rt,
    pasien_m.rw,
    propinsi_m.propinsi_nama AS propinsi,
    kabupaten_m.kabupaten_nama AS kabupaten,
    kecamatan_m.kecamatan_nama AS kecamatan,
    kelurahan_m.kelurahan_nama AS kelurahan,
    negara_m.nama_negara AS negara,
    negara_m.kode_negara
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
     LEFT JOIN lookup_m jeniskelamin ON pasien_m.jeniskelamin::integer = jeniskelamin.lookup_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
     JOIN loginpemakai_k ON pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_wynacom_t.his_reg_no
           FROM hasilpemeriksaanlab_wynacom_t
          GROUP BY hasilpemeriksaanlab_wynacom_t.his_reg_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.his_reg_no::text
     LEFT JOIN propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
     LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
     LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
     LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
     LEFT JOIN negara_m ON pasien_m.negara_id = negara_m.negara_id
  WHERE ruang_penunjang.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL;");

    

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210914_043033_migrate_US1416_api_wynakom cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210914_043033_migrate_US1416_api_wynakom cannot be reverted.\n";

        return false;
    }
    */
}
