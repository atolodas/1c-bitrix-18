<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

Loc::loadMessages(__FILE__);
$saleStatusList = [];
$arFilter = array();
$arFilter["LID"] = LANGUAGE_ID;
$results = \CSaleStatus::GetList(
    array($by => $order),
    $arFilter,
    false,
    false,
    array('ID', 'SORT', 'TYPE', 'NOTIFY', 'LID', 'COLOR' ,'NAME', 'DESCRIPTION', $by)
);
$dbResultList = new \CAdminUiResult($results, 'tbl_sale_status');
$dbResultList->NavStart();
while($result = $dbResultList->Fetch()){
    $saleStatusList[$result['ID']] = $result['NAME'];
}

$data = array(
	'NAME' => Loc::getMessage('SALE_HPS_INVOICEBOX_TITLE'),
	'SORT' => 500,
	'CODES' => array(
		'INVOICEBOX_PARTICIPANT_ID' => array(
			'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_PARTICIPANT_ID'),
			'SORT' 	=> 100,
			'GROUP' => 'PAYMENT',
		),
		'INVOICEBOX_PARTICIPANT_IDENT' => array(
			'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_PARTICIPANT_IDENT'),
			'SORT' 	=> 100,
			'GROUP' => 'PAYMENT',
		),
		'INVOICEBOX_PARTICIPANT_APIKEY' => array(
			'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_PARTICIPANT_APIKEY'),
			'SORT' 	=> 200,
			'GROUP' => 'PAYMENT',
		),
		'INVOICEBOX_MEASURE_DEFAULT' => array(
			'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_MEASURE_DEFAULT'),
			'SORT' 	=> 100,
			'GROUP' => 'PAYMENT',
		),
		'INVOICEBOX_ORDERDESCR' => array(
			'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_ORDERDESCR'),
			'SORT' 	=> 400,
			'GROUP' => 'PAYMENT',
		),
		'PAYMENT_ID' => array(
			'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_PAYMENT_ID'),
			'SORT' 	=> 700,
			'GROUP' => 'PAYMENT',
			'DEFAULT' => array(
				'PROVIDER_VALUE' => 'ID',
				'PROVIDER_KEY' => 'PAYMENT'
			)
		),
		'PAYMENT_SHOULD_PAY' => array(
			'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_SHOULD_PAY'),
			'SORT' 	=> 800,
			'GROUP' => 'PAYMENT',
			'DEFAULT' => array(
				'PROVIDER_VALUE' 	=> 'SUM',
				'PROVIDER_KEY' 		=> 'PAYMENT'
			)
		),
		'PAYMENT_CURRENCY' => array(
			'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_CURRENCY'),
			'SORT' 	=> 900,
			'GROUP' => 'PAYMENT',
			'DEFAULT' => array(
				'PROVIDER_VALUE' 	=> 'CURRENCY',
				'PROVIDER_KEY' 		=> 'PAYMENT'
			)
		),
		'PAYMENT_DATE_INSERT' => array(
			'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_DATE_INSERT'),
			'SORT' 	=> 1000,
			'GROUP' => 'PAYMENT',
			'DEFAULT' => array(
				'PROVIDER_VALUE' 	=> 'DATE_BILL',
				'PROVIDER_KEY' 		=> 'PAYMENT'
			)
		),
		'BUYER_PERSON_EMAIL' => array(
			'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_EMAIL_USER'),
			'SORT' 	=> 1100,
			'GROUP' => 'BUYER_PERSON',
			'DEFAULT' => array(
				'PROVIDER_VALUE' 	=> 'EMAIL',
				'PROVIDER_KEY' 		=> 'PROPERTY'
			)
		),
		'PS_CHANGE_STATUS_PAY' => array(
			'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_CHANGE_STATUS_PAY'),
			'SORT' 	=> 1200,
			'GROUP' => 'GENERAL_SETTINGS',
			"INPUT" => array(
				'TYPE' 			=> 'Y/N'
			)
		),
		'PS_IS_TEST' => array(
			'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_TESTMODE'),
			'SORT' 	=> 1300,
			'GROUP' => 'GENERAL_SETTINGS',
			"INPUT" => array(
				'TYPE' 			=> 'Y/N'
			)
		),
        'PS_IS_DEFFERED_PAYMENT' => array(
            'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_DEFFERED_PAYMENT'),
            'SORT' 	=> 1400,
            'GROUP' => 'PAYMENT',
            "INPUT" => array(
                'TYPE' => 'ENUM',
                'OPTIONS' =>$saleStatusList
            )
        ),
        'PS_STATUS_ORDER_AFTER_PAY' => array(
            'NAME' 	=> Loc::getMessage('SALE_HPS_INVOICEBOX_STATUS_ORDER_AFTER_PAY'),
            'SORT' 	=> 1500,
            'GROUP' => 'PAYMENT',
            "INPUT" => array(
                'TYPE' => 'ENUM',
                'OPTIONS' =>$saleStatusList
            )
        ),
	)
); //
