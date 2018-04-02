<?php
/**
 * 2017-2018 Moip Wirecard Brasil
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author MOIP DEVS - <prestashop@moip.com.br>
 *  @copyright  2017-2018 Moip Wirecard Brasil
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Moip Wirecard Brasil
 */

include_once dirname(__FILE__) . '/../../../../config/config.inc.php';
include_once dirname(__FILE__) . '/../../../../init.php';
include_once dirname(__FILE__) . '/../../moipv2.php';
class Moipv2OauthModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public function __construct($response = array())
	{
		parent::__construct($response);
		$this->display_header            = false;
		$this->display_header_javascript = false;
		$this->display_footer            = false;
	}
	public function typeApp()
	{
		if (Configuration::get('MOIPV2_TYPE_RECEIPT') == 4) {
			return "APP-4WORRHSEHO5U"; // recebimento definido na conta moip
		} elseif (Configuration::get('MOIPV2_TYPE_RECEIPT') == 3) {
			return "APP-MRRA5BA9JWCK"; // receber em 30 dias
		} elseif (Configuration::get('MOIPV2_TYPE_RECEIPT') == 2) {
			return "APP-2NGTWO1QBY73"; // receber em 14 dias
		} elseif (Configuration::get('MOIPV2_TYPE_RECEIPT') == 1) {
			return "APP-B7NMMDMXMNI7"; // receber em 2 dias
		}
	}
	public function typeSecret()
	{
		if (Configuration::get('MOIPV2_TYPE_RECEIPT') == 4) {
			return "977hq8wowu9mfi0bjsmkc0j71179oxa"; // recebimento definido na conta moip
		} elseif (Configuration::get('MOIPV2_TYPE_RECEIPT') == 3) {
			return "81ebd7aa0cf0485baee10c829a5bdf1d"; // receber em 30 dias
		} elseif (Configuration::get('MOIPV2_TYPE_RECEIPT') == 2) {
			return "85defd558e1246beaa7f955daf62943d"; // receber em 14 dias
		} elseif (Configuration::get('MOIPV2_TYPE_RECEIPT') == 1) {
			return "2b226394c1ba4ef1a9e0270b25c150bc"; // receber em 2 dias
		}
	}
	public function postProcess()
	{	
		$token = "";
		$code = "";
		extract($_GET);
		if ($token == Configuration::get('MOIPV2_KEY_TOKEN')) {
			if (Configuration::get('MOIPV2_ENDPOINT') == 1) {
				$oauth        = $this->getOauthAcess($code);
				$decode_json  = json_decode($oauth, true);
				$oauth_access = $decode_json['access_token'];
				if ($oauth_access) {
					$public_key_json = $this->getKey($oauth_access);
					Configuration::updateValue('MOIPV2_OAUTH_DEV', $oauth_access);
					Configuration::updateValue('MOIPV2_PUBLICKEY_DEV', $public_key_json);
					$webhooks = $this->EnableWebHooks();
					Configuration::updateValue('MOIPV2_TOKEN_WEBHOOKS_DEV', $webhooks['token']);
					echo "Autorização realizada com sucesso, por favor, realize os testes na loja.";
				} else {
					echo "Não foi possível autorizar o módulo, por favor repita o processo.";
				}
			} else {
				$oauth        = $this->getOauthAcess($code);
				$decode_json  = json_decode($oauth, true);
				$oauth_access = $decode_json['access_token'];
				if ($oauth_access) {
					$public_key_json = $this->getKey($oauth_access);
					Configuration::updateValue('MOIPV2_OAUTH_PROD', $oauth_access);
					Configuration::updateValue('MOIPV2_PUBLICKEY_PROD', $public_key_json);
					$webhooks = $this->EnableWebHooks();
					Configuration::updateValue('MOIPV2_TOKEN_WEBHOOKS_PROD', $webhooks['token']);
					echo "Autorização realizada com sucesso, por favor, realize os testes na loja.";
				} else {
					echo "Não foi possível autorizar o módulo, por favor repita o processo.";
				}
			}
			Tools::clearSmartyCache();
			Tools::clearXMLCache();
			Media::clearCache();
			PrestaShopAutoload::getInstance()->generateIndex();
		}
	}
	public function getOauthAcess($code)
	{
		$documento = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
		$moipv2    = new Moipv2();
		if (Configuration::get('MOIPV2_ENDPOINT') == 1) {
			$url        = "https://connect-sandbox.moip.com.br/oauth/token";
			$header     = "Authorization: Basic " . base64_encode($moipv2::TOKEN_TEST . ":" . $moipv2::KEY_TEST);
			$array_json = array(
				'client_id' => 'APP-0KPSXJOVUGFI',
				'client_secret' => '5g66zd5lcfk2j8vbb7q74ahu5owv2z5',
				'redirect_uri' => 'http://moip.o2ti.com/prestashop/redirect/',
				'grant_type' => 'authorization_code',
				'code' => $code
			);
			$json       = http_build_query($array_json);
		} else {
			$url        = "https://connect.moip.com.br/oauth/token";
			$header     = "Authorization: Basic " . base64_encode($moipv2::TOKEN_PROD . ":" . $moipv2::KEY_PROD);
			$array_json = array(
				'client_id' => $this->typeApp(),
				'client_secret' => $this->typeSecret(),
				'redirect_uri' => 'http://moip.o2ti.com/prestashop/redirect/',
				'grant_type' => 'authorization_code',
				'code' => $code
			);
			$json       = http_build_query($array_json);
		}
		/* var_dump($array_json); die();*/
		$ch     = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			$header,
			$documento
		));
		curl_setopt($ch, CURLOPT_USERAGENT, 'MoipPrestashop/2.0.0');
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}
	public function getKey($oauth)
	{
		$documento = 'Content-Type: application/json; charset=utf-8';
		if (Configuration::get('MOIPV2_ENDPOINT') == 1) {
			$url    = "https://sandbox.moip.com.br/v2/keys/";
			$header = "Authorization: OAuth " . $oauth;
		} else {
			$url    = "https://api.moip.com.br/v2/keys/";
			$header = "Authorization: OAuth " . $oauth;
		}
		$ch     = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			$header,
			$documento
		));
		curl_setopt($ch, CURLOPT_USERAGENT, 'MoipMagento/2.0.0');
		$responseBody = curl_exec($ch);
		curl_close($ch);
		$responseBody = json_decode($responseBody, true);
		$_key         = $responseBody['keys']['encryption'];
		return $_key;
	}
	public function EnableWebHooks()
	{
		$status_controller = array(
			"ORDER.PAID",
			"ORDER.NOT_PAID"
		);
		$webhooks          = array(
			"events" => $status_controller,
			"target" => _PS_BASE_URL_ . __PS_BASE_URI__ . "index.php?fc=module&module=moipv2&controller=webhooks",
			"media" => "WEBHOOK"
		);
		if (Configuration::get('MOIPV2_ENDPOINT') == 1) {
			$url       = "https://sandbox.moip.com.br/v2/preferences/notifications/";
			$oauth     = Configuration::get('MOIPV2_OAUTH_DEV');
			$header    = "Authorization: OAuth " . $oauth;
			$documento = "Content-Type: application/json";
		} else {
			$url       = "https://api.moip.com.br/v2/preferences/notifications/";
			$oauth     = Configuration::get('MOIPV2_OAUTH_PROD');
			$header    = "Authorization: OAuth " . $oauth;
			$documento = "Content-Type: application/json";
		}
		$json   = json_encode($webhooks);
		$ch     = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			$header,
			$documento
		));
		curl_setopt($ch, CURLOPT_USERAGENT, 'MoipPrestashop/2.0.0');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$res  = curl_exec($ch);
		curl_close($ch);
		return json_decode($res, true);
	}
}