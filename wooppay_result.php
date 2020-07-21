<?
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2012-2015 Wooppay
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @copyright   Copyright (c) 2012-2015 Wooppay
 * @author      Yaroshenko Vladimir <mr.struct@mail.ru>
 * @version     1.0
 */

ini_set('display_errors', 1);
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("sale"))
	die('Can\'t include module "Sale"');

if (empty($_GET['orderId']) || empty($_GET['key']) || md5($_GET['orderId']) != $_GET['key']) {
	exit;
}
if (!class_exists('WooppaySoapClient')) {
	require('WooppaySoapClient.php');
}
$order = CSaleOrder::GetByID($_GET['orderId']);
if ($order && $order['PAYED'] != 'Y') {
	CSalePaySystemAction::InitParamArrays($order, $order["ID"]);
	$client = new WooppaySoapClient(CSalePaySystemAction::GetParamValue('URL'));
	$login_request = new CoreLoginRequest();
	$login_request->username = CSalePaySystemAction::GetParamValue('LOGIN');
	$login_request->password = CSalePaySystemAction::GetParamValue('PASS');
	try {
		if ($client->login($login_request)) {
			$prefix = trim(CSalePaySystemAction::GetParamValue('ORDER_PREFIX'));
			$invoice_request = new CashCreateInvoiceByServiceRequest();
			$invoice_request->referenceId = $prefix . $order['ID'];
			$invoice_request->backUrl = '';
			$invoice_request->serviceName = CSalePaySystemAction::GetParamValue('SERVICE');
			$invoice_request->serviceName='';
			$invoice_request->requestUrl = '';
			$invoice_request->addInfo = '';
			$invoice_request->amount = (float)$order['PRICE'];
			$invoice_request->deathDate = '';
			$invoice_request->description = '';
			$invoice_data = $client->createInvoice($invoice_request);

			$wooppay_operation_id = $invoice_data->response->operationId;
			$operationdata_request = new CashGetOperationDataRequest();
			$operationdata_request->operationId = array($wooppay_operation_id);
			$operation_data = $client->getOperationData($operationdata_request);
			if (!isset($operation_data->response->records[0]->status) || empty($operation_data->response->records[0]->status)) {
				exit;
			}

			if ($operation_data->response->records[0]->status == WooppayOperationStatus::OPERATION_STATUS_DONE) {
				if (CSaleOrder::PayOrder($order['ID'], 'Y', true, true)) {
					echo '{"data":1}';
					exit;
				}
			}
		}
	} catch (Exception $e) {
		exit;
	}
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
?>