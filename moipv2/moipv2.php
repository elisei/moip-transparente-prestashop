<?php
/*
* 2007-2014 PrestaShop
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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Moipv2 extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();
   	const TOKEN_TEST = "8OKLQFT5XQZXU7CKXX43GPJOMIJPMSMF";
    const KEY_TEST = "NT0UKOXS4ALNSVOXJVNXVKRLEOQCITHI5HDKW3LI";
    const TOKEN_PROD = "EVCHBAUMKM0U4EE4YXIA8VMC0KBEPKN2";
    const KEY_PROD = "4NECP62EKI8HRSMN3FGYOZNVYZOMBDY0EQHK9MHO";

	public $moipv2Name;
	
	public $extra_mail_vars;
	public $MOIPV2_KEY_TOKEN;
	public $MOIPV2_TOKEN_WEBHOOKS_DEV;
	public $MOIPV2_TOKEN_WEBHOOKS_PROD;
	public $MOIPV2_STATUS_1;
	public $MOIPV2_STATUS_2;
	public $MOIPV2_STATUS_3;
	public $MOIPV2_STATUS_4;
	public $MOIPV2_ENDPOINT;
	public $MOIPV2_OAUTH_PROD;
	public $MOIPV2_OAUTH_DEV;
	public $MOIPV2_PUBLICKEY_PROD;
	public $MOIPV2_PUBLICKEY_DEV;
	public $MOIPV2_BOLETO_ACEITE;
	public $MOIPV2_CARTAO_ACEITE;
	public $MOIPV2_TEF_ACEITE;
	public $MOIPV2_CARTAO_NUMBER;


	public function __construct()
	{
		$this->name = 'moipv2';
		$this->tab = 'payments_gateways';
		$this->version = '2.5.7';
		$this->author = 'MOIP DEVS - <prestashop@moip.com.br>';
		$this->controllers = array('payment', 'validation', 'authorization');
		$this->is_eu_compatible = 1;

		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$config = Configuration::getMultiple(array('MOIPV2_NAME','MOIPV2_CUPOMBOLETO','MOIPV2_BOLETODISCOUNT','MOIPV2_CARTAO_NUMBER','MOIPV2_KEY_TOKEN','MOIPV2_ENDPOINT','MOIPV2_OAUTH_PROD','MOIPV2_OAUTH_DEV','MOIPV2_PUBLICKEY_PROD','MOIPV2_PUBLICKEY_DEV','MOIPV2_TOKEN_WEBHOOKS_DEV','MOIPV2_TOKEN_WEBHOOKS_PROD', 'MOIPV2_STATUS_1','MOIPV2_STATUS_2','MOIPV2_STATUS_3','MOIPV2_STATUS_4','MOIPV2_BOLETO_ACEITE','MOIPV2_CARTAO_ACEITE','MOIPV2_TEF_ACEITE','MOIPV2_TYPE_JUROS','MOIPV2_CARTAO_PARCEL2','MOIPV2_CARTAO_PARCEL3','MOIPV2_CARTAO_PARCEL4','MOIPV2_CARTAO_PARCEL5','MOIPV2_CARTAO_PARCEL6','MOIPV2_CARTAO_PARCEL7','MOIPV2_CARTAO_PARCEL8','MOIPV2_CARTAO_PARCEL9','MOIPV2_CARTAO_PARCEL10','MOIPV2_CARTAO_PARCEL11','MOIPV2_CARTAO_PARCEL12','MOIPV2_REDIR_BOLETO','MOIPV2_REDIR_TEF'));
		if (isset($config['MOIPV2_NAME']))
			$this->moipv2Name = $config['MOIPV2_NAME'];
		if (isset($config['MOIPV2_KEY_TOKEN']))
			$this->moipv2Name = $config['MOIPV2_KEY_TOKEN'];
		if (isset($config['MOIPV2_ENDPOINT']))
			$this->moipv2Name = $config['MOIPV2_ENDPOINT'];
		if (isset($config['MOIPV2_OAUTH_PROD']))
			$this->MOIPV2_OAUTH_PROD = $config['MOIPV2_OAUTH_PROD'];
		if (isset($config['MOIPV2_OAUTH_DEV']))
			$this->MOIPV2_OAUTH_DEV = $config['MOIPV2_OAUTH_DEV'];
		if (isset($config['MOIPV2_PUBLICKEY_PROD']))
			$this->MOIPV2_PUBLICKEY_PROD = $config['MOIPV2_PUBLICKEY_PROD'];
		if (isset($config['MOIPV2_PUBLICKEY_DEV']))
			$this->MOIPV2_PUBLICKEY_DEV = $config['MOIPV2_PUBLICKEY_DEV'];
		if (isset($config['MOIPV2_TOKEN_WEBHOOKS_DEV']))
			$this->MOIPV2_TOKEN_WEBHOOKS = $config['MOIPV2_TOKEN_WEBHOOKS_DEV'];
		if (isset($config['MOIPV2_TOKEN_WEBHOOKS_PROD']))
			$this->MOIPV2_TOKEN_WEBHOOKS = $config['MOIPV2_TOKEN_WEBHOOKS_PROD'];

		if (isset($config['MOIPV2_STATUS_1']))
			$this->MOIPV2_STATUS_1 = $config['MOIPV2_STATUS_1'];
		if (isset($config['MOIPV2_STATUS_2']))
			$this->MOIPV2_STATUS_2 = $config['MOIPV2_STATUS_2'];
		if (isset($config['MOIPV2_STATUS_3']))
			$this->MOIPV2_STATUS_3 = $config['MOIPV2_STATUS_3'];
		if (isset($config['MOIPV2_STATUS_4']))
			$this->MOIPV2_STATUS_4 = $config['MOIPV2_STATUS_4'];
		

		if (isset($config['MOIPV2_BOLETO_ACEITE']))
			$this->MOIPV2_BOLETO_ACEITE = $config['MOIPV2_BOLETO_ACEITE'];
		if (isset($config['MOIPV2_CARTAO_ACEITE']))
			$this->MOIPV2_CARTAO_ACEITE = $config['MOIPV2_CARTAO_ACEITE'];
		if (isset($config['MOIPV2_TEF_ACEITE']))
			$this->MOIPV2_TEF_ACEITE = $config['MOIPV2_TEF_ACEITE'];
		
		if (isset($config['MOIPV2_CARTAO_NUMBER']))
			$this->MOIPV2_CARTAO_NUMBER = $config['MOIPV2_CARTAO_NUMBER'];

		if(isset($config['MOIPV2_TYPE_JUROS']))
			$this->MOIPV2_TYPE_JUROS = $config['MOIPV2_TYPE_JUROS'];
		if (isset($config['MOIPV2_BOLETODISCOUNT']))
			$this->MOIPV2_BOLETODISCOUNT = $config['MOIPV2_BOLETODISCOUNT'];
		if (isset($config['MOIPV2_CUPOMBOLETO']))
			$this->MOIPV2_CUPOMBOLETO = $config['MOIPV2_CUPOMBOLETO'];



		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('MOIP Wirecard Brasil');
		$this->description = $this->l('Moip Wirecard Brasil, recebimento com cartão, boleto ou transferência');
		$this->confirmUninstall = $this->l('Você realmente quer desinstalar?');

		

		$this->extra_mail_vars = array('{moipv2_name}' => Configuration::get('MOIPV2_NAME'),);
	}

	public function install()
	{	
		
		
		if (!parent::install() || !$this->registerHook('payment')  || !$this->registerHook('paymentReturn')){
			$token = rand('9999999','999999999');
			Configuration::updateValue('MOIPV2_KEY_TOKEN', $token);
			Configuration::updateValue('MOIPV2_ENDPOINT', 1);
			Configuration::updateValue('MOIPV2_TYPE_JUROS', 1);
			Configuration::updateValue('MOIPV2_CARTAO_NUMBER', 12);
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL2', 4.50);
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL3', 5.00);
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL4', 5.50);
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL5', 6.50);
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL6', 7.50);
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL7', 8.50);
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL8', 9.50);
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL9', 10.50);
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL10', 11.50);
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL11', 12.00);
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL12', 12.50);
			Configuration::updateValue('MOIPV2_BOLETO_ACEITE', 1);
			Configuration::updateValue('MOIPV2_CARTAO_ACEITE', 1);
			Configuration::updateValue('MOIPV2_TEF_ACEITE', 1);
			

			
			return false;
		}
		$token = rand('9999999','999999999');
		Configuration::updateValue('MOIPV2_ENDPOINT', 1);
		Configuration::updateValue('MOIPV2_NAME', "Cartão de Crédito, Boleto ou Transferência");
		Configuration::updateValue('MOIPV2_KEY_TOKEN', $token);

		Configuration::updateValue('MOIPV2_ENDPOINT', 1);
		Configuration::updateValue('MOIPV2_TYPE_JUROS', 1);
		Configuration::updateValue('MOIPV2_CARTAO_NUMBER', 12);
		Configuration::updateValue('MOIPV2_CARTAO_PARCEL2', 4.50);
		Configuration::updateValue('MOIPV2_CARTAO_PARCEL3', 5.00);
		Configuration::updateValue('MOIPV2_CARTAO_PARCEL4', 5.50);
		Configuration::updateValue('MOIPV2_CARTAO_PARCEL5', 6.50);
		Configuration::updateValue('MOIPV2_CARTAO_PARCEL6', 7.50);
		Configuration::updateValue('MOIPV2_CARTAO_PARCEL7', 8.50);
		Configuration::updateValue('MOIPV2_CARTAO_PARCEL8', 9.50);
		Configuration::updateValue('MOIPV2_CARTAO_PARCEL9', 10.50);
		Configuration::updateValue('MOIPV2_CARTAO_PARCEL10', 11.50);
		Configuration::updateValue('MOIPV2_CARTAO_PARCEL11', 12.00);
		Configuration::updateValue('MOIPV2_CARTAO_PARCEL12', 12.50);
		Configuration::updateValue('MOIPV2_BOLETO_ACEITE', 1);
		Configuration::updateValue('MOIPV2_CARTAO_ACEITE', 1);
		Configuration::updateValue('MOIPV2_TEF_ACEITE', 1);
		
		$this->add_order_state('MOIPV2_STATUS_2','br:MOIP - Aguardando Pagamento', 0, 1, '#4169E1', 0, 1, 0, 'br:moip_analise');
		$this->add_order_state('MOIPV2_STATUS_3','br:MOIP - Pagamento em Análise', 0, 1, '#4169E1', 0, 1, 0, 'br:awaiting_payment');
		
	
		return true;
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('MOIPV2_NAME') 
					|| !Configuration::deleteByName('MOIPV2_KEY_TOKEN')
					|| !Configuration::deleteByName('MOIPV2_ENDPOINT')
					|| !Configuration::deleteByName('MOIPV2_OAUTH_PROD')
					|| !Configuration::deleteByName('MOIPV2_OAUTH_DEV')
					|| !Configuration::deleteByName('MOIPV2_PUBLICKEY_PROD')
					|| !Configuration::deleteByName('MOIPV2_PUBLICKEY_DEV')
					|| !Configuration::deleteByName('MOIPV2_TOKEN_WEBHOOKS_DEV')
					|| !Configuration::deleteByName('MOIPV2_TOKEN_WEBHOOKS_PROD')

					|| !Configuration::deleteByName('MOIPV2_STATUS_1')
					|| !Configuration::deleteByName('MOIPV2_STATUS_2')
                    || !Configuration::deleteByName('MOIPV2_STATUS_3')
                    || !Configuration::deleteByName('MOIPV2_STATUS_4')
                    
                    || !Configuration::deleteByName('MOIPV2_BOLETO_ACEITE')
                    || !Configuration::deleteByName('MOIPV2_CARTAO_ACEITE')
                    || !Configuration::deleteByName('MOIPV2_TEF_ACEITE')

                    || !Configuration::deleteByName('MOIPV2_CARTAO_NUMBER')
                    || !Configuration::deleteByName('MOIPV2_CARTAO_PARCEL2')
                    || !Configuration::deleteByName('MOIPV2_CARTAO_PARCEL3')
                    || !Configuration::deleteByName('MOIPV2_CARTAO_PARCEL4')
                    || !Configuration::deleteByName('MOIPV2_CARTAO_PARCEL5')
                    || !Configuration::deleteByName('MOIPV2_CARTAO_PARCEL6')
                    || !Configuration::deleteByName('MOIPV2_CARTAO_PARCEL7')
                    || !Configuration::deleteByName('MOIPV2_CARTAO_PARCEL8')
                    || !Configuration::deleteByName('MOIPV2_CARTAO_PARCEL9')
                    || !Configuration::deleteByName('MOIPV2_CARTAO_PARCEL10')
                    || !Configuration::deleteByName('MOIPV2_CARTAO_PARCEL11')
                    || !Configuration::deleteByName('MOIPV2_TYPE_JUROS')
                    
                    || !Configuration::deleteByName('MOIPV2_REDIR_BOLETO')
                    || !Configuration::deleteByName('MOIPV2_REDIR_TEF')

                     || !Configuration::deleteByName('MOIPV2_BOLETODISCOUNT')
                    || !Configuration::deleteByName('MOIPV2_CUPOMBOLETO')

                    
                    || !parent::uninstall())
			return false;
		return true;
	}
	
	private function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			Configuration::updateValue('MOIPV2_NAME', Tools::getValue('MOIPV2_NAME'));
			Configuration::updateValue('MOIPV2_ENDPOINT', Tools::getValue('MOIPV2_ENDPOINT'));
			Configuration::updateValue('MOIPV2_OAUTH_PROD', Tools::getValue('MOIPV2_OAUTH_PROD'));
			Configuration::updateValue('MOIPV2_OAUTH_DEV', Tools::getValue('MOIPV2_OAUTH_DEV'));
			Configuration::updateValue('MOIPV2_PUBLICKEY_PROD', Tools::getValue('MOIPV2_PUBLICKEY_PROD'));
			Configuration::updateValue('MOIPV2_PUBLICKEY_DEV', Tools::getValue('MOIPV2_PUBLICKEY_DEV'));

			Configuration::updateValue('MOIPV2_TOKEN_WEBHOOKS_DEV', Tools::getValue('MOIPV2_TOKEN_WEBHOOK_DEV'));
			Configuration::updateValue('MOIPV2_TOKEN_WEBHOOKS_PROD', Tools::getValue('MOIPV2_TOKEN_WEBHOOK_PROD'));

			Configuration::updateValue('MOIPV2_STATUS_1', Tools::getValue('MOIPV2_STATUS_1'));
			Configuration::updateValue('MOIPV2_STATUS_2', Tools::getValue('MOIPV2_STATUS_2'));
			Configuration::updateValue('MOIPV2_STATUS_3', Tools::getValue('MOIPV2_STATUS_3'));
			Configuration::updateValue('MOIPV2_STATUS_4', Tools::getValue('MOIPV2_STATUS_4'));
			
			Configuration::updateValue('MOIPV2_BOLETO_ACEITE', Tools::getValue('MOIPV2_BOLETO_ACEITE'));
			Configuration::updateValue('MOIPV2_CARTAO_ACEITE', Tools::getValue('MOIPV2_CARTAO_ACEITE'));
			Configuration::updateValue('MOIPV2_TEF_ACEITE', Tools::getValue('MOIPV2_TEF_ACEITE'));


			Configuration::updateValue('MOIPV2_TYPE_JUROS', Tools::getValue('MOIPV2_TYPE_JUROS'));
			Configuration::updateValue('MOIPV2_CARTAO_NUMBER', Tools::getValue('MOIPV2_CARTAO_NUMBER'));
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL2', Tools::getValue('MOIPV2_CARTAO_PARCEL2'));
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL2', Tools::getValue('MOIPV2_CARTAO_PARCEL2'));
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL3', Tools::getValue('MOIPV2_CARTAO_PARCEL3'));
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL4', Tools::getValue('MOIPV2_CARTAO_PARCEL4'));
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL5', Tools::getValue('MOIPV2_CARTAO_PARCEL5'));
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL6', Tools::getValue('MOIPV2_CARTAO_PARCEL6'));
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL7', Tools::getValue('MOIPV2_CARTAO_PARCEL7'));
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL8', Tools::getValue('MOIPV2_CARTAO_PARCEL8'));
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL9', Tools::getValue('MOIPV2_CARTAO_PARCEL9'));
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL10', Tools::getValue('MOIPV2_CARTAO_PARCEL10'));
			Configuration::updateValue('MOIPV2_CARTAO_PARCEL11', Tools::getValue('MOIPV2_CARTAO_PARCEL11'));
			


			

			Configuration::updateValue('MOIPV2_CUPOMBOLETO', Tools::getValue('MOIPV2_CUPOMBOLETO'));
			Configuration::updateValue('MOIPV2_BOLETODISCOUNT', Tools::getValue('MOIPV2_BOLETODISCOUNT'));



			
			
		}
		$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
	}


	
    public function getConfigFieldsValues()
	{
		
		return array(
			'MOIPV2_NAME' => Tools::getValue('MOIPV2_NAME', Configuration::get('MOIPV2_NAME')),
			'MOIPV2_ENDPOINT' => Tools::getValue('MOIPV2_ENDPOINT', Configuration::get('MOIPV2_ENDPOINT')),
			'MOIPV2_OAUTH_PROD' =>  Tools::getValue('MOIPV2_OAUTH_PROD', Configuration::get('MOIPV2_OAUTH_PROD')),
			'MOIPV2_OAUTH_DEV' =>  Tools::getValue('MOIPV2_OAUTH_DEV', Configuration::get('MOIPV2_OAUTH_DEV')),
			'MOIPV2_PUBLICKEY_PROD' =>  Tools::getValue('MOIPV2_PUBLICKEY_PROD', Configuration::get('MOIPV2_PUBLICKEY_PROD')),
			'MOIPV2_PUBLICKEY_DEV' =>  Tools::getValue('MOIPV2_PUBLICKEY_DEV', Configuration::get('MOIPV2_PUBLICKEY_DEV')),
			'MOIPV2_TOKEN_WEBHOOKS_DEV' =>  Tools::getValue('MOIPV2_TOKEN_WEBHOOKS_DEV', Configuration::get('MOIPV2_TOKEN_WEBHOOKS_DEV')),
			'MOIPV2_TOKEN_WEBHOOKS_PROD' =>  Tools::getValue('MOIPV2_TOKEN_WEBHOOKS_PROD', Configuration::get('MOIPV2_TOKEN_WEBHOOKS_PROD')),

			'MOIPV2_STATUS_1' =>  Tools::getValue('MOIPV2_STATUS_1', Configuration::get('MOIPV2_STATUS_1')),
			'MOIPV2_STATUS_2' =>  Tools::getValue('MOIPV2_STATUS_2', Configuration::get('MOIPV2_STATUS_2')),
			'MOIPV2_STATUS_3' =>  Tools::getValue('MOIPV2_STATUS_3', Configuration::get('MOIPV2_STATUS_3')),
			'MOIPV2_STATUS_4' =>  Tools::getValue('MOIPV2_STATUS_4', Configuration::get('MOIPV2_STATUS_4')),
			'MOIPV2_CARTAO_ACEITE' =>  Tools::getValue('MOIPV2_CARTAO_ACEITE', Configuration::get('MOIPV2_CARTAO_ACEITE')),
			'MOIPV2_BOLETO_ACEITE' =>  Tools::getValue('MOIPV2_BOLETO_ACEITE', Configuration::get('MOIPV2_BOLETO_ACEITE')),
			'MOIPV2_TEF_ACEITE' =>  Tools::getValue('MOIPV2_TEF_ACEITE', Configuration::get('MOIPV2_TEF_ACEITE')),
			'MOIPV2_CARTAO_NUMBER' =>  Tools::getValue('MOIPV2_CARTAO_NUMBER', Configuration::get('MOIPV2_CARTAO_NUMBER')),

			'MOIPV2_TYPE_JUROS' =>  Tools::getValue('MOIPV2_TYPE_JUROS', Configuration::get('MOIPV2_TYPE_JUROS')),


			'MOIPV2_CARTAO_PARCEL2' =>  Tools::getValue('MOIPV2_CARTAO_PARCEL2', Configuration::get('MOIPV2_CARTAO_PARCEL2')),
			'MOIPV2_CARTAO_PARCEL3' =>  Tools::getValue('MOIPV2_CARTAO_PARCEL3', Configuration::get('MOIPV2_CARTAO_PARCEL3')),
			'MOIPV2_CARTAO_PARCEL4' =>  Tools::getValue('MOIPV2_CARTAO_PARCEL4', Configuration::get('MOIPV2_CARTAO_PARCEL4')),
			'MOIPV2_CARTAO_PARCEL5' =>  Tools::getValue('MOIPV2_CARTAO_PARCEL5', Configuration::get('MOIPV2_CARTAO_PARCEL5')),
			'MOIPV2_CARTAO_PARCEL6' =>  Tools::getValue('MOIPV2_CARTAO_PARCEL6', Configuration::get('MOIPV2_CARTAO_PARCEL6')),
			'MOIPV2_CARTAO_PARCEL7' =>  Tools::getValue('MOIPV2_CARTAO_PARCEL7', Configuration::get('MOIPV2_CARTAO_PARCEL7')),
			'MOIPV2_CARTAO_PARCEL8' =>  Tools::getValue('MOIPV2_CARTAO_PARCEL8', Configuration::get('MOIPV2_CARTAO_PARCEL8')),
			'MOIPV2_CARTAO_PARCEL9' =>  Tools::getValue('MOIPV2_CARTAO_PARCEL9', Configuration::get('MOIPV2_CARTAO_PARCEL9')),
			'MOIPV2_CARTAO_PARCEL10' =>  Tools::getValue('MOIPV2_CARTAO_PARCEL10', Configuration::get('MOIPV2_CARTAO_PARCEL10')),
			'MOIPV2_CARTAO_PARCEL11' =>  Tools::getValue('MOIPV2_CARTAO_PARCEL11', Configuration::get('MOIPV2_CARTAO_PARCEL11')),
			'MOIPV2_CARTAO_PARCEL12' =>  Tools::getValue('MOIPV2_CARTAO_PARCEL12', Configuration::get('MOIPV2_CARTAO_PARCEL12')),
			
			'MOIPV2_BOLETODISCOUNT' => Tools::getValue('MOIPV2_BOLETODISCOUNT', Configuration::get('MOIPV2_BOLETODISCOUNT')),
			'MOIPV2_CUPOMBOLETO' => Tools::getValue('MOIPV2_CUPOMBOLETO', Configuration::get('MOIPV2_CUPOMBOLETO')),

		);
	}

	private function _postValidation()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			if (!Tools::getValue('MOIPV2_NAME'))
				$this->_postErrors[] = $this->l('The "Pay to the order of" field is required.');
			
		}
	}

	

	private function _displayMoipv2()
	{	
		$redirectUri = Tools::getHttpHost(true).__PS_BASE_URI__.'index.php?fc=module&module=moipv2&controller=oauth?token='.Configuration::get('MOIPV2_KEY_TOKEN');
		$redirectUri = urlencode($redirectUri);
		

		$url_process_producao ="#";
		if(Configuration::get('MOIPV2_ENDPOINT') == 1){
			$ambiente = "sandbox";
			$responseType = "code";
			$redirectUri = "http://moip.o2ti.com/prestashop/redirect/?client_id=".$redirectUri;
			$appId = 'APP-0KPSXJOVUGFI';
			$scope	= 'RECEIVE_FUNDS,REFUND,MANAGE_ACCOUNT_INFO,DEFINE_PREFERENCES,RETRIEVE_FINANCIAL_INFO';
			$endpoint_moip = "https://connect-sandbox.moip.com.br/oauth/authorize";
		
			$publickey = Configuration::get('MOIPV2_PUBLICKEY_DEV');
		} else {
			$ambiente = "producao";
			$responseType = "code";
			$redirectUri = "http://moip.o2ti.com/prestashop/redirect/?client_id=".$redirectUri;
			$appId = 'APP-4WORRHSEHO5U';
			$scope	= 'RECEIVE_FUNDS,REFUND,MANAGE_ACCOUNT_INFO,DEFINE_PREFERENCES,RETRIEVE_FINANCIAL_INFO';
			$endpoint_moip = "https://connect.moip.com.br/oauth/authorize";
			
			$publickey = Configuration::get('MOIPV2_PUBLICKEY_PROD');
		}

			$set_url_btn = $endpoint_moip.'?response_type='.$responseType.'&client_id='.$appId.'&redirect_uri='.$redirectUri.'&scope='.$scope;
		if(Configuration::get('MOIPV2_OAUTH_DEV') && Configuration::get('MOIPV2_PUBLICKEY_DEV')){
			$state_dev = 'ativo';
		} else {
			$state_dev = 'inativo';
		}
		if(Configuration::get('MOIPV2_OAUTH_PROD') && Configuration::get('MOIPV2_PUBLICKEY_PROD')){
			$state_prod = 'ativo';
		} else {
			$state_prod = 'inativo';
		}

		$this->smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_moipv2' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/',
			'url_process' => $set_url_btn,
			'ambiente' => $ambiente,
			'state_prod' => $state_prod,
			'state_dev' => $state_dev,
			'module_dir' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/',
			'MOIP_INSTALLMENT' => Tools::getHttpHost(true).__PS_BASE_URI__.'index.php?fc=module&module=moipv2&controller=installment',
			'MOIPV2_NAME' => Configuration::get('MOIPV2_NAME'),
			'MOIPV2_ENDPOINT' => Configuration::get('MOIPV2_ENDPOINT'),
			'MOIPV2_OAUTH_PROD' => Configuration::get('MOIPV2_OAUTH_PROD'),
			'MOIPV2_OAUTH_DEV' => Configuration::get('MOIPV2_OAUTH_DEV'),
			'MOIPV2_PUBLICKEY' => Configuration::get('MOIPV2_PUBLICKEY_PROD'),
			'MOIPV2_PUBLICKEY_DEV' => Configuration::get('MOIPV2_PUBLICKEY_DEV'),
			'MOIPV2_CUPOMBOLETO' => Configuration::get('MOIPV2_CUPOMBOLETO'),
			'MOIPV2_BOLETODISCOUNT' => Configuration::get('MOIPV2_BOLETODISCOUNT'),
		));
		return $this->display(__FILE__, 'infos.tpl');
	}

	public function getContent()
	{
		$this->_html = "";
		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postValidation();
			if (!count($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors as $err)
					$this->_html .= $this->displayError($err);
		}
		else
			$this->_html .= '<br />';

		$this->_html .= $this->_displayMoipv2();
		$this->_html .= $this->renderForm();

		return $this->_html;
	}

	public function hookPayment($params)
	{
		if (!$this->active)
			return;
		if (!$this->checkCurrency($params['cart']))
			return;
		
    	$cart = $this->context->cart;
    	$customer = new Customer($cart->id_customer);
    	if(Configuration::get('MOIPV2_ENDPOINT') == 1){
    		$publickey = Configuration::get('MOIPV2_PUBLICKEY_DEV');
    	} else {
    		$publickey = Configuration::get('MOIPV2_PUBLICKEY_PROD');
    	}
    		$this->smarty->assign(array(
				'this_path' => $this->_path,
				'publickey' => $publickey,
				'order_total' => $this->context->cart->getOrderTotal(true, Cart::BOTH),
				'errors_moip' => null,
				'orderValueBr' =>$this->context->cart->getOrderTotal(true, Cart::BOTH),
				'this_path_moipv2' => $this->_path,
				'MOIPV2_BOLETO_ACEITE' => Configuration::get('MOIPV2_BOLETO_ACEITE'),
				'MOIPV2_CARTAO_ACEITE' => Configuration::get('MOIPV2_CARTAO_ACEITE'),
				'MOIPV2_TEF_ACEITE' => Configuration::get('MOIPV2_TEF_ACEITE'),
				'MOIPV2_CUPOMBOLETO' => Configuration::get('MOIPV2_CUPOMBOLETO'),
				'MOIPV2_BOLETODISCOUNT' => Configuration::get('MOIPV2_BOLETODISCOUNT'),
				'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/',
				'url_process' => 'index.php?controller=order-payment&id_cart='.(int)$cart->id.'&id_module='.(int)$this->id.'&id_order='.$this->currentOrder.'&key='.$customer->secure_key
			));	
    	

		
		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookDisplayPaymentEU($params)
	{
		if (!$this->active)
			return;
		if (!$this->checkCurrency($params['cart']))
			return;

		return array(
			'cta_text' => $this->l('Pay by Check'),
			'logo' => Media::getMediaPath(dirname(__FILE__).'/moipv2.png'),
			'action' => $this->context->link->getModuleLink($this->name.'a', 'validation', array(), true)
		);
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return;
		
		extract($_GET);
		$state = $params['objOrder']->getCurrentState();
		if (in_array($state, array(Configuration::get('MOIPV2_STATUS_1'), Configuration::get('MOIPV2_STATUS_2'),Configuration::get('MOIPV2_STATUS_3'),Configuration::get('PS_OS_OUTOFSTOCK'), Configuration::get('PS_OS_OUTOFSTOCK_UNPAID'))))
		{
			if($paymentMethod == "BOLETO"){
					$this->smarty->assign(array(
						'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
						'moipv2Name' => "Boleto Bancário",
						'code_line'=> $code_line,
						'redirectURI'=>$redirectURI,
						'paymentMethod' => "BOLETO",
						'moip_status' => $moip_status,
						'status' => 'ok',
						'id_order' => $params['objOrder']->id
					));
			} elseif($paymentMethod == "ONLINE_BANK_DEBIT") {
					$this->smarty->assign(array(
						'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
						'moipv2Name' => "Transferência Bancária",
						'redirectURI'=> $redirectURI,
						'paymentMethod' => "ONLINE_BANK_DEBIT",
						'moip_status' => $moip_status,
						'status' => 'ok',
						'id_order' => $params['objOrder']->id
					));
			} elseif($paymentMethod == "CREDIT_CARD") {
					$this->smarty->assign(array(
						'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
						'moipv2Name' => "Cartão de Crédito",
						'paymentMethod' => "CREDIT_CARD",
						'code_id_payment' => $code_id_payment,
						'moip_status' => $moip_status,
						'status' => 'ok',
						'id_order' => $params['objOrder']->id
					));
			} else {
					$this->smarty->assign(array(
						'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
						'moipv2Name' => $this->moipv2Name,
						'status' => 'error',
						'id_order' => $params['objOrder']->id
					));
			}
			
			if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference))
				$this->smarty->assign('reference', $params['objOrder']->reference);
		}
		else
			$this->smarty->assign('status', 'failed');
		return $this->display(__FILE__, 'payment_return.tpl');
	}

	public function checkCurrency($cart)
	{
		$currency_order = new Currency((int)($cart->id_currency));
		$currencies_module = $this->getCurrency((int)$cart->id_currency);

		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}

	private function _returnstatus_select(){
        global $cookie;
        $retorno = '';
        
        if (_PS_VERSION_ < '1.5'){
            $id_lang = $cookie->id_lang;
        }else{
            $id_lang = Context::getContext()->language->id;
        }
        
        $rq = Db::getInstance()->ExecuteS('SELECT `id_order_state`, `name` FROM `'._DB_PREFIX_.'order_state_lang`
                                         WHERE id_lang = \''.$id_lang.'\'');

        foreach ($rq as $status_moipv2){
        	$status_list[] =	array(
					                'id_option' => $status_moipv2["id_order_state"], 
					                'name' => $status_moipv2["name"],
					                'select' => 1
					              );
				        
        }
     
        return $status_list;
    }

	public function renderForm()
	{
		$options = array();
		$options_endpoint = array();
		$options_juros =  array(
								  array(
								    'id_option' => 1,              
								    'name' => 'Juros Simples'           
								  ),
								  array(
								    'id_option' => 2,
								    'name' => 'Juros Composto'           
								  ),
								);
		$options = $this->_returnstatus_select();
		$options_endpoint =  array(
													  array(
													    'id_option' => 1,              
													    'name' => 'SandBox (Ambiente de teste)'           
													  ),
													  array(
													    'id_option' => 2,
													    'name' => 'Produção (Ambiente de comrpas reais)'           
													  ),
													);
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Nome do meio de pagamento'),
					'icon' => 'icon-AdminAdmin'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Nome do meio de pagamento'),
						'name' => 'MOIPV2_NAME',
						'required' => true,
					),
					
				),
			
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),			
		);

		$fields_form1 = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Ambiente de comunicação'),
					'icon' => 'icon-AdminParentOrders',
					
				),
				'input' => array(
					array(
			            'type' => 'select',
			            'lang' => true,
			            'label' => $this->l('Ambiente'),
			            'name' => 'MOIPV2_ENDPOINT',
			            
			            'options' => array(
			              'query' => $options_endpoint,
			              'id' => 'id_option', 
			              'name' => 'name'
			            )
			            
			          ),
					array(
						'type' => 'hidden',
						'label' => $this->l('Token de Acesso Oauth - Ambiente de Produção'),
						'name' => 'MOIPV2_OAUTH_PROD',
						'desc' => 'Será preenchido após autorizar o modulo',
						'readonly'=> true

						
					),
					array(
						'type' => 'hidden',
						'label' => $this->l('Chave Pública - Ambiente de Produção'),
						'name' => 'MOIPV2_PUBLICKEY_PROD',
						'desc' => 'Será preenchido após autorizar o modulo',
						'readonly'=> true
					),
				
					array(
						'type' => 'hidden',
						'label' => $this->l('Token de Acesso Oauth - Ambiente SandBox'),
						'name' => 'MOIPV2_OAUTH_DEV',
						'desc' => 'Será preenchido após autorizar o modulo',
						
						'readonly'=> true
					),
									
					array(
						'type' => 'hidden',
						'label' => $this->l('Chave Pública - Ambiente SandBox'),
						'name' => 'MOIPV2_PUBLICKEY_DEV',
						'desc' => 'Será preenchido após autorizar o modulo',
						'readonly'=> true
					),
					
				),
			
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),			
		);

		
		$fields_form2 = array(
				'form' => array(
				'legend' => array(
					'title' => $this->l('Atualizaçao de status'),
					'icon' => 'icon-AdminParentOrders'
				),
				'input' => array(
					
					array(
			            'type' => 'select',
			            'lang' => true,
			            'label' => $this->l('Pagamento aprovado'),
			            'name' => 'MOIPV2_STATUS_1',
			            
			            'options' => array(
			              'query' => $options,
			              'id' => 'id_option', 
			              'name' => 'name'
			            )
			            
			          ),
					array(
			            'type' => 'select',
			            'lang' => true,
			            'label' => $this->l('Aguardando Pagamento de Boleto/TEF'),
			            'name' => 'MOIPV2_STATUS_2',
			            
			            'options' => array(
			              'query' => $options,
			              'id' => 'id_option', 
			              'name' => 'name'
			            )
			            
			          ),
					array(
			            'type' => 'select',
			            'lang' => true,
			            'label' => $this->l('Pagamento em Análise (Cartão de Crédito)'),
			            'name' => 'MOIPV2_STATUS_3',
			            
			            'options' => array(
			              'query' => $options,
			              'id' => 'id_option', 
			              'name' => 'name'
			            )
			            
			          ),
					
					
				),
			
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		 );



		$options_true = array(
	          array( 'id_option' => 1, 'name' => 'Sim' ),
	          array( 'id_option' => 0, 'name' => 'Nao' )
	        );
		$options_true1 = array(
	          array( 'id_option' => 1, 'name' => 'Sim' ),
	          array( 'id_option' => 0, 'name' => 'Nao' )
	        );
		$options_true2 = array(
	          array( 'id_option' => 1, 'name' => 'Sim' ),
	          array( 'id_option' => 0, 'name' => 'Nao' )
	        );

		$options_true3 = array(
	          array( 'id_option' => 1, 'name' => 'Sim' ),
	          array( 'id_option' => 0, 'name' => 'Nao' )
	        );
		
			$fields_form3 = array(
				'form' => array(
				'legend' => array(
					'title' => $this->l('Meios de Pagamento aceitos'),
					'icon' => 'icon-AdminParentOrders'
				),
				'input' => array(
				
					array(
			            'type' => 'select',
			            'lang' => true,
			            'label' => $this->l('Permitir compras com Cartão de Crédito'),
			            'name' => 'MOIPV2_CARTAO_ACEITE',
			            
			            'options' => array(
			              'query' => $options_true,
			              'id' => 'id_option', 
			              'name' => 'name'
			            )
			            
			          ),
					array(
			            'type' => 'select',
			            'lang' => true,
			            'label' => $this->l('Permitir compras com Boleto'),
			            'name' => 'MOIPV2_BOLETO_ACEITE',
			            
			            'options' => array(
			              'query' => $options_true,
			              'id' => 'id_option', 
			              'name' => 'name'
			            )
			            
			          ),
					array(
			            'type' => 'select',
			            'lang' => true,
			            'label' => $this->l('Oferecer desconto no Pagamento por Boleto?'),
			            'name' => 'MOIPV2_BOLETODISCOUNT',
			            
			            'options' => array(
			              'query' => $options_true,
			              'id' => 'id_option', 
			              'name' => 'name'
			            )
			            
			          ),

					array(
			            'type' => 'text',
			            'lang' => false,
			            'label' => $this->l('Cupom a ser aplicado em caso de compra por boleto'),
			            'desc' => 'Você deverá criar um cupom de desconto e especificar o código dele aqui.',
			            'name' => 'MOIPV2_CUPOMBOLETO',
			          ),

					array(
			            'type' => 'select',
			            'lang' => true,
			            'label' => $this->l('Permitir compras com Transferência Bancária'),
			            'name' => 'MOIPV2_TEF_ACEITE',
			            
			            'options' => array(
			              'query' => $options_true,
			              'id' => 'id_option', 
			              'name' => 'name'
			            )
			            
			          ),
					
				),
			
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
			
		 );


		
		$fields_form4 = array(
				'form' => array(
				'legend' => array(
					'title' => $this->l('Configuraçoes de Parcelamento'),
					'icon' => 'icon-AdminParentOrders'
				),
				'input' => array(
					array(
			            'type' => 'text',
			            'lang' => false,
			            'label' => $this->l('Número máximo de parcelas permitidas'),
			            'desc' => 'No máximo em 12x, o moip nao parcela em mais vezes.',
			            'name' => 'MOIPV2_CARTAO_NUMBER',
			          ),
					array(
			            'type' => 'select',
			            'lang' => false,
			            'label' => $this->l('Tipo de juros'),
			            'name' => 'MOIPV2_TYPE_JUROS',
			            'options' => array(
				              'query' => $options_juros,
				              'id' => 'id_option', 
				              'name' => 'name'
				            )
			          ),
					array(
			            'type' => 'text',
			            'lang' => false,
			            'label' => $this->l('Valor do juros para a parcela 2'),
			            'desc' => 'Por padrão esse valor é de 3.99, não use vírgulas apenas pontos para casas decimais, não usar o %.',
			            'name' => 'MOIPV2_CARTAO_PARCEL2',
			          ),
					array(
			            'type' => 'text',
			            'lang' => false,
			            'label' => $this->l('Valor do juros para a parcela 3'),
			            'desc' => 'Por padrão esse valor é de 3.99, não use vírgulas apenas pontos para casas decimais, não usar o %.',
			            'name' => 'MOIPV2_CARTAO_PARCEL3',
			          ),
					array(
			            'type' => 'text',
			            'lang' => false,
			            'label' => $this->l('Valor do juros para a parcela 4'),
			            'desc' => 'Por padrão esse valor é de 3.99, não use vírgulas apenas pontos para casas decimais, não usar o %.',
			            'name' => 'MOIPV2_CARTAO_PARCEL4',
			          ),
					array(
			            'type' => 'text',
			            'lang' => false,
			            'label' => $this->l('Valor do juros para a parcela 5'),
			            'desc' => 'Por padrão esse valor é de 3.99, não use vírgulas apenas pontos para casas decimais, não usar o %.',
			            'name' => 'MOIPV2_CARTAO_PARCEL5',
			          ),
			          array(
			            'type' => 'text',
			            'lang' => false,
			            'label' => $this->l('Valor do juros para a parcela 6'),
			            'desc' => 'Por padrão esse valor é de 3.99, não use vírgulas apenas pontos para casas decimais, não usar o %.',
			            'name' => 'MOIPV2_CARTAO_PARCEL6',
			          ),
			          array(
			            'type' => 'text',
			            'lang' => false,
			            'label' => $this->l('Valor do juros para a parcela 7'),
			            'desc' => 'Por padrão esse valor é de 4.19, não use vírgulas apenas pontos para casas decimais, não usar o %.',
			            'name' => 'MOIPV2_CARTAO_PARCEL7',
			          ),
			          array(
			            'type' => 'text',
			            'lang' => false,
			            'label' => $this->l('Valor do juros para a parcela 8'),
			            'desc' => 'Por padrão esse valor é de 4.19, não use vírgulas apenas pontos para casas decimais, não usar o %.',
			            'name' => 'MOIPV2_CARTAO_PARCEL8',
			          ),
			          array(
			            'type' => 'text',
			            'lang' => false,
			            'label' => $this->l('Valor do juros para a parcela 9'),
			            'desc' => 'Por padrão esse valor é de 4.19, não use vírgulas apenas pontos para casas decimais, não usar o %.',
			            'name' => 'MOIPV2_CARTAO_PARCEL9',
			          ),
			          array(
			            'type' => 'text',
			            'lang' => false,
			            'label' => $this->l('Valor do juros para a parcela 10'),
			            'desc' => 'Por padrão esse valor é de 4.19, não use vírgulas apenas pontos para casas decimais, não usar o %.',
			            'name' => 'MOIPV2_CARTAO_PARCEL10',
			          ),
			          array(
			            'type' => 'text',
			            'lang' => false,
			            'label' => $this->l('Valor do juros para a parcela 11'),
			            'desc' => 'Por padrão esse valor é de 4.19, não use vírgulas apenas pontos para casas decimais, não usar o %.',
			            'name' => 'MOIPV2_CARTAO_PARCEL11',
			          ),
			          array(
			            'type' => 'text',
			            'lang' => false,
			            'label' => $this->l('Valor do juros para a parcela 12'),
			            'desc' => 'Por padrão esse valor é de 4.19, não use vírgulas apenas pontos para casas decimais, não usar o %.',
			            'name' => 'MOIPV2_CARTAO_PARCEL12',
			          ),
				),
			
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'btnSubmit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form, $fields_form1, $fields_form2, $fields_form3, $fields_form4,$fields_form5));
	}


	

    public function getOrderIdMoip($json_order) {
                $documento = 'Content-Type: application/json; charset=utf-8';

			
				if(Configuration::get('MOIPV2_ENDPOINT') == 1){
						$url = "https://sandbox.moip.com.br/v2/orders/";
						$oauth = Configuration::get('MOIPV2_OAUTH_DEV');
				} else {
						$url = "https://api.moip.com.br/v2/orders/";
						$oauth = Configuration::get('MOIPV2_OAUTH_PROD');
				}


				$header = "Authorization: OAuth ".$oauth;
                

                $result = array();
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_order);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array($header, $documento));
                curl_setopt($ch,CURLOPT_USERAGENT,'MoipPrestashop/2.0.0');
                $responseBody = curl_exec($ch);
                curl_close($ch);
                $decode = json_decode($responseBody);
                Logger::addLog($responseBody ,1);
                Logger::addLog($json_order ,1);
                
            return $decode;
     }


     public function add_order_state($conf_name, $name, $invoice, $send_email, $color, $unremovable, $logable, $delivery, $template = null)
	{
		$res = true;
		$name_lang = array();
		$template_lang = array();
		foreach (explode('|', $name) AS $item)
		{
			$temp = explode(':', $item);
			$name_lang[$temp[0]] = $temp[1];
		}
		
		if ($template)
			foreach (explode('|', $template) AS $item)
			{
				$temp = explode(':', $item);
				$template_lang[$temp[0]] = $temp[1];
			}
		
		$res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'order_state` (`invoice`, `send_email`, `color`, `unremovable`, `logable`, `delivery`) 
			VALUES ('.(int)$invoice.', '.(int)$send_email.', "'.$color.'", '.(int)$unremovable.', '.(int)$logable.', '.(int)$delivery.')');
		
		$id_order_state = Db::getInstance()->getValue('
			SELECT MAX(`id_order_state`)
			FROM `'._DB_PREFIX_.'order_state`');
		
		$languages = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'lang`');
		foreach ($languages AS $lang)
		{
			$iso_code = $lang['iso_code'];
			$iso_code_name = isset($name_lang[$iso_code])?$iso_code:'br';
			$iso_code_template = isset($template_lang[$iso_code])?$iso_code:'br';
			$name = isset($name_lang[$iso_code]) ? $name_lang[$iso_code] : $name_lang['br'];
			$template = isset($template_lang[$iso_code]) ? $template_lang[$iso_code] : '';
			$res &= Db::getInstance()->execute('
			INSERT IGNORE INTO `'._DB_PREFIX_.'order_state_lang` (`id_lang`, `id_order_state`, `name`, `template`) 
			VALUES ('.(int)$lang['id_lang'].', '.(int)$id_order_state.', "'. $name .'", "'. $template .'")
			');
		}
		
		$exist = Db::getInstance()->getValue('SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE \''.pSQL($conf_name).'\'');
		if ($exist)
			$res &= Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.(int)$id_order_state.'" WHERE `name` LIKE \''.pSQL($conf_name).'\'');
		else
			$res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (name, value) VALUES ("'.pSQL($conf_name).'", "'.(int)$id_order_state.'"');
	}
}
