
{*
* 07-14 PrestaShop
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
*  @copyright  07-14 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<p class="payment_module">    
<form action="{$link->getModuleLink('moipv2', 'authorization', [], true)|escape:'html'}"  class="formulario" id="formulario" method="POST">
	

	 <link rel="stylesheet" type="text/css" href="{$modules_dir}moipv2/script/default.css" />
     <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
     <script type="text/javascript" src="https://assets.moip.com.br/v2/moip.min.js"></script>
     <script type="text/javascript" src="{$modules_dir}moipv2/script/getBin.js"></script>
     <script type="text/javascript" src="{$modules_dir}moipv2/script/jquery.validate.js"></script>
     <script type="text/javascript" src="{$modules_dir}moipv2/script/jquery.maskedinput.js"></script>
     <script type="text/javascript" src="{$modules_dir}moipv2/script/moip.js"></script>
		

        <input type="hidden" name="paymentForm" value="" />
        <input type="hidden" name="paymentMethod" value="{if ($MOIPV2_CARTAO_ACEITE)}CREDIT_CARD{/if}" />
        <input type="hidden" name="paymentBank" value=""/>
       	<input type="hidden" name="paymentHASH" id="paymentHASH" value=""/>
        <input type="hidden" name="paymentUrl" value="{$MOIP_INSTALLMENT|escape:'html'}"/>
        <input type="hidden" name="paymentOrderValue" value="{$orderValueBr}"/>
        
        <textarea id="id-chave-publica" class="chave-publica-moip" style="display:none !important;" autocomplete="off">{$publickey|escape:'html'}</textarea>

    {literal}
        <script type="text/javascript">
            $(document).ready(function(){
                MoipPagamentos();

                calcParcela();
            });
        </script>
    {/literal}
   

<div class="col-md-3">
    <div class="btn-moip-pg" data-toggle="buttons">
    {if ($MOIPV2_CARTAO_ACEITE)}
      <label class="btn btn-default active btn-lg btn-block btn-select-payment-moip" >
        <input type="radio" name="payment" value="CREDIT_CARD" checked /><i class="fa fa-credit-card" aria-hidden="true"></i> Cartão de Crédito
      </label>
    {/if}
    {if $MOIPV2_BOLETO_ACEITE}
      <label class="btn btn-default btn-block btn-lg btn-select-payment-moip" >
           <input type="radio" name="payment" value="BOLETO" /><i class="fa fa-barcode" aria-hidden="true"></i> Boleto Bancário
      </label>
    {/if}
    {if $MOIPV2_TEF_ACEITE}
      <label class="btn btn-default btn-block btn-lg btn-select-payment-moip">
            <input type="radio" name="payment" value="ONLINE_BANK_DEBIT" /><i class="fa fa-money" aria-hidden="true"></i> Transf. Bancária
      </label>
    {/if}
    </div>
</div>
<div class="col-md-9" id="moip-area-pay">
    <div class="escolha payform" id="CREDIT_CARD"  {if ($MOIPV2_CARTAO_ACEITE)} style="display: block;" {/if}>
        <legend><i class="fa fa-credit-card" aria-hidden="true"></i>  - Pagamento com cartão de crédito</legend>
        <div class="row">
            <div class="col-lg-6">
                
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-8">
                            <label class="control-label label-moip-pg" for="cartaoNumero">Número do Cartão</label>
                            <input type="tel" pattern="[0-9]*" minlength="11" maxlength="27" title="Número do seu Cartão de Crédito" id="cartaoNumero" value="" required class="required  form-control" >
                        </div>
                        <div class="col-md-4">
                                <label class="control-label label-moip-pg" for="segurancaNumero">
                                    Cod. de Seg.
                                </label>
                                <input type="tel" pattern="[0-9]*" title="Código de Seguranção do Seu Cartão" id="segurancaNumero" size="4" value="" class="required form-control" maxlength="4" minlength="3">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <label class="control-label label-moip-pg" for="cartaoMes">
                        Data de validade 
                    </label>
                    <div class="row">
                        <div class="col-md-8 col-xs-6">
                            <select name="cartaoMes" id="cartaoMes" class="required form-control" title="Mês de Vencimento do seu Cartão" autocomplete="off">
                                <option value="">Mês</option>
                                <option value="01">01 - Janeiro</option>
                                <option value="02">02 - Fevereiro</option>
                                <option value="03">03 - Março</option>
                                <option value="04">04 - Abril</option>
                                <option value="05">05 - Maio</option>
                                <option value="06">06 - Junho</option>
                                <option value="07">07 - Julho</option>
                                <option value="08">08 - Agosto</option>
                                <option value="09">09 - Setembro</option>
                                <option value="10">10 - Outubro</option>
                                <option value="11">11 - Novembro</option>
                                <option value="12">12 - Dezembro</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-xs-6">
                            <select name="cartaoAno" id="cartaoAno" class="required form-control" title="Ano de Vencimento do seu Cartão" autocomplete="off">
                                <option value="">Ano</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                                <option value="24">24</option>
                                <option value="25">25</option>
                                <option value="26">26</option>
                                <option value="27">27</option>
                                <option value="28">28</option>
                                <option value="29">29</option>
                                <option value="30">30</option>
                                <option value="31">31</option>
                                <option value="32">32</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <label  class="control-label label-moip-pg" for="nomePortador">Nome do titular (exatamente como impresso)</label>
                    <input type="text" name="nomePortador" id="nomePortador" value="" required class="required form-control" />
                </div>
                <div class="col-md-12">
                    <label class="control-label label-moip-pg" for="cpfPortador">CPF</label>
                    <input type="text" name="cpfPortador" id="cpfPortador" value="" required class="required form-control" />
                </div>
                <div class="col-md-12">
                    <label class="control-label label-moip-pg" for="parcelamentoCartao">Parcelas</label>
                    <select name="parcelamentoCartao" id="parcelamentoCartao" class="form-control">
                        <option value="1" label="Pagamento à vista" title="Parcela única de R$ {$orderValueBr}">Pagamento à vista</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <div class="parcelamentoCartao">Parcela única de R$ {$orderValueBr}</div>
                </div>
                <div class="col-md-12">
                    <img src="{$modules_dir}moipv2/images/spinner.gif" class="spinner_moip" alt="Aguarde..."  style="display:none;"/>
                    <button class="exclusive moip-btn btn btn-lg btn-success" id="CREDIT_CARD" name="submit" type="submit">Concluir pedido <i class="icon-chevron-right right"></i></button> 
                </div>   
                <ul id="alert-area"></ul>
            </div>
            <div class="col-lg-6">
                <div class="flip-container">
                    <div class="flipper">
                        <div class="front">
                           <div class="card-info">
                                <div class="card-data-info">
                                    <div class="card-info-number"></div>
                                    <div class="card-info-brand" id="card-info-brand"></div>
                                    <div class="card-info-expiration-value">
                                        <span class="card-info-mes"></span>
                                        <span class="card-info-expiration-break">/</span>
                                        <span class="card-info-ano"></span>
                                    </div>
                                    <div class="card-info-name"></div>
                                </div>
                            </div>
                        </div>
                        <div class="back" >
                           <div class="card-info-back">
                                <div class="card-data-back">
                                    <div class="card-info-cvv"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
       </div>
    </div>

    
      
        <div class="escolha payform" id="BOLETO">
            <div id="div-boleto" class="escolha-side-full">
                <legend><i class="fa fa-barcode" aria-hidden="true"></i>  - Pagamento por boleto bancário</legend>
                {if $MOIPV2_BOLETODISCOUNT}
                    <p>Pague com desconto!</p>
                {/if}
                <p>Você deverá efetuar o pagamento do boleto em até três (3) dias após sua impressão.</p>
                <img src="{$modules_dir}moipv2/images/spinner.gif" class="spinner_moip" alt="Aguarde..."  style="display:none;" />
              <button type="submit" class="exclusive moip-btn  btn btn-lg btn-success" id="BRADESCO">Concluir pedido <i class="icon-chevron-right right"></i></button>
                

            </div>

        </div>
   
        
        <div class="escolha payform" id="ONLINE_BANK_DEBIT">
            <div id="div-debito" class="escolha-side-full">
                <legend><i class="fa fa-money" aria-hidden="true"></i>  - Pagamento por transferência bancária</legend>
                <p>Parcela única de R$ {$orderValueBr}  </p>
                <p>Você será redirecionado ao site de seu banco para concluir o pagamento.</p>
                <img src="{$modules_dir}moipv2/images/spinner.gif" class="spinner_moip" alt="Aguarde..." style="display:none;" />
                <button class="exclusive moip-btn btn btn-lg btn-success" id="ONLINE_BANK_DEBIT" name="submit" type="submit">Concluir pedido <i class="icon-chevron-right right"></i></button>
            </div>
        </div>
    
    
  
</div>

</form>
</p>
