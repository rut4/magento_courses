<?php
/**
 * Oggetto Web shipping extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto Shipping module to newer versions in the future.
 * If you wish to customize the Oggetto Shipping module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_Shipping
 * @copyright  Copyright (C) 2012 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;

$regions = [
    'RU-AD'     => 'Республика Адыгея',
    'RU-AL'     => 'Республика Алтай',
    'RU-BA'     => 'Республика Башкортостан',
    'RU-BU'     => 'Республика Бурятия',
    'RU-DA'     => 'Республика Дагестан',
    'RU-IN'     => 'Республика Ингушетия',
    'RU-KB'     => 'Кабардино-Балкарская республика',
    'RU-KL'     => 'Республика Калмыкия',
    'RU-KC'     => 'Карачаево-Черкесская республика',
    'RU-KR'     => 'Республика Карелия',
    'RU-KO'     => 'Республика Коми',
    'UA-43'     => 'Республика Крым',
    'RU-ME'     => 'Республика Марий Эл',
    'RU-MO'     => 'Республика Мордовия',
    'RU-SA'     => 'Республика Саха (Якутия)',
    'RU-SE'     => 'Республика Северная Осетия — Алания',
    'RU-TA'     => 'Республика Татарстан',
    'RU-TY'     => 'Республика Тыва',
    'RU-UD'     => 'Удмуртская республика',
    'RU-KK'     => 'Республика Хакасия',
    'RU-CE'     => 'Чеченская республика',
    'RU-CU'     => 'Чувашская республика',
    'RU-ALT'    => 'Алтайский край',
    'RU-ZAB'    => 'Забайкальский край',
    'RU-KAM'    => 'Камчатский край',
    'RU-KDA'    => 'Краснодарский край',
    'RU-KYA'    => 'Красноярский край',
    'RU-PER'    => 'Пермский край',
    'RU-PRI'    => 'Приморский край',
    'RU-STA'    => 'Ставропольский край',
    'RU-KHA'    => 'Хабаровский край',
    'RU-AMU'    => 'Амурская область',
    'RU-ARK'    => 'Архангельская область',
    'RU-AST'    => 'Астраханская область',
    'RU-BEL'    => 'Белгородская область',
    'RU-BRY'    => 'Брянская область',
    'RU-VLA'    => 'Владимирская область',
    'RU-VGG'    => 'Волгоградская область',
    'RU-VLG'    => 'Вологодская область',
    'RU-VOR'    => 'Воронежская область',
    'RU-IVA'    => 'Ивановская область',
    'RU-IRK'    => 'Иркутская область',
    'RU-KGD'    => 'Калининградская область',
    'RU-KLU'    => 'Калужская область',
    'RU-KEM'    => 'Кемеровская область',
    'RU-KIR'    => 'Кировская область',
    'RU-KOS'    => 'Костромская область',
    'RU-KGN'    => 'Курганская область',
    'RU-KRS'    => 'Курская область',
    'RU-LEN'    => 'Ленинградская область',
    'RU-LIP'    => 'Липецкая область',
    'RU-MAG'    => 'Магаданская область',
    'RU-MOS'    => 'Московская область',
    'RU-MUR'    => 'Мурманская область',
    'RU-NIZ'    => 'Нижегородская область',
    'RU-NGR'    => 'Новгородская область',
    'RU-NVS'    => 'Новосибирская область',
    'RU-OMS'    => 'Омская область',
    'RU-ORE'    => 'Оренбургская область',
    'RU-ORL'    => 'Орловская область',
    'RU-PNZ'    => 'Пензенская область',
    'RU-PSK'    => 'Псковская область',
    'RU-ROS'    => 'Ростовская область',
    'RU-RYA'    => 'Рязанская область',
    'RU-SAM'    => 'Самарская область',
    'RU-SAR'    => 'Саратовская область',
    'RU-SAK'    => 'Сахалинская область',
    'RU-SVE'    => 'Свердловская область',
    'RU-SMO'    => 'Смоленская область',
    'RU-TAM'    => 'Тамбовская область',
    'RU-TVE'    => 'Тверская область',
    'RU-TOM'    => 'Томская область',
    'RU-TUL'    => 'Тульская область',
    'RU-TYU'    => 'Тюменская область',
    'RU-ULY'    => 'Ульяновская область',
    'RU-CHE'    => 'Челябинская область',
    'RU-YAR'    => 'Ярославская область',
    'RU-MOW'    => 'Москва',
    'RU-SPE'    => 'Санкт-Петербург',
    'UA-40'     => 'Севастополь',
    'RU-YEV'    => 'Еврейская автономная область',
    'RU-NEN'    => 'Ненецкий автономный округ',
    'RU-KHM'    => 'Ханты-Мансийский автономный округ - Югра',
    'RU-CHU'    => 'Чукотский автономный округ',
    'RU-YAN'    => 'Ямало-Ненецкий автономный округ'
];

try {
    foreach ($regions as $code => $name) {
        Mage::getModel('directory/region')->setData([
            'country_id' => 'RU',
            'code' => $code,
            'default_name' => $name
        ])
            ->save();
    }
} catch (Exception $e) {
    Mage::logException($e);
}
