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

class Moipv2AuthorizationModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;
    public function __construct($response = array())
    {
        parent::__construct($response);
        $this->display_header            = false;
        $this->display_header_javascript = false;
        $this->display_footer            = false;
    }
    public function generateRuleMoip($code)
    {
        $cartRule = new CartRule(CartRule::getIdByCode($code));
        $this->context->cart->addCartRule($cartRule->id);
        return $this;
    }
    public function initContent()
    {
        $paymentMethod = "";
        $erros         = "";
        parent::initContent();
        extract($_POST);
        $moipv2 = new Moipv2();
        if (($paymentMethod == 'BOLETO' || $paymentMethod == 'ONLINE_BANK_DEBIT') && Configuration::get('MOIPV2_BOLETODISCOUNT') == 1) {
            $this->generateRuleMoip(Configuration::get('MOIPV2_CUPOMBOLETO'));
        }
        $json_order = $this->getOrderCreateMoip($_POST);
        $moip_order = $moipv2->getOrderIdMoip($json_order);
        if (isset($moip_order->id)) {
            $json = $this->getPaymentJson($_POST);
        }
        $cart = $this->context->cart;
        if (!$this->module->checkCurrency($cart))
            Tools::redirect('index.php?controller=order');
        if (isset($moip_order->id)) {
            $cart = $this->context->cart;
            if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
                Tools::redirect('index.php?controller=order&step=1');
            $authorized = false;
            foreach (Module::getPaymentModules() as $module)
                if ($module['name'] == 'moipv2') {
                    $authorized = true;
                    break;
                }
            if (!$authorized)
                die($this->module->l('This payment method is not available.', 'validation'));
            $customer = new Customer($cart->id_customer);
            if (!Validate::isLoadedObject($customer))
                Tools::redirect('index.php?controller=order&step=1');
            $currency = $this->context->currency;
            $total    = (float) $cart->getOrderTotal(true, Cart::BOTH);
            if ($paymentMethod == "BOLETO") {
                $PAY_MOIP      = $this->getPaymentMoIP($moip_order->id, $json);
                $moip_pay      = $PAY_MOIP->id;
                $moip_order_id = $moip_order->id;
                $mailVars      = array(
                    '{method_moip_pay}' => "BOLETO",
                    '{moipv2_name}' => 'Pagamento por Boleto Bancário',
                    '{moipv2_link}' => (string) $PAY_MOIP->_links->payBoleto->printHref
                );
                $message       = "Pedido Moip " . $moip_order_id . "
                    Pagamento Moip " . $moip_pay . "
                    Link para 2ª via: " . (string) $PAY_MOIP->_links->payBoleto->printHref;
                $this->module->validateOrder((int) $cart->id, Configuration::get('MOIPV2_STATUS_2'), $total, "Boleto Bancário ", $message, $mailVars, (int) $currency->id, null, $customer->secure_key);
                Tools::redirect('index.php?controller=order-confirmation&id_cart=' . (int) $cart->id . '&id_module=' . (int) $this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key . '&moip_key=' . $moip_pay . '&paymentMethod=BOLETO&moip_status=' . $PAY_MOIP->status . '&redirectURI=' . urlencode($PAY_MOIP->_links->payBoleto->printHref) . '&code_line=' . $PAY_MOIP->fundingInstrument->boleto->lineCode);
            } elseif ($paymentMethod == "ONLINE_BANK_DEBIT") {
                $PAY_MOIP      = $this->getPaymentMoIP($moip_order->id, $json);
                $moip_order_id = $moip_order->id;
                $bank_url      = $PAY_MOIP->_links->payOnlineBankDebitItau->redirectHref;
                $moip_pay      = $PAY_MOIP->id;
                $mailVars      = array(
                    '{method_moip_pay}' => "ONLINE_BANK_DEBIT",
                    '{moipv2_name}' => 'Transferência Bancária',
                    '{moipv2_link}' => $bank_url
                );
                $message       = "Pedido Moip " . $moip_order_id . "
                    Pagamento Moip " . $moip_pay . "
                    Link para o pagamento: " . (string) $PAY_MOIP->_links->payOnlineBankDebitItau->redirectHref;
                $this->module->validateOrder((int) $cart->id, Configuration::get('MOIPV2_STATUS_2'), $total, "Transferência Bancária", $message, $mailVars, (int) $currency->id, false, $customer->secure_key);
                Tools::redirect('index.php?controller=order-confirmation&id_cart=' . (int) $cart->id . '&id_module=' . (int) $this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key . '&moip_key=' . $moip_pay . '&paymentMethod=ONLINE_BANK_DEBIT&moip_status=' . $PAY_MOIP->status . '&redirectURI=' . urlencode($bank_url));
            } elseif ($paymentMethod == "CREDIT_CARD") {
                $PAY_MOIP   = $this->getPaymentMoIP($moip_order->id, $json);
                $code_error = "35598";
                if ($PAY_MOIP->status == "CANCELLED") {
                    if (!is_null($PAY_MOIP->cancellationDetails)) {
                        $code_error = $PAY_MOIP->cancellationDetails->code;
                    }
                }
                $moip_status           = $PAY_MOIP->status;
                $moip_pay              = $PAY_MOIP->id;
                $moip_total            = $PAY_MOIP->amount->total/100;
                $moip_order_id         = $moip_order->id;
                $moip_installmentCount = $PAY_MOIP->installmentCount;
                $moip_card_first6      = $PAY_MOIP->fundingInstrument->creditCard->first6;
                $moip_card_last4       = $PAY_MOIP->fundingInstrument->creditCard->last4;
                $moip_card_birthdate   = $PAY_MOIP->fundingInstrument->creditCard->holder->birthdate;
                $moip_card_taxDocument = $PAY_MOIP->fundingInstrument->creditCard->holder->taxDocument->number;
                $moip_card_fullname    = $PAY_MOIP->fundingInstrument->creditCard->holder->fullname;
                $mailVars              = array(
                    '{method_moip_pay}' => "CREDIT_CARD",
                    '{moipv2_name}' => 'Cartão de Crédito',
                    '{moip_number}' => $moip_card_first6 . 'xxxx' . $moip_card_last4,
                    '{moip_card_fullname}' => $moip_card_fullname,
                    '{moip_card_installment}' => $moip_installmentCount,
                    '{moip_total}' => Tools::convertPrice($moip_total)
                );
                
                $message = "Pedido Moip " . $moip_order_id . "
                    Pagamento Moip " . $moip_pay . "
                    Número de Parcelas " . $moip_installmentCount . "
                    Nome Impresso " . $moip_card_fullname . "
                    Número do Cartão " . $moip_card_first6 . "xxxx" . $moip_card_last4;
                
                $this->module->validateOrder((int) $cart->id, Configuration::get('MOIPV2_STATUS_3'), $total, "Cartão de Crédito", $message, $mailVars, (int) $currency->id, false, $customer->secure_key);
                
                
                
                Tools::redirect('index.php?controller=order-confirmation&id_cart=' . (int) $cart->id . '&id_module=' . (int) $this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key . '&moip_key=' . $moip_pay . '&paymentMethod=CREDIT_CARD&moip_status=' . (string) $moip_status . '&code_id_payment=id_' . (int) $code_error);
                
            }
        } else {
            $erro_order = "";
            foreach ($moip_order->errors as $value) {
                $erro_order .= $value->description;
            }
            $this->context->smarty->assign(array(
                'nbProducts' => $cart->nbProducts(),
                'method_moip_pay' => 'ERRO',
                'erro_message' => $erro_order,
                'cust_currency' => $cart->id_currency,
                'currencies' => $this->module->getCurrency((int) $cart->id_currency),
                'total' => $cart->getOrderTotal(true, Cart::BOTH),
                'isoCode' => $this->context->language->iso_code,
                'moipv2Name' => $this->module->moipv2Name,
                'this_path' => $this->module->getPathUri(),
                'this_path_moipv2' => $this->module->getPathUri(),
                'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->module->name . '/'
            ));
            $this->setTemplate('payment_execution.tpl');
        }
    }
    public function getPaymentJson($pagamento)
    {
        $paymentMethod      = "";
        $parcelamentoCartao = "";
        $paymentHASH        = "";
        $nomePortador       = "";
        $dataPortador       = "";
        $cpfPortador        = "";
        extract($pagamento);
        if ($paymentMethod == "BOLETO") {
            $array_payment = array(
                "fundingInstrument" => array(
                    "method" => "BOLETO",
                    "boleto" => array(
                        "expirationDate" => $this->getDataVencimento(Configuration::get('MOIPV2_BOLETO_DATE')),
                        "instructionLines" => array(
                            "first" => Configuration::get('MOIPV2_BOLETO_INST_1'),
                            "second" => Configuration::get('MOIPV2_BOLETO_INST_2'),
                            "third" => Configuration::get('MOIPV2_BOLETO_INST_3')
                        )
                    )
                )
            );
        } elseif ($paymentMethod == "ONLINE_BANK_DEBIT") {
            $array_payment = array(
                "fundingInstrument" => array(
                    "method" => "ONLINE_BANK_DEBIT",
                    "onlineBankDebit" => array(
                        "bankNumber" => '341',
                        "expirationDate" => $this->getDataVencimento(3),
                        "returnUri" => "https://"
                    )
                )
            );
        } else {
            $address       = new Address($this->context->cart->id_address_invoice);
            $array_payment = array(
                "installmentCount" => $parcelamentoCartao,
                "fundingInstrument" => array(
                    "method" => "CREDIT_CARD",
                    "creditCard" => array(
                        "hash" => $paymentHASH,
                        "holder" => array(
                            "fullname" => $nomePortador,
                            "birthdate" => date('Y-m-d', strtotime($dataPortador)),
                            "taxDocument" => array(
                                "type" => "CPF",
                                "number" => preg_replace("/[^0-9]/", "", $cpfPortador)
                            ),
                            "phone" => array(
                                "countryCode" => "55",
                                "areaCode" => $this->getNumberOrDDD($address->phone, true),
                                "number" => $this->getNumberOrDDD($address->phone)
                            )
                        )
                    )
                )
            );
        }
        $json = json_encode($array_payment);
        return $json;
    }
    public function getNumberOrDDD($param_telefone, $param_ddd = false)
    {
        $cust_ddd       = '11';
        $cust_telephone = preg_replace("/[^0-9]/", "", $param_telefone);
        $st             = Tools::strlen($cust_telephone) - 8;
        if ($st > 0) {
            $cust_ddd       = Tools::substr($cust_telephone, 0, 2);
            $cust_telephone = Tools::substr($cust_telephone, $st, 8);
        }
        if ($param_ddd === false) {
            $retorno = $cust_telephone;
        } else {
            $retorno = $cust_ddd;
        }
        return $retorno;
    }
    public function getDataVencimento($NDias)
    {
        $NDias   = "+" . $NDias . " days";
        $NewDate = Date('Y-m-d', strtotime($NDias));
        return $NewDate;
    }
    public function getPaymentMoIP($IdMoip, $json)
    {
        
        $documento = 'Content-Type: application/json; charset=utf-8';
        if (Configuration::get('MOIPV2_ENDPOINT') == 1) {
            $url   = "https://sandbox.moip.com.br/v2/orders/{$IdMoip}/payments";
            $oauth = Configuration::get('MOIPV2_OAUTH_DEV');
        } else {
            $url   = "https://api.moip.com.br/v2/orders/{$IdMoip}/payments";
            $oauth = Configuration::get('MOIPV2_OAUTH_PROD');
        }
        Logger::addLog($url, 1);
        $header = "Authorization: OAuth " . $oauth;
        Logger::addLog($header, 1);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            $header,
            $documento
        ));
        curl_setopt($ch, CURLOPT_USERAGENT, 'MoipPrestashop/2.0.0');
        $responseBody = curl_exec($ch);
        curl_close($ch);
        Logger::addLog($responseBody, 1);
        $decode = json_decode($responseBody);
        return $decode;
    }
    public function getOrderCreateMoip($dado_payment)
    {
        $paymentMethod      = "";
        $parcelamentoCartao = "";
        extract($dado_payment);
        
        $cart_itens     = $this->context->cart->getProducts();
        $produc_itens   = $this->getListaProdutos($cart_itens);
        $shipping_price = $this->context->cart->getTotalShippingCost(null, true);
        $total_order    = $this->context->cart->getOrderTotal(true, Cart::BOTH);
        $price_ajust    = $this->autidaOrder($cart_itens, $shipping_price, $total_order);
        if ($paymentMethod == "CREDIT_CARD") {
            if ($price_ajust > 0) {
                $valor_juros_parcela = $this->getValueParc($this->context->cart->getOrderTotal(true, Cart::BOTH), $parcelamentoCartao);
                $addtion             = $price_ajust + $valor_juros_parcela;
                if ($price_ajust > 0) {
                    $addtion  = $addtion;
                    $discount = 000;
                } else {
                    $addtion  = $addtion;
                    $discount = $price_ajust;
                }
            } else {
                $valor_juros_parcela = $this->getValueParc($this->context->cart->getOrderTotal(true, Cart::BOTH), $parcelamentoCartao);
                $addtion             = $valor_juros_parcela;
                $discount            = $price_ajust;
            }
        } else {
            if ($price_ajust > 0) {
                $addtion  = $price_ajust;
                $discount = 000;
            } else {
                $addtion  = 000;
                $discount = $price_ajust;
            }
        }
        $address  = new Address($this->context->cart->id_address_invoice);
        $customer = new Customer(Context::getContext()->cookie->id_customer);
        // INICIO - definição do atributo para o documento cpf...  altere caso necessário para o seu atributo.
        $taxvat   = $customer->cpf_cnpj;
        if (!$taxvat) {
            if (isset($customer->document)) {
                $taxvat = $customer->document;
            } elseif (isset($address->vat_number)) {
                $taxvat = $address->vat_number;
            } else {
                $taxvat = '000.000.000-00';
            }
        }
        $taxvat = preg_replace("/[^0-9]/", "", $taxvat);
        if (Tools::strlen($taxvat) > 11) {
            $name_persona  = $address->company;
            $document_type = "CNPJ";
        } else {
            $name_persona  = $address->firstname . ' ' . $address->lastname;
            $document_type = "CPF";
        }
        // FIM - definição do atributo para o documento cpf...  altere caso necessário para o seu atributo.
        $prestashopState = new State($address->id_state);
        $addressUF       = $prestashopState->iso_code;
        /* var_dump($this->context->cart);
        die();*/
        $array_order     = array(
            "ownId" => (int) $this->context->cart->id,
            "amount" => array(
                "currency" => "BRL",
                "subtotals" => array(
                    "shipping" => number_format($shipping_price, 2, '', ''),
                    "discount" => abs($discount),
                    "addition" => $addtion
                )
            ),
            "items" => $produc_itens,
            "customer" => array(
                "ownId" => $customer->email,
                "fullname" => $name_persona,
                "email" => $customer->email,
                "birthDate" => '1980-10-10',
                "taxDocument" => array(
                    "type" => $document_type,
                    "number" => $taxvat
                ),
                "phone" => array(
                    "countryCode" => "55",
                    "areaCode" => $this->getNumberOrDDD($address->phone, true),
                    "number" => $this->getNumberOrDDD($address->phone)
                ),
                "shippingAddress" => array(
                    "street" => $address->address1,
                    "streetNumber" => $this->getNumEndereco($address->address1),
                    "complement" => $this->getNumEndereco($address->address2),
                    "district" => $address->address2,
                    "city" => $address->city,
                    "state" => $addressUF,
                    "country" => "BRA",
                    "zipCode" => $address->postcode
                )
            )
        );
        $json_order      = json_encode($array_order);
        return $json_order;
    }
    public function encryptIt($q)
    {
        if (Configuration::get('MOIPV2_ENDPOINT') == 1) {
            $oauth = Configuration::get('MOIPV2_OAUTH_DEV');
        } else {
            $oauth = Configuration::get('MOIPV2_OAUTH_PROD');
        }
        $qEncoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($oauth), $q, MCRYPT_MODE_CBC, md5(md5($oauth))));
        return $qEncoded;
    }
    public function getValueParc($valor, $parcela)
    {
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
        $array                  = array(
            '1' => 0,
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
        $installmentAmmount     = $array[$parcela];
        return $installmentAmmount;
    }
    public function getJurosSimples($valor, $juros)
    {
        if ($juros != 0) {
            $taxa        = $juros / 100;
            $principal   = $valor * $taxa + $valor;
            $juros_total = $principal - $valor;
            $calc        = Tools::convertPrice($juros_total);
        } else {
            $calc = $valor;
        }
        return $calc;
    }
    public function getJurosComposto($valor, $juros, $parcela)
    {
        if ($juros != 0) {
            $valjuros    = "";
            $principal   = $valor;
            $taxa        = $juros / 100;
            $valjuros    = ($principal * $taxa) / (1 - (pow(1 / (1 + $taxa), $parcela)));
            $juros_total = ($valjuros * $parcela) - $principal;
            $calc        = Tools::convertPrice($juros_total);
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
                $calc = $this->getJurosSimples($valor, $juros);
            } else {
                $calc = $this->getJurosComposto($valor, $juros, $parcela);
            }
        } else {
            $calc = 0;
        }
        return number_format($calc, 2, '', '');
    }
    public function getListaProdutos($cart_itens)
    {
        $produtos = array();
        foreach ($cart_itens as $item) {
            if ($item['total'] > 0) {
                $produtos[] = array(
                    'product' => $item['name'],
                    'quantity' => 1,
                    'detail' => $item['reference'],
                    'price' => number_format($item['total'], 2, '', '')
                );
            }
        }
        return $produtos;
    }
    public function getNumEndereco($endereco)
    {
        $numEnderecoDefault = '0';
        $numEndereco        = trim(preg_replace("/[^0-9]/", "", $endereco));
        if ($numEndereco)
            return ($numEndereco);
        else
            return ($numEnderecoDefault);
    }
    public function getPrestaShopState($id_state)
    {
        $rq = Db::getInstance()->getRow('
        SELECT `name`, `iso_code` FROM `' . _DB_PREFIX_ . 'state`
        WHERE id_state = \'' . pSQL($id_state) . '\'');
        return $rq;
    }
    public function autidaOrder($produc_itens, $shipping_price, $total_order)
    {
        $price_prod = array();
        foreach ($produc_itens as $item) {
            if ($item['total'] > 0) {
                $price_prod[] = $item['total'];
            }
        }
        $preco_prods   = array_sum($price_prod);
        $prod_shipping = $preco_prods + $shipping_price;
        if ($prod_shipping != $total_order) {
            if ($total_order > $prod_shipping) {
                $addition_price = $total_order - $prod_shipping;
                return number_format($addition_price, 2, '', '');
            } else {
                $discount_order = $total_order - $prod_shipping;
                return number_format($discount_order, 2, '', '');
            }
        }
    }
}
