<?php


include_once dirname(__FILE__) . '/../../../../config/config.inc.php';
include_once dirname(__FILE__) . '/../../../../init.php';
include_once dirname(__FILE__) . '/../../moipv2.php';
class Moipv2WebhooksModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public function __construct($response = array()) {
	    parent::__construct($response);
	    $this->display_header = false;
	    $this->display_header_javascript = false;
	    $this->display_footer = false;
	}
    public function postProcess()
    {
        parent::postProcess();
        $headers = $this->getRequestHeaders();
        Logger::addLog(json_encode($headers) ,1);
        Logger::addLog(json_encode($headers["Authorization"]) ,1);
        $inputJSON = file_get_contents('php://input');
		$input= json_decode( $inputJSON, TRUE );
		Logger::addLog($inputJSON ,1);
		$order_identificador = $input['resource']['order']['ownId'];
		$order_status = $input['resource']['order']['status'];

		if($order_status == "PAID"){
			$status = Configuration::get('MOIPV2_STATUS_1');
		} elseif ($order_status == "NOT_PAID") {
			$status = Configuration::get('MOIPV2_STATUS_5');
		} else{
			die();
		}
		if($order_identificador){
			

			$sql = 'SELECT `id_order` FROM `ps_orders` WHERE `id_cart` LIKE "'.$order_identificador.'"';
			$order_identificador_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
			if($order_identificador_id)
			{
				$order = new Order($order_identificador_id[0]['id_order']);
				$history = new OrderHistory();
				$history->id_order = intval($order->id);
				$last = $history->getLastOrderState($order->id);
				if($last->id != $status){
					$history->changeIdOrderState($status, intval($order->id));
					$history->addWithemail();
					$history->save(); 
				} 
			}
		} else {
			//header('HTTP/1.1 404 Not Found');
		}
		
    }

    private function getRequestHeaders() {
		    $headers = array();
		    foreach($_SERVER as $key => $value) {
		        if (substr($key, 0, 5) <> 'HTTP_') {
		            continue;
		        }
		        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
		        $headers[$header] = $value;
		    }
		    return $headers;
		}
}