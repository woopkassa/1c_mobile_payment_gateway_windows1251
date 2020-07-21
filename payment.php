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

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

include(GetLangFileName(dirname(__FILE__) . "/", "/wooppay.php"));
if (!class_exists('WooppaySoapClient')) {
	require('WooppaySoapClient.php');
}
$orderID = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
$order = CSaleOrder::GetByID($orderID);

if ($order && $order['PAYED'] == 'N' && !isset($_POST['WOOPPAY_PAY'])) {
	$client = new WooppaySoapClient(CSalePaySystemAction::GetParamValue('URL'));
	$login_request = new CoreLoginRequest();
	$login_request->username = CSalePaySystemAction::GetParamValue('LOGIN');
	$login_request->password = CSalePaySystemAction::GetParamValue('PASS');
	if ($client->login($login_request)) {
		if ($_POST['WOOPPAY_PHONE'] != null) {
			$phone = $_POST['WOOPPAY_PHONE'];
		} else {
			$phone = $GLOBALS['SALE_INPUT_PARAMS']['PROPERTY']['PHONE'];
		}
		$operator_request = new CoreGetMobileOperatorRequest();
		$operator_request->phone = $phone;
		$operator = $client->checkOperator($operator_request);
		$operator = $operator->response->operator;
		if ($operator == 'beeline' || $operator == 'activ' || $operator == 'kcell') {
			try {
				$phone = substr($phone, 1);
				$code_request = new CoreRequestConfirmationCodeRequest();
				$code_request->phone = $phone;
				$client->requestConfirmationCode($code_request);
				?>
				<form method="post" action="<?= POST_FORM_ACTION_URI ?>">
					<?php if (isset($_POST['WOOPPAY_PHONE'])) { ?>
						<input type="hidden" name="WOOPPAY_PHONE" value="<?= $_POST['WOOPPAY_PHONE'] ?>">
					<?php } ?>
					<div style="display:block">
						<h4>Код подтверждения:</h4>
						<input type="text" maxlength="6" name="WOOPPAY_CODE">
					</div>
					<div style="display:block; margin-top: 20px;float: left!important;">
						<input type="submit" name="WOOPPAY_PAY" value="Оплатить"
							   class="pull-right btn btn-default btn-lg"/>
					</div>
				</form>
				<?php
			} catch (Exception $e) {
				if (strpos($e->getMessage(), 222) != false) {
					echo '<div><h4 style="color: red;">Вы уже запрашивали код подтверждения на данный номер в течение предыдущих 5 минут. Попробуйте позже.</h4></div>';
					?>
					<form method="post" action="<?= POST_FORM_ACTION_URI ?>">
						<div style="display:block">
							<h4>Введите номер телефона для оплаты:</h4>
							<input type="text" name="WOOPPAY_PHONE">
						</div>
						<div style="display:block; margin-top: 20px;float: left!important;">
							<input type="submit" value="Отправить"
								   class="pull-right btn btn-default btn-lg"/>
						</div>
					</form>
					<?php
				}
			}
		} else {
			echo '<div><h4 style="color: red;">Недопустимый сотовый оператор для оплаты с мобильного телефона. Допустимые операторы Activ, Kcell, Beeline.</h4></div>';
			?>
			<form method="post" action="<?= POST_FORM_ACTION_URI ?>">
				<div style="display:block">
					<h4>Введите номер телефона для оплаты:</h4>
					<input type="text" name="WOOPPAY_PHONE">
				</div>
				<div style="display:block; margin-top: 20px;float: left!important;">
					<input type="submit" value="Отправить"
						   class="pull-right btn btn-default btn-lg"/>
				</div>
			</form>
			<?php
		}
	}
	?>
<?php } else if ($order && $order['PAYED'] != 'Y') {
	$client = new WooppaySoapClient(CSalePaySystemAction::GetParamValue('URL'));
	$login_request = new CoreLoginRequest();
	$login_request->username = CSalePaySystemAction::GetParamValue('LOGIN');
	$login_request->password = CSalePaySystemAction::GetParamValue('PASS');
	if (isset($_POST['WOOPPAY_PHONE'])) {
		$phone = $_POST['WOOPPAY_PHONE'];
	} else {
		$phone = $GLOBALS['SALE_INPUT_PARAMS']['PROPERTY']['PHONE'];
	}
	try {
		if ($client->login($login_request)) {
			$prefix = trim(CSalePaySystemAction::GetParamValue('ORDER_PREFIX'));
			$invoice_request = new CashCreateInvoiceByServiceRequest();
			$invoice_request->serviceName = CSalePaySystemAction::GetParamValue('SERVICE');
			$invoice_request->referenceId = $prefix . $order['ID'];
			$invoice_request->backUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/personal/order/';
			$invoice_request->requestUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/bitrix/php_interface/include/sale_payment/wooppay_mobile/wooppay_result.php?orderId=' . $order['ID'] . '&key=' . md5($order['ID']);
			$invoice_request->addInfo = $_POST['WOOPPAY_CODE'];
			$invoice_request->amount = (float)$order['PRICE'];
			$invoice_request->serviceType = 2;
			$invoice_request->deathDate = '';
			$invoice_request->description = '';
			$invoice_request->userPhone = substr($phone, 1);
			$invoice_request->userEmail = $USER->GetEmail();
			$invoice_data = $client->createInvoice($invoice_request);
			LocalRedirect($invoice_data->response->operationUrl, true);
		}
	} catch (Exception $e) {
		if (strpos($e->getMessage(), 223) != false) {
			echo '<div><h4 style="color: red;">Неверный код подтверждения.</h4></div>';
		} elseif (strpos($e->getMessage(), 224) != false) {
			echo '<div><h4 style="color: red;">Вы ввели неверный код подтверждения слишком много раз. Попробуйте через 5 минут</h4></div>';
		} elseif (strpos($e->getMessage(), 226) != false) {
			echo '<div><h4 style="color: red;">У вас недостаточно средств на балансе мобильного телефона.</h4></div>';
		} else {
			echo 'К сожалению, в системе <em>Wooppay</em> не удалось создать счёт на оплату вашего заказа. Попробуйте повторить оплату позже из персонального раздела.';
		}
	} ?>
	<form method="post" action="<?= POST_FORM_ACTION_URI ?>">
		<?php if (isset($_POST['WOOPPAY_PHONE'])) { ?>
			<input type="hidden" name="WOOPPAY_PHONE" value="<?= $phone ?>">
		<?php } ?>
		<div style="display:block">
			<h4>Код подтверждения:</h4>
			<input type="text" maxlength="6" name="WOOPPAY_CODE">
		</div>
		<div style="display:block; margin-top: 20px;float: left!important;">
			<input type="submit" name="WOOPPAY_PAY" value="Оплатить" class="pull-right btn btn-default btn-lg"/>
		</div>
	</form>
	<?php
} else {
	echo 'Заказ оплачен';
}
?>