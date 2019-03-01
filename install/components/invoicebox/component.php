<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Application;
use Bitrix\Sale;
use Bitrix\Sale\PriceMaths;
use Bitrix\Main\Localization\Loc;


\CModule::IncludeModule('sale');
global $APPLICATION;


////////////////////////////
//проверяем ip адрес запроса
$legal_ip = [
    '77.244.212.7',
    '77.244.212.8',
    '77.244.212.9'
];
if (!in_array($_SERVER['REMOTE_ADDR'], $legal_ip)) {
    CEventLog::Add([
        'SEVERITY' => 'ERROR',
        'AUDIT_TYPE_ID' => 'INVOICE_PAYMENT_LOG',
        'MODULE_ID' => 'invoicebox.payment',
        'DESCRIPTION' => json_encode([
            'ip_server' => $_SERVER['REMOTE_ADDR'],
            'error' => Loc::getMessage('SALE_HPS_INVOICEBOX_LOG_FORBIDDEN_SERVER')
        ], true),
    ]);
    exit('error: 403 forbidden');
}
////////////////////

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

////////////////////

//проверяем наличие ключа в настройках
$options = \CSalePaySystemAction::GetList([], ['ACTION_FILE' => 'invoicebox'])->Fetch();
if (!$options || !isset($options['PARAMS'])) {
    CEventLog::Add([
        'SEVERITY' => 'INFO',
        'AUDIT_TYPE_ID' => 'INVOICE_PAYMENT_LOG',
        'MODULE_ID' => 'invoicebox.payment',
        'DESCRIPTION' => json_encode([
            'error' => Loc::getMessage('SALE_HPS_INVOICEBOX_LOG_MODULE_IS_NOT_CONF')
        ], true),
    ]);
    exit('error: module is not configured');
}
$params = unserialize($options['PARAMS']);
if (empty($params['INVOICEBOX_PARTICIPANT_APIKEY']) || empty($params['INVOICEBOX_PARTICIPANT_APIKEY']['VALUE'])) {
    CEventLog::Add([
        'SEVERITY' => 'INFO',
        'AUDIT_TYPE_ID' => 'INVOICE_PAYMENT_LOG',
        'MODULE_ID' => 'invoicebox.payment',
        'DESCRIPTION' => json_encode([
            'error' => Loc::getMessage('SALE_HPS_INVOICEBOX_LOG_MODULE_IS_NOT_SET_API_KEY')
        ], true),
    ]);
    exit('error: the apikey is not set in the module settings');
}
////////////////////

///////////////////
//проверка типа запроса
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    CEventLog::Add([
        'SEVERITY' => 'INFO',
        'AUDIT_TYPE_ID' => 'INVOICE_PAYMENT_LOG',
        'MODULE_ID' => 'invoicebox.payment',
        'DESCRIPTION' => json_encode([
            'error' => Loc::getMessage('SALE_HPS_INVOICEBOX_LOG_MODULE_IS_ERROR_REQUEST_TYPE')
        ], true),
    ]);
    echo 'error: request type is invalid';
}
//////////////////

/////////////////
//проверка ключа и хеша запроса
$sign_strA =
    $request->get("participantId") .
    $request->get("participantOrderId") .
    $request->get("ucode") .
    $request->get("timetype") .
    $request->get("time") .
    $request->get("amount") .
    $request->get("currency") .
    $request->get("agentName") .
    $request->get("agentPointName") .
    $request->get("testMode") .
    $params['INVOICEBOX_PARTICIPANT_APIKEY']['VALUE'];

$sign_crcA = md5( $sign_strA ); //
if(ToUpper($sign_crcA) != ToUpper($request->get('sign'))){
    CEventLog::Add([
        'SEVERITY' => 'INFO',
        'AUDIT_TYPE_ID' => 'INVOICE_PAYMENT_LOG',
        'MODULE_ID' => 'invoicebox.payment',
        'DESCRIPTION' => json_encode([
            'error' => Loc::getMessage('SALE_HPS_INVOICEBOX_LOG_MODULE_IS_ERROR_API_KEY_IN_REQUEST')
        ], true),
    ]);
    exit('error: key is not correct');
}
/////////////////

/////////////////
//тестовый запрос
if ($request->get('id') == -1
    && $request->get('ucode') == '00000-00000-00000-0000'
    && $request->get('participant_order_id') == 'TEST'
    && $request->get('testmode') == 'y'
    && $request->get('organization_name') == 'TEST'
    && $request->get('participant_amount') == '100.00'
    && $request->get('point_name') == 'TEST'
) {
    CEventLog::Add([
        'SEVERITY' => 'INFO',
        'AUDIT_TYPE_ID' => 'INVOICE_PAYMENT_LOG',
        'MODULE_ID' => 'invoicebox.payment',
        'DESCRIPTION' => json_encode([
            'success' => Loc::getMessage('SALE_HPS_INVOICEBOX_LOG_MODULE_IS_TEST_SUCCESS')
        ], true),
    ]);
    exit('OK');
}
////////////////

////////////////
//проверяем идентификатор магазина
$participantId = isset($params['INVOICEBOX_PARTICIPANT_ID']) && !empty($params['INVOICEBOX_PARTICIPANT_ID']['VALUE']) ? $params['INVOICEBOX_PARTICIPANT_ID']['VALUE'] : null;
if ($request->get('participantId') != $participantId){
    CEventLog::Add([
        'SEVERITY' => 'INFO',
        'AUDIT_TYPE_ID' => 'INVOICE_PAYMENT_LOG',
        'MODULE_ID' => 'invoicebox.payment',
        'DESCRIPTION' => json_encode([
            'error' => Loc::getMessage('SALE_HPS_INVOICEBOX_LOG_REQUEST_PARTICIPANT_IS_NOT_VALID')
        ], true),
    ]);
    exit('error: incorrect participantId');
}
///////////////
/// работа с заказом
$order = Sale\Order::load($request->get('participantOrderId'));
if (!$order) {
    CEventLog::Add([
        'SEVERITY' => 'INFO',
        'AUDIT_TYPE_ID' => 'INVOICE_PAYMENT_LOG',
        'MODULE_ID' => 'invoicebox.payment',
        'DESCRIPTION' => json_encode([
            'error' => Loc::getMessage('SALE_HPS_INVOICEBOX_LOG_REQUEST_ORDER_IS_NOT_VALID')
        ], true),
    ]);
    exit('error: order not found');
}

//сверяем сумму оплаты
$price = number_format($order->getPrice(), 2, '.', '');
if ($price != $request->get('amount')) {
    CEventLog::Add([
        'SEVERITY' => 'INFO',
        'AUDIT_TYPE_ID' => 'INVOICE_PAYMENT_LOG',
        'MODULE_ID' => 'invoicebox.payment',
        'DESCRIPTION' => json_encode([
            'error' => Loc::getMessage('SALE_HPS_INVOICEBOX_LOG_REQUEST_AMOUNT_IS_NOT_VALID')
        ], true),
    ]);
    exit('error: amount does not match');
}
//ставим оплату в заказе
$paymentCollection = $order->getPaymentCollection();
foreach ($paymentCollection as $payment) {
    $payment->setPaid('Y');
}
$statusAfterPay = isset($params['PS_STATUS_ORDER_AFTER_PAY']) && !empty($params['PS_STATUS_ORDER_AFTER_PAY']['VALUE']) ? $params['PS_STATUS_ORDER_AFTER_PAY']['VALUE'] : null;
if ($statusAfterPay) {
    $order->setField('STATUS_ID', $statusAfterPay);
}
$order->save();

CEventLog::Add([
    'SEVERITY' => 'INFO',
    'AUDIT_TYPE_ID' => 'INVOICE_PAYMENT_LOG',
    'MODULE_ID' => 'invoicebox.payment',
    'DESCRIPTION' => json_encode([
        'order_id' => $order->get('id'),
        'success' => Loc::getMessage('SALE_HPS_INVOICEBOX_LOG_ORDER_IS_PAY')
    ], true),
]);
exit('OK');
