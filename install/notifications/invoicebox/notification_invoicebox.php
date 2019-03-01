<?php 

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/bx_root.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('sale');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$participantId 		= isset( $_REQUEST["participantId"] ) ? IntVal($_REQUEST["participantId"]) : false;
$participantOrderId 	= isset( $_REQUEST["participantOrderId"] ) ? htmlspecialcharsbx(trim($_REQUEST["participantOrderId"])) : false;

if ( $participantId && $participantOrderId )
{ 
	// ---------------------------------------------------------
	$ucode 		= trim($_REQUEST["ucode"]);
	$timetype 	= trim($_REQUEST["timetype"]);
	$time 		= str_replace(' ','+',trim($_REQUEST["time"]));
	$amount 	= trim($_REQUEST["amount"]);
	$currency 	= trim($_REQUEST["currency"]);
	$agentName 	= escape_win(trim($_REQUEST["agentName"]));
	$agentPointName = escape_win(trim($_REQUEST["agentPointName"]));
	$testMode 	= trim($_REQUEST["testMode"]);
	$sign	 	= trim($_REQUEST["sign"]);
	// ---------------------------------------------------------
	$bCorrectPayment = True;
	$arOrder = CSaleOrder::GetByID($participantOrderId);
	
	if (!($arOrder))
	{
	
		die( Loc::getMessage('SALE_HPS_INVOICEBOX_NOT_NUMBER') . $participantOrderId );
	} else
	{
	
		CSalePaySystemAction::InitParamArrays($arOrder, $participantOrderId);
		
		$participant_apikey 	=  CSalePaySystemAction::GetParamValue("INVOICEBOX_PARTICIPANT_APIKEY");

		$sign_strA = 
			$participantId .
			$participantOrderId .
			$ucode .
			$timetype .
			$time .
			$amount .
			$currency .
			$agentName .
			$agentPointName .
			$testMode .
			$participant_apikey;

		$sign_crcA = md5( $sign_strA ); //
		
		if ( strtolower($sign_crcA) != strtolower($sign) )
		{
		
			die( Loc::getMessage('SALE_HPS_INVOICEBOX_NOT_SIGN') );
		}; //

		$strPS_STATUS_DESCRIPTION 	= GetMessage('SALE_RES_NUMBER').": ".$participantOrderId . " (" . $ucode . ")";
		$strPS_STATUS_DESCRIPTION 	.= "; ".GetMessage('SALE_RES_DATEPAY').": ".date("d.m.Y H:i:s");
		$strPS_STATUS_DESCRIPTION 	.= "; ".GetMessage('SASP_RES_PAY_TYPE').": ".$agentName;
		$strPS_STATUS_MESSAGE 		= GetMessage('SASP_RES_PAYED');
		
		$arFields = array(
			"PS_STATUS" 		=> "Y",
			"PS_STATUS_CODE" 	=> "-",
			"PS_STATUS_DESCRIPTION" => $strPS_STATUS_DESCRIPTION,
			"PS_STATUS_MESSAGE" 	=> $strPS_STATUS_MESSAGE,
	 		"PS_SUM" 		=> $amount,
			"PS_CURRENCY" 		=> $arOrder["CURRENCY"],
			"PS_RESPONSE_DATE" 	=> Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG)))
		); //


		if (roundEx(($arOrder["PRICE"]-$arOrder["SUM_PAID"]), 2) == roundEx($amount, 2) )
		{  
			CSaleOrder::PayOrder($arOrder["ID"], "Y");
			CSaleOrder::StatusOrder($arOrder["ID"], "P");
		}else{
		
			die (Loc::getMessage('SALE_HPS_INVOICEBOX_NOT_SUM'));
			
		}; //
		
		$APPLICATION->RestartBuffer();
		if(CSaleOrder::Update($arOrder["ID"], $arFields))
		{
			
			echo "OK";
		}; //
	}; //
	die();
}; //if

die( Loc::getMessage('SALE_HPS_INVOICEBOX_NOT_DATA') );

