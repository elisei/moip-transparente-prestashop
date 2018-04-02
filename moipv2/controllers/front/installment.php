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
class Moipv2InstallmentModuleFrontController extends ModuleFrontController
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
		parent::postProcess();
		$max                    = Configuration::get('MOIPV2_CARTAO_NUMBER') + 1;
		$min                    = Configuration::get('MOIPV2_CARTAO_MIN_PARC');
		$MOIPV2_CARTAO_PARCEL2  = Configuration::get('MOIPV2_CARTAO_PARCEL2');
		$MOIPV2_CARTAO_PARCEL3  = Configuration::get('MOIPV2_CARTAO_PARCEL3');
		$MOIPV2_CARTAO_PARCEL4  = Configuration::get('MOIPV2_CARTAO_PARCEL4');
		$MOIPV2_CARTAO_PARCEL5  = Configuration::get('MOIPV2_CARTAO_PARCEL5');
		$MOIPV2_CARTAO_PARCEL6  = Configuration::get('MOIPV2_CARTAO_PARCEL6');
		$MOIPV2_CARTAO_PARCEL7  = Configuration::get('MOIPV2_CARTAO_PARCEL7');
		$MOIPV2_CARTAO_PARCEL8  = Configuration::get('MOIPV2_CARTAO_PARCEL8');
		$MOIPV2_CARTAO_PARCEL9  = Configuration::get('MOIPV2_CARTAO_PARCEL9');
		$MOIPV2_CARTAO_PARCEL10 = Configuration::get('MOIPV2_CARTAO_PARCEL10');
		$MOIPV2_CARTAO_PARCEL11 = Configuration::get('MOIPV2_CARTAO_PARCEL11');
		$MOIPV2_CARTAO_PARCEL12 = Configuration::get('MOIPV2_CARTAO_PARCEL12');
		$Method = "";
		$valor = "";
		$parcela = "";
		$price_order = "";
		$installmentCount = "";
		extract($_GET);
		if ($Method == 'cart') {
			$valor = $price_order;
			$array = array(
				'1' => $this->getParcelas($valor, 0, 1),
				'2' => $this->getParcelas($valor, $MOIPV2_CARTAO_PARCEL2, 2),
				'3' => $this->getParcelas($valor, $MOIPV2_CARTAO_PARCEL3, 3),
				'4' => $this->getParcelas($valor, $MOIPV2_CARTAO_PARCEL4, 4),
				'5' => $this->getParcelas($valor, $MOIPV2_CARTAO_PARCEL5, 5),
				'6' => $this->getParcelas($valor, $MOIPV2_CARTAO_PARCEL6, 6),
				'7' => $this->getParcelas($valor, $MOIPV2_CARTAO_PARCEL7, 7),
				'8' => $this->getParcelas($valor, $MOIPV2_CARTAO_PARCEL8, 8),
				'9' => $this->getParcelas($valor, $MOIPV2_CARTAO_PARCEL9, 9),
				'10' => $this->getParcelas($valor, $MOIPV2_CARTAO_PARCEL10, 10),
				'11' => $this->getParcelas($valor, $MOIPV2_CARTAO_PARCEL11, 11),
				'12' => $this->getParcelas($valor, $MOIPV2_CARTAO_PARCEL12, 12)
			);
			if ($max != 12) {
				while ($max <= 12) {
					unset($array[$max]);
					$max++;
				}
			}
			foreach ($array as $key => $parcela) {
				if ($parcela['parcela'] < $min) {
					unset($array[$key]);
				}
			}
			echo Tools::jsonEncode($array);
		} else {
			$parcela       = $installmentCount;
			$order_ammount = $order_ammount;
			$array         = array(
				'1' => Tools::displayPrice($valor),
				'2' => $this->getJuros($valor, $MOIPV2_CARTAO_PARCEL2, 2),
				'3' => $this->getJuros($valor, $MOIPV2_CARTAO_PARCEL3, 3),
				'4' => $this->getJuros($valor, $MOIPV2_CARTAO_PARCEL4, 4),
				'5' => $this->getJuros($valor, $MOIPV2_CARTAO_PARCEL5, 5),
				'6' => $this->getJuros($valor, $MOIPV2_CARTAO_PARCEL6, 6),
				'7' => $this->getJuros($valor, $MOIPV2_CARTAO_PARCEL7, 7),
				'8' => $this->getJuros($valor, $MOIPV2_CARTAO_PARCEL8, 8),
				'9' => $this->getJuros($valor, $MOIPV2_CARTAO_PARCEL9, 9),
				'10' => $this->getJuros($valor, $MOIPV2_CARTAO_PARCEL10, 10),
				'11' => $this->getJuros($valor, $MOIPV2_CARTAO_PARCEL11, 11),
				'12' => $this->getJuros($valor, $MOIPV2_CARTAO_PARCEL12, 12)
			);
			if ($max != 12) {
				$count = $max + 1;
				while ($count <= 12) {
					unset($array[$count]);
					$count++;
				}
			}
			$installmentAmmount = $array[$parcela];
			echo Tools::jsonEncode($installmentAmmount);
		}
	}
	public function getJurosSimples($valor, $juros, $parcela)
	{
		if ($juros) {
			$taxa      = $juros / 100;
			$principal = $valor * $taxa + $valor;
			$calc      = $principal / $parcela;
		} else {
			$calc = $valor;
		}
		return $calc;
	}
	public function getJurosComposto($valor, $juros, $parcela)
	{
		if ($juros) {
			$principal = $valor;
			$taxa      = $juros / 100;
			$calc      = ($principal * $taxa) / (1 - (pow(1 / (1 + $taxa), $parcela)));
		} else {
			$calc = $valor;
		}
		return $calc;
	}
	public function getJuros($valor, $juros, $parcela)
	{
		$MOIPV2_TYPE_JUROS = Configuration::get('MOIPV2_TYPE_JUROS');
		if ($juros) {
			if ($MOIPV2_TYPE_JUROS == 1) {
				$calc = $this->getJurosSimples($valor, $juros, $parcela);
			} else {
				$calc = $this->getJurosComposto($valor, $juros, $parcela);
			}
		} else {
			$calc = Tools::convertPrice($valor);
		}
		return $calc;
	}
	public function getParcelas($valor, $juros, $parcela)
	{
		$calc              = array();
		$MOIPV2_TYPE_JUROS = Configuration::get('MOIPV2_TYPE_JUROS');
		if ($juros > 0) {
			if ($MOIPV2_TYPE_JUROS == 1) {
				$valor_parc = $this->getJurosSimples($valor, $juros, $parcela);
				$calc       = array(
					'parcela' => $valor_parc,
					'juros' => $juros,
					'total' => Tools::displayPrice($valor_parc * $parcela),
					'parcela_format' => Tools::displayPrice($valor_parc)
				);
				;
			} else {
				$valor_parc = $this->getJurosComposto($valor, $juros, $parcela);
				$calc       = array(
					'parcela' => $valor_parc,
					'juros' => $juros,
					'total' => Tools::displayPrice($valor_parc * $parcela),
					'parcela_format' => Tools::displayPrice($valor_parc)
				);
			}
			return $calc;
		} else {
			$valor_parc = $valor / $parcela;
			$calc       = array(
				'parcela' => $valor_parc,
				'juros' => $juros,
				'total' => Tools::displayPrice($valor),
				'parcela_format' => Tools::displayPrice($valor_parc)
			);
			return $calc;
		}
	}
}
