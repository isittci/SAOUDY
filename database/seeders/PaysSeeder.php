<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaysSeeder extends Seeder
{
    /**
     * Seeder pour enregistrer tous les pays du monde
     * Inclut : nom, code ISO alpha-2, code ISO alpha-3, indicatif téléphonique
     */
    public function run(): void
    {
        $pays = [
            // A
            ['nom' => 'Afghanistan', 'code_iso_2' => 'AF', 'code_iso_3' => 'AFG', 'indicatif' => '+93'],
            ['nom' => 'Afrique du Sud', 'code_iso_2' => 'ZA', 'code_iso_3' => 'ZAF', 'indicatif' => '+27'],
            ['nom' => 'Albanie', 'code_iso_2' => 'AL', 'code_iso_3' => 'ALB', 'indicatif' => '+355'],
            ['nom' => 'Algérie', 'code_iso_2' => 'DZ', 'code_iso_3' => 'DZA', 'indicatif' => '+213'],
            ['nom' => 'Allemagne', 'code_iso_2' => 'DE', 'code_iso_3' => 'DEU', 'indicatif' => '+49'],
            ['nom' => 'Andorre', 'code_iso_2' => 'AD', 'code_iso_3' => 'AND', 'indicatif' => '+376'],
            ['nom' => 'Angola', 'code_iso_2' => 'AO', 'code_iso_3' => 'AGO', 'indicatif' => '+244'],
            ['nom' => 'Antigua-et-Barbuda', 'code_iso_2' => 'AG', 'code_iso_3' => 'ATG', 'indicatif' => '+1-268'],
            ['nom' => 'Arabie Saoudite', 'code_iso_2' => 'SA', 'code_iso_3' => 'SAU', 'indicatif' => '+966'],
            ['nom' => 'Argentine', 'code_iso_2' => 'AR', 'code_iso_3' => 'ARG', 'indicatif' => '+54'],
            ['nom' => 'Arménie', 'code_iso_2' => 'AM', 'code_iso_3' => 'ARM', 'indicatif' => '+374'],
            ['nom' => 'Australie', 'code_iso_2' => 'AU', 'code_iso_3' => 'AUS', 'indicatif' => '+61'],
            ['nom' => 'Autriche', 'code_iso_2' => 'AT', 'code_iso_3' => 'AUT', 'indicatif' => '+43'],
            ['nom' => 'Azerbaïdjan', 'code_iso_2' => 'AZ', 'code_iso_3' => 'AZE', 'indicatif' => '+994'],

            // B
            ['nom' => 'Bahamas', 'code_iso_2' => 'BS', 'code_iso_3' => 'BHS', 'indicatif' => '+1-242'],
            ['nom' => 'Bahreïn', 'code_iso_2' => 'BH', 'code_iso_3' => 'BHR', 'indicatif' => '+973'],
            ['nom' => 'Bangladesh', 'code_iso_2' => 'BD', 'code_iso_3' => 'BGD', 'indicatif' => '+880'],
            ['nom' => 'Barbade', 'code_iso_2' => 'BB', 'code_iso_3' => 'BRB', 'indicatif' => '+1-246'],
            ['nom' => 'Belgique', 'code_iso_2' => 'BE', 'code_iso_3' => 'BEL', 'indicatif' => '+32'],
            ['nom' => 'Belize', 'code_iso_2' => 'BZ', 'code_iso_3' => 'BLZ', 'indicatif' => '+501'],
            ['nom' => 'Bénin', 'code_iso_2' => 'BJ', 'code_iso_3' => 'BEN', 'indicatif' => '+229'],
            ['nom' => 'Bhoutan', 'code_iso_2' => 'BT', 'code_iso_3' => 'BTN', 'indicatif' => '+975'],
            ['nom' => 'Biélorussie', 'code_iso_2' => 'BY', 'code_iso_3' => 'BLR', 'indicatif' => '+375'],
            ['nom' => 'Birmanie (Myanmar)', 'code_iso_2' => 'MM', 'code_iso_3' => 'MMR', 'indicatif' => '+95'],
            ['nom' => 'Bolivie', 'code_iso_2' => 'BO', 'code_iso_3' => 'BOL', 'indicatif' => '+591'],
            ['nom' => 'Bosnie-Herzégovine', 'code_iso_2' => 'BA', 'code_iso_3' => 'BIH', 'indicatif' => '+387'],
            ['nom' => 'Botswana', 'code_iso_2' => 'BW', 'code_iso_3' => 'BWA', 'indicatif' => '+267'],
            ['nom' => 'Brésil', 'code_iso_2' => 'BR', 'code_iso_3' => 'BRA', 'indicatif' => '+55'],
            ['nom' => 'Brunei', 'code_iso_2' => 'BN', 'code_iso_3' => 'BRN', 'indicatif' => '+673'],
            ['nom' => 'Bulgarie', 'code_iso_2' => 'BG', 'code_iso_3' => 'BGR', 'indicatif' => '+359'],
            ['nom' => 'Burkina Faso', 'code_iso_2' => 'BF', 'code_iso_3' => 'BFA', 'indicatif' => '+226'],
            ['nom' => 'Burundi', 'code_iso_2' => 'BI', 'code_iso_3' => 'BDI', 'indicatif' => '+257'],

            // C
            ['nom' => 'Cambodge', 'code_iso_2' => 'KH', 'code_iso_3' => 'KHM', 'indicatif' => '+855'],
            ['nom' => 'Cameroun', 'code_iso_2' => 'CM', 'code_iso_3' => 'CMR', 'indicatif' => '+237'],
            ['nom' => 'Canada', 'code_iso_2' => 'CA', 'code_iso_3' => 'CAN', 'indicatif' => '+1'],
            ['nom' => 'Cap-Vert', 'code_iso_2' => 'CV', 'code_iso_3' => 'CPV', 'indicatif' => '+238'],
            ['nom' => 'Centrafrique', 'code_iso_2' => 'CF', 'code_iso_3' => 'CAF', 'indicatif' => '+236'],
            ['nom' => 'Chili', 'code_iso_2' => 'CL', 'code_iso_3' => 'CHL', 'indicatif' => '+56'],
            ['nom' => 'Chine', 'code_iso_2' => 'CN', 'code_iso_3' => 'CHN', 'indicatif' => '+86'],
            ['nom' => 'Chypre', 'code_iso_2' => 'CY', 'code_iso_3' => 'CYP', 'indicatif' => '+357'],
            ['nom' => 'Colombie', 'code_iso_2' => 'CO', 'code_iso_3' => 'COL', 'indicatif' => '+57'],
            ['nom' => 'Comores', 'code_iso_2' => 'KM', 'code_iso_3' => 'COM', 'indicatif' => '+269'],
            ['nom' => 'Corée du Nord', 'code_iso_2' => 'KP', 'code_iso_3' => 'PRK', 'indicatif' => '+850'],
            ['nom' => 'Corée du Sud', 'code_iso_2' => 'KR', 'code_iso_3' => 'KOR', 'indicatif' => '+82'],
            ['nom' => 'Costa Rica', 'code_iso_2' => 'CR', 'code_iso_3' => 'CRI', 'indicatif' => '+506'],
            ['nom' => 'Côte d\'Ivoire', 'code_iso_2' => 'CI', 'code_iso_3' => 'CIV', 'indicatif' => '+225'],
            ['nom' => 'Croatie', 'code_iso_2' => 'HR', 'code_iso_3' => 'HRV', 'indicatif' => '+385'],
            ['nom' => 'Cuba', 'code_iso_2' => 'CU', 'code_iso_3' => 'CUB', 'indicatif' => '+53'],

            // D
            ['nom' => 'Danemark', 'code_iso_2' => 'DK', 'code_iso_3' => 'DNK', 'indicatif' => '+45'],
            ['nom' => 'Djibouti', 'code_iso_2' => 'DJ', 'code_iso_3' => 'DJI', 'indicatif' => '+253'],
            ['nom' => 'Dominique', 'code_iso_2' => 'DM', 'code_iso_3' => 'DMA', 'indicatif' => '+1-767'],

            // E
            ['nom' => 'Égypte', 'code_iso_2' => 'EG', 'code_iso_3' => 'EGY', 'indicatif' => '+20'],
            ['nom' => 'Émirats Arabes Unis', 'code_iso_2' => 'AE', 'code_iso_3' => 'ARE', 'indicatif' => '+971'],
            ['nom' => 'Équateur', 'code_iso_2' => 'EC', 'code_iso_3' => 'ECU', 'indicatif' => '+593'],
            ['nom' => 'Érythrée', 'code_iso_2' => 'ER', 'code_iso_3' => 'ERI', 'indicatif' => '+291'],
            ['nom' => 'Espagne', 'code_iso_2' => 'ES', 'code_iso_3' => 'ESP', 'indicatif' => '+34'],
            ['nom' => 'Estonie', 'code_iso_2' => 'EE', 'code_iso_3' => 'EST', 'indicatif' => '+372'],
            ['nom' => 'Eswatini', 'code_iso_2' => 'SZ', 'code_iso_3' => 'SWZ', 'indicatif' => '+268'],
            ['nom' => 'États-Unis', 'code_iso_2' => 'US', 'code_iso_3' => 'USA', 'indicatif' => '+1'],
            ['nom' => 'Éthiopie', 'code_iso_2' => 'ET', 'code_iso_3' => 'ETH', 'indicatif' => '+251'],

            // F
            ['nom' => 'Fidji', 'code_iso_2' => 'FJ', 'code_iso_3' => 'FJI', 'indicatif' => '+679'],
            ['nom' => 'Finlande', 'code_iso_2' => 'FI', 'code_iso_3' => 'FIN', 'indicatif' => '+358'],
            ['nom' => 'France', 'code_iso_2' => 'FR', 'code_iso_3' => 'FRA', 'indicatif' => '+33'],

            // G
            ['nom' => 'Gabon', 'code_iso_2' => 'GA', 'code_iso_3' => 'GAB', 'indicatif' => '+241'],
            ['nom' => 'Gambie', 'code_iso_2' => 'GM', 'code_iso_3' => 'GMB', 'indicatif' => '+220'],
            ['nom' => 'Géorgie', 'code_iso_2' => 'GE', 'code_iso_3' => 'GEO', 'indicatif' => '+995'],
            ['nom' => 'Ghana', 'code_iso_2' => 'GH', 'code_iso_3' => 'GHA', 'indicatif' => '+233'],
            ['nom' => 'Grèce', 'code_iso_2' => 'GR', 'code_iso_3' => 'GRC', 'indicatif' => '+30'],
            ['nom' => 'Grenade', 'code_iso_2' => 'GD', 'code_iso_3' => 'GRD', 'indicatif' => '+1-473'],
            ['nom' => 'Guatemala', 'code_iso_2' => 'GT', 'code_iso_3' => 'GTM', 'indicatif' => '+502'],
            ['nom' => 'Guinée', 'code_iso_2' => 'GN', 'code_iso_3' => 'GIN', 'indicatif' => '+224'],
            ['nom' => 'Guinée-Bissau', 'code_iso_2' => 'GW', 'code_iso_3' => 'GNB', 'indicatif' => '+245'],
            ['nom' => 'Guinée équatoriale', 'code_iso_2' => 'GQ', 'code_iso_3' => 'GNQ', 'indicatif' => '+240'],
            ['nom' => 'Guyana', 'code_iso_2' => 'GY', 'code_iso_3' => 'GUY', 'indicatif' => '+592'],

            // H
            ['nom' => 'Haïti', 'code_iso_2' => 'HT', 'code_iso_3' => 'HTI', 'indicatif' => '+509'],
            ['nom' => 'Honduras', 'code_iso_2' => 'HN', 'code_iso_3' => 'HND', 'indicatif' => '+504'],
            ['nom' => 'Hongrie', 'code_iso_2' => 'HU', 'code_iso_3' => 'HUN', 'indicatif' => '+36'],

            // I
            ['nom' => 'Inde', 'code_iso_2' => 'IN', 'code_iso_3' => 'IND', 'indicatif' => '+91'],
            ['nom' => 'Indonésie', 'code_iso_2' => 'ID', 'code_iso_3' => 'IDN', 'indicatif' => '+62'],
            ['nom' => 'Irak', 'code_iso_2' => 'IQ', 'code_iso_3' => 'IRQ', 'indicatif' => '+964'],
            ['nom' => 'Iran', 'code_iso_2' => 'IR', 'code_iso_3' => 'IRN', 'indicatif' => '+98'],
            ['nom' => 'Irlande', 'code_iso_2' => 'IE', 'code_iso_3' => 'IRL', 'indicatif' => '+353'],
            ['nom' => 'Islande', 'code_iso_2' => 'IS', 'code_iso_3' => 'ISL', 'indicatif' => '+354'],
            ['nom' => 'Israël', 'code_iso_2' => 'IL', 'code_iso_3' => 'ISR', 'indicatif' => '+972'],
            ['nom' => 'Italie', 'code_iso_2' => 'IT', 'code_iso_3' => 'ITA', 'indicatif' => '+39'],

            // J
            ['nom' => 'Jamaïque', 'code_iso_2' => 'JM', 'code_iso_3' => 'JAM', 'indicatif' => '+1-876'],
            ['nom' => 'Japon', 'code_iso_2' => 'JP', 'code_iso_3' => 'JPN', 'indicatif' => '+81'],
            ['nom' => 'Jordanie', 'code_iso_2' => 'JO', 'code_iso_3' => 'JOR', 'indicatif' => '+962'],

            // K
            ['nom' => 'Kazakhstan', 'code_iso_2' => 'KZ', 'code_iso_3' => 'KAZ', 'indicatif' => '+7'],
            ['nom' => 'Kenya', 'code_iso_2' => 'KE', 'code_iso_3' => 'KEN', 'indicatif' => '+254'],
            ['nom' => 'Kirghizistan', 'code_iso_2' => 'KG', 'code_iso_3' => 'KGZ', 'indicatif' => '+996'],
            ['nom' => 'Kiribati', 'code_iso_2' => 'KI', 'code_iso_3' => 'KIR', 'indicatif' => '+686'],
            ['nom' => 'Koweït', 'code_iso_2' => 'KW', 'code_iso_3' => 'KWT', 'indicatif' => '+965'],

            // L
            ['nom' => 'Laos', 'code_iso_2' => 'LA', 'code_iso_3' => 'LAO', 'indicatif' => '+856'],
            ['nom' => 'Lesotho', 'code_iso_2' => 'LS', 'code_iso_3' => 'LSO', 'indicatif' => '+266'],
            ['nom' => 'Lettonie', 'code_iso_2' => 'LV', 'code_iso_3' => 'LVA', 'indicatif' => '+371'],
            ['nom' => 'Liban', 'code_iso_2' => 'LB', 'code_iso_3' => 'LBN', 'indicatif' => '+961'],
            ['nom' => 'Liberia', 'code_iso_2' => 'LR', 'code_iso_3' => 'LBR', 'indicatif' => '+231'],
            ['nom' => 'Libye', 'code_iso_2' => 'LY', 'code_iso_3' => 'LBY', 'indicatif' => '+218'],
            ['nom' => 'Liechtenstein', 'code_iso_2' => 'LI', 'code_iso_3' => 'LIE', 'indicatif' => '+423'],
            ['nom' => 'Lituanie', 'code_iso_2' => 'LT', 'code_iso_3' => 'LTU', 'indicatif' => '+370'],
            ['nom' => 'Luxembourg', 'code_iso_2' => 'LU', 'code_iso_3' => 'LUX', 'indicatif' => '+352'],

            // M
            ['nom' => 'Macédoine du Nord', 'code_iso_2' => 'MK', 'code_iso_3' => 'MKD', 'indicatif' => '+389'],
            ['nom' => 'Madagascar', 'code_iso_2' => 'MG', 'code_iso_3' => 'MDG', 'indicatif' => '+261'],
            ['nom' => 'Malaisie', 'code_iso_2' => 'MY', 'code_iso_3' => 'MYS', 'indicatif' => '+60'],
            ['nom' => 'Malawi', 'code_iso_2' => 'MW', 'code_iso_3' => 'MWI', 'indicatif' => '+265'],
            ['nom' => 'Maldives', 'code_iso_2' => 'MV', 'code_iso_3' => 'MDV', 'indicatif' => '+960'],
            ['nom' => 'Mali', 'code_iso_2' => 'ML', 'code_iso_3' => 'MLI', 'indicatif' => '+223'],
            ['nom' => 'Malte', 'code_iso_2' => 'MT', 'code_iso_3' => 'MLT', 'indicatif' => '+356'],
            ['nom' => 'Maroc', 'code_iso_2' => 'MA', 'code_iso_3' => 'MAR', 'indicatif' => '+212'],
            ['nom' => 'Maurice', 'code_iso_2' => 'MU', 'code_iso_3' => 'MUS', 'indicatif' => '+230'],
            ['nom' => 'Mauritanie', 'code_iso_2' => 'MR', 'code_iso_3' => 'MRT', 'indicatif' => '+222'],
            ['nom' => 'Mexique', 'code_iso_2' => 'MX', 'code_iso_3' => 'MEX', 'indicatif' => '+52'],
            ['nom' => 'Micronésie', 'code_iso_2' => 'FM', 'code_iso_3' => 'FSM', 'indicatif' => '+691'],
            ['nom' => 'Moldavie', 'code_iso_2' => 'MD', 'code_iso_3' => 'MDA', 'indicatif' => '+373'],
            ['nom' => 'Monaco', 'code_iso_2' => 'MC', 'code_iso_3' => 'MCO', 'indicatif' => '+377'],
            ['nom' => 'Mongolie', 'code_iso_2' => 'MN', 'code_iso_3' => 'MNG', 'indicatif' => '+976'],
            ['nom' => 'Monténégro', 'code_iso_2' => 'ME', 'code_iso_3' => 'MNE', 'indicatif' => '+382'],
            ['nom' => 'Mozambique', 'code_iso_2' => 'MZ', 'code_iso_3' => 'MOZ', 'indicatif' => '+258'],

            // N
            ['nom' => 'Namibie', 'code_iso_2' => 'NA', 'code_iso_3' => 'NAM', 'indicatif' => '+264'],
            ['nom' => 'Nauru', 'code_iso_2' => 'NR', 'code_iso_3' => 'NRU', 'indicatif' => '+674'],
            ['nom' => 'Népal', 'code_iso_2' => 'NP', 'code_iso_3' => 'NPL', 'indicatif' => '+977'],
            ['nom' => 'Nicaragua', 'code_iso_2' => 'NI', 'code_iso_3' => 'NIC', 'indicatif' => '+505'],
            ['nom' => 'Niger', 'code_iso_2' => 'NE', 'code_iso_3' => 'NER', 'indicatif' => '+227'],
            ['nom' => 'Nigeria', 'code_iso_2' => 'NG', 'code_iso_3' => 'NGA', 'indicatif' => '+234'],
            ['nom' => 'Norvège', 'code_iso_2' => 'NO', 'code_iso_3' => 'NOR', 'indicatif' => '+47'],
            ['nom' => 'Nouvelle-Zélande', 'code_iso_2' => 'NZ', 'code_iso_3' => 'NZL', 'indicatif' => '+64'],

            // O
            ['nom' => 'Oman', 'code_iso_2' => 'OM', 'code_iso_3' => 'OMN', 'indicatif' => '+968'],
            ['nom' => 'Ouganda', 'code_iso_2' => 'UG', 'code_iso_3' => 'UGA', 'indicatif' => '+256'],
            ['nom' => 'Ouzbékistan', 'code_iso_2' => 'UZ', 'code_iso_3' => 'UZB', 'indicatif' => '+998'],

            // P
            ['nom' => 'Pakistan', 'code_iso_2' => 'PK', 'code_iso_3' => 'PAK', 'indicatif' => '+92'],
            ['nom' => 'Palaos', 'code_iso_2' => 'PW', 'code_iso_3' => 'PLW', 'indicatif' => '+680'],
            ['nom' => 'Palestine', 'code_iso_2' => 'PS', 'code_iso_3' => 'PSE', 'indicatif' => '+970'],
            ['nom' => 'Panama', 'code_iso_2' => 'PA', 'code_iso_3' => 'PAN', 'indicatif' => '+507'],
            ['nom' => 'Papouasie-Nouvelle-Guinée', 'code_iso_2' => 'PG', 'code_iso_3' => 'PNG', 'indicatif' => '+675'],
            ['nom' => 'Paraguay', 'code_iso_2' => 'PY', 'code_iso_3' => 'PRY', 'indicatif' => '+595'],
            ['nom' => 'Pays-Bas', 'code_iso_2' => 'NL', 'code_iso_3' => 'NLD', 'indicatif' => '+31'],
            ['nom' => 'Pérou', 'code_iso_2' => 'PE', 'code_iso_3' => 'PER', 'indicatif' => '+51'],
            ['nom' => 'Philippines', 'code_iso_2' => 'PH', 'code_iso_3' => 'PHL', 'indicatif' => '+63'],
            ['nom' => 'Pologne', 'code_iso_2' => 'PL', 'code_iso_3' => 'POL', 'indicatif' => '+48'],
            ['nom' => 'Portugal', 'code_iso_2' => 'PT', 'code_iso_3' => 'PRT', 'indicatif' => '+351'],

            // Q
            ['nom' => 'Qatar', 'code_iso_2' => 'QA', 'code_iso_3' => 'QAT', 'indicatif' => '+974'],

            // R
            ['nom' => 'République Dominicaine', 'code_iso_2' => 'DO', 'code_iso_3' => 'DOM', 'indicatif' => '+1-809'],
            ['nom' => 'République du Congo', 'code_iso_2' => 'CG', 'code_iso_3' => 'COG', 'indicatif' => '+242'],
            ['nom' => 'République Démocratique du Congo', 'code_iso_2' => 'CD', 'code_iso_3' => 'COD', 'indicatif' => '+243'],
            ['nom' => 'République Tchèque', 'code_iso_2' => 'CZ', 'code_iso_3' => 'CZE', 'indicatif' => '+420'],
            ['nom' => 'Roumanie', 'code_iso_2' => 'RO', 'code_iso_3' => 'ROU', 'indicatif' => '+40'],
            ['nom' => 'Royaume-Uni', 'code_iso_2' => 'GB', 'code_iso_3' => 'GBR', 'indicatif' => '+44'],
            ['nom' => 'Russie', 'code_iso_2' => 'RU', 'code_iso_3' => 'RUS', 'indicatif' => '+7'],
            ['nom' => 'Rwanda', 'code_iso_2' => 'RW', 'code_iso_3' => 'RWA', 'indicatif' => '+250'],

            // S
            ['nom' => 'Saint-Kitts-et-Nevis', 'code_iso_2' => 'KN', 'code_iso_3' => 'KNA', 'indicatif' => '+1-869'],
            ['nom' => 'Saint-Vincent-et-les-Grenadines', 'code_iso_2' => 'VC', 'code_iso_3' => 'VCT', 'indicatif' => '+1-784'],
            ['nom' => 'Sainte-Lucie', 'code_iso_2' => 'LC', 'code_iso_3' => 'LCA', 'indicatif' => '+1-758'],
            ['nom' => 'Salomon', 'code_iso_2' => 'SB', 'code_iso_3' => 'SLB', 'indicatif' => '+677'],
            ['nom' => 'Salvador', 'code_iso_2' => 'SV', 'code_iso_3' => 'SLV', 'indicatif' => '+503'],
            ['nom' => 'Samoa', 'code_iso_2' => 'WS', 'code_iso_3' => 'WSM', 'indicatif' => '+685'],
            ['nom' => 'São Tomé-et-Príncipe', 'code_iso_2' => 'ST', 'code_iso_3' => 'STP', 'indicatif' => '+239'],
            ['nom' => 'Sénégal', 'code_iso_2' => 'SN', 'code_iso_3' => 'SEN', 'indicatif' => '+221'],
            ['nom' => 'Serbie', 'code_iso_2' => 'RS', 'code_iso_3' => 'SRB', 'indicatif' => '+381'],
            ['nom' => 'Seychelles', 'code_iso_2' => 'SC', 'code_iso_3' => 'SYC', 'indicatif' => '+248'],
            ['nom' => 'Sierra Leone', 'code_iso_2' => 'SL', 'code_iso_3' => 'SLE', 'indicatif' => '+232'],
            ['nom' => 'Singapour', 'code_iso_2' => 'SG', 'code_iso_3' => 'SGP', 'indicatif' => '+65'],
            ['nom' => 'Slovaquie', 'code_iso_2' => 'SK', 'code_iso_3' => 'SVK', 'indicatif' => '+421'],
            ['nom' => 'Slovénie', 'code_iso_2' => 'SI', 'code_iso_3' => 'SVN', 'indicatif' => '+386'],
            ['nom' => 'Somalie', 'code_iso_2' => 'SO', 'code_iso_3' => 'SOM', 'indicatif' => '+252'],
            ['nom' => 'Soudan', 'code_iso_2' => 'SD', 'code_iso_3' => 'SDN', 'indicatif' => '+249'],
            ['nom' => 'Soudan du Sud', 'code_iso_2' => 'SS', 'code_iso_3' => 'SSD', 'indicatif' => '+211'],
            ['nom' => 'Sri Lanka', 'code_iso_2' => 'LK', 'code_iso_3' => 'LKA', 'indicatif' => '+94'],
            ['nom' => 'Suède', 'code_iso_2' => 'SE', 'code_iso_3' => 'SWE', 'indicatif' => '+46'],
            ['nom' => 'Suisse', 'code_iso_2' => 'CH', 'code_iso_3' => 'CHE', 'indicatif' => '+41'],
            ['nom' => 'Suriname', 'code_iso_2' => 'SR', 'code_iso_3' => 'SUR', 'indicatif' => '+597'],
            ['nom' => 'Syrie', 'code_iso_2' => 'SY', 'code_iso_3' => 'SYR', 'indicatif' => '+963'],

            // T
            ['nom' => 'Tadjikistan', 'code_iso_2' => 'TJ', 'code_iso_3' => 'TJK', 'indicatif' => '+992'],
            ['nom' => 'Tanzanie', 'code_iso_2' => 'TZ', 'code_iso_3' => 'TZA', 'indicatif' => '+255'],
            ['nom' => 'Tchad', 'code_iso_2' => 'TD', 'code_iso_3' => 'TCD', 'indicatif' => '+235'],
            ['nom' => 'Thaïlande', 'code_iso_2' => 'TH', 'code_iso_3' => 'THA', 'indicatif' => '+66'],
            ['nom' => 'Timor oriental', 'code_iso_2' => 'TL', 'code_iso_3' => 'TLS', 'indicatif' => '+670'],
            ['nom' => 'Togo', 'code_iso_2' => 'TG', 'code_iso_3' => 'TGO', 'indicatif' => '+228'],
            ['nom' => 'Tonga', 'code_iso_2' => 'TO', 'code_iso_3' => 'TON', 'indicatif' => '+676'],
            ['nom' => 'Trinité-et-Tobago', 'code_iso_2' => 'TT', 'code_iso_3' => 'TTO', 'indicatif' => '+1-868'],
            ['nom' => 'Tunisie', 'code_iso_2' => 'TN', 'code_iso_3' => 'TUN', 'indicatif' => '+216'],
            ['nom' => 'Turkménistan', 'code_iso_2' => 'TM', 'code_iso_3' => 'TKM', 'indicatif' => '+993'],
            ['nom' => 'Turquie', 'code_iso_2' => 'TR', 'code_iso_3' => 'TUR', 'indicatif' => '+90'],
            ['nom' => 'Tuvalu', 'code_iso_2' => 'TV', 'code_iso_3' => 'TUV', 'indicatif' => '+688'],

            // U
            ['nom' => 'Ukraine', 'code_iso_2' => 'UA', 'code_iso_3' => 'UKR', 'indicatif' => '+380'],
            ['nom' => 'Uruguay', 'code_iso_2' => 'UY', 'code_iso_3' => 'URY', 'indicatif' => '+598'],

            // V
            ['nom' => 'Vanuatu', 'code_iso_2' => 'VU', 'code_iso_3' => 'VUT', 'indicatif' => '+678'],
            ['nom' => 'Vatican', 'code_iso_2' => 'VA', 'code_iso_3' => 'VAT', 'indicatif' => '+39-06'],
            ['nom' => 'Venezuela', 'code_iso_2' => 'VE', 'code_iso_3' => 'VEN', 'indicatif' => '+58'],
            ['nom' => 'Viêt Nam', 'code_iso_2' => 'VN', 'code_iso_3' => 'VNM', 'indicatif' => '+84'],

            // Y
            ['nom' => 'Yémen', 'code_iso_2' => 'YE', 'code_iso_3' => 'YEM', 'indicatif' => '+967'],

            // Z
            ['nom' => 'Zambie', 'code_iso_2' => 'ZM', 'code_iso_3' => 'ZMB', 'indicatif' => '+260'],
            ['nom' => 'Zimbabwe', 'code_iso_2' => 'ZW', 'code_iso_3' => 'ZWE', 'indicatif' => '+263'],
        ];

        $now = now();

        foreach ($pays as $p) {
            DB::table('pays')->updateOrInsert(
                ['code_iso_2' => $p['code_iso_2']], // Clé unique pour éviter les doublons
                [
                    'id' => Str::uuid(),
                    'nom' => $p['nom'],
                    'code_iso_2' => $p['code_iso_2'],
                    'code_iso_3' => $p['code_iso_3'],
                    'indicatif' => $p['indicatif'],
                    'actif' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $this->command->info('✅ ' . count($pays) . ' pays ont été enregistrés avec succès.');
    }
}
