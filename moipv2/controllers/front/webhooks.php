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
class Moipv2WebhooksModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public function __construct($response = array())
	{
		parent::__construct($response);
		$this->display_header            = false;
		$this->display_header_javascript = false;
		$this->display_footer            = false;
	}
	public function postProcess()
	{
		sleep(2);
		Logger::addLog("-------- webhooks -------- ".rand(), 1);
		parent::postProcess();
		$headers = $this->getRequestHeaders();
		if(Configuration::get('MOIPV2_ENDPOINT') == 2){
			$authorization = Configuration::get('MOIPV2_TOKEN_WEBHOOKS_PROD');	
		} else {
			$authorization = Configuration::get('MOIPV2_TOKEN_WEBHOOKS_DEV');
		}
		if($authorization == $headers["Authorization"]){

			$inputJSON = Tools::file_get_contents('php://input');
			$input     = json_decode($inputJSON, TRUE);
			Logger::addLog($inputJSON, 1);
			$order_identificador = $input['resource']['order']['ownId'];
			$order_status        = $input['resource']['order']['status'];
			if ($order_status == "PAID") {
				$status = Configuration::get('MOIPV2_STATUS_1');
			} elseif ($order_status == "NOT_PAID") {
				$status = Configuration::get('MOIPV2_STATUS_4');
			} else {
				Logger::addLog("input ".$input, 1);
				Logger::addLog("input ".$input['resource'], 1);
				Logger::addLog("input ".$input['resource']['order'], 1);
				die();
			}
			if ($order_identificador) {
				$sql                    = 'SELECT `id_order` FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_cart` LIKE "' . $order_identificador . '"';
				$order_identificador_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
				Logger::addLog($order_identificador_id[0]['id_order'], 1);
				if ($order_identificador_id) {
					$order             = new Order($order_identificador_id[0]['id_order']);
					$history           = new OrderHistory();
					$history->id_order = $order->id;
					$last              = $history->getLastOrderState($order->id);
					if ($last->id != $status) {
						$history->changeIdOrderState($status, $order->id);
						$history->addWithemail();
						$history->save();
					}
				}
			} else {
				//header('HTTP/1.1 404 Not Found');
			}

		} else {

		}
		
	}
	private function getRequestHeaders()
	{
		$headers = array();
		foreach ($_SERVER as $key => $value) {
			if (Tools::substr($key, 0, 5) <> 'HTTP_') {
				continue;
			}
			$header           = str_replace(' ', '-', ucwords(str_replace('_', ' ', Tools::strtolower(Tools::substr($key, 5)))));
			$headers[$header] = $value;
		}
		return $headers;
	}
}
