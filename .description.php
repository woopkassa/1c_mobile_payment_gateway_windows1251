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

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><?
include(GetLangFileName(dirname(__FILE__) . "/", "/wooppay.php"));

$psTitle = GetMessage("TITLE");
$psDescription = $psDescription = "<a href=\"https://www.wooppay.com\" target=\"_blank\">https://www.wooppay.com</a>";

$arPSCorrespondence = array(
	"LOGIN" => array(
		"NAME" => GetMessage("LOGIN"),
		"DESCR" => "",
		"VALUE" => "",
		"TYPE" => ""
	),
	"PASS" => array(
		"NAME" => GetMessage("PASS"),
		"DESCR" => "",
		"VALUE" => "",
		"TYPE" => ""
	),
	"URL" => array(
		"NAME" => GetMessage("URL"),
		"DESCR" => "",
		"VALUE" => "",
		"TYPE" => ""
	),
	"SERVICE" => array(
		"NAME" => GetMessage("SERVICE"),
		"DESCR" => "",
		"VALUE" => "",
		"TYPE" => ""
	),
	"ORDER_PREFIX" => array(
		"NAME" => GetMessage("ORDER_PREFIX"),
		"DESCR" => "",
		"VALUE" => "",
		"TYPE" => ""
	)
);
?>