function escape_win($path)
{


	$path = strtoupper ($path); 
	$a =  strtr($path, 
	array(
	"\U0430"=>Loc::getMessage('U0430'),
	"\U0431"=>Loc::getMessage('U0431'),
	"\U0432"=>Loc::getMessage('U0432'), 
	"\U0433"=>Loc::getMessage('U0433'),
	"\U0434"=>Loc::getMessage('U0434'),
	"\U0435"=>Loc::getMessage('U0435'),
	"\U0451"=>Loc::getMessage('U0451'),
	"\U0436"=>Loc::getMessage('U0436'),
	"\U0437"=>Loc::getMessage('U0437'),
	"\U0438"=>Loc::getMessage('U0438'),
	"\U0439"=>Loc::getMessage('U0439'),
	"\U043A"=>Loc::getMessage('U043A'),
	"\U043B"=>Loc::getMessage('U043B'),
	"\U043C"=>Loc::getMessage('U043C'),
	"\U043D"=>Loc::getMessage('U043D'),
	"\U043E"=>Loc::getMessage('U043E'),
	"\U043F"=>Loc::getMessage('U043F'),
	"\U0440"=>Loc::getMessage('U0440'),
	"\U0441"=>Loc::getMessage('U0441'),
	"\U0442"=>Loc::getMessage('U0442'),
	"\U0443"=>Loc::getMessage('U0443'),
	"\U0444"=>Loc::getMessage('U0444'),
	"\U0445"=>Loc::getMessage('U0445'),
	"\U0446"=>Loc::getMessage('U0446'),
	"\U0447"=>Loc::getMessage('U0447'),
	"\U0448"=>Loc::getMessage('U0448'),
	"\U0449"=>Loc::getMessage('U0449'),
	"\U044A"=>Loc::getMessage('U044A'),
	"\U044B"=>Loc::getMessage('U044B'),
	"\U044C"=>Loc::getMessage('U044C'),
	"\U044D"=>Loc::getMessage('U044D'),
	"\U044E"=>Loc::getMessage('U044E'),
	"\U044F"=>Loc::getMessage('U044F'),
	"\U0410"=>Loc::getMessage('U0410'),
	"\U0411"=>Loc::getMessage('U0411'),
	"\U0412"=>Loc::getMessage('U0412'),
	"\U0413"=>Loc::getMessage('U0413'),
	"\U0414"=>Loc::getMessage('U0414'), 
	"\U0415"=>Loc::getMessage('U0415'),
	"\U0401"=>Loc::getMessage('U0401'),
	"\U0416"=>Loc::getMessage('U0416'),
	"\U0417"=>Loc::getMessage('U0417'),
	"\U0418"=>Loc::getMessage('U0418'),
	"\U0419"=>Loc::getMessage('U0419'),
	"\U041A"=>Loc::getMessage('U041A'), 
	"\U041B"=>Loc::getMessage('U041B'),
	"\U041C"=>Loc::getMessage('U041C'),
	"\U041D"=>Loc::getMessage('U041D'),
	"\U041E"=>Loc::getMessage('U041E'),
	"\U041F"=>Loc::getMessage('U041F'),
	"\U0420"=>Loc::getMessage('U0420'),
	"\U0421"=>Loc::getMessage('U0421'), 
	"\U0422"=>Loc::getMessage('U0422'),
	"\U0423"=>Loc::getMessage('U0423'),
	"\U0424"=>Loc::getMessage('U0424'),
	"\U0425"=>Loc::getMessage('U0425'),
	"\U0426"=>Loc::getMessage('U0426'),
	"\U0427"=>Loc::getMessage('U0427'),
	"\U0428"=>Loc::getMessage('U0428'), 
	"\U0429"=>Loc::getMessage('U0429'),
	"\U042A"=>Loc::getMessage('U042A'),
	"\U042B"=>Loc::getMessage('U042B'),
	"\U042C"=>Loc::getMessage('U042C'),
	"\U042D"=>Loc::getMessage('U042D'),
	"\U042E"=>Loc::getMessage('U042E'),
	"\U042F"=>Loc::getMessage('U042F'))); 
	$a = strtr($a, array('\\'=>'"'));
	
	return $a;
}; //func