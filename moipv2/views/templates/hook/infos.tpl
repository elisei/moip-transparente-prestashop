{*
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
*}
 <script type="text/javascript" src="../modules/moipv2/script/admin.js"></script>
<div class="alert alert-info">


<img src="../modules/moipv2/moipv2.png" style="float:left; margin-right:15px;" height="49">


<p><strong>MOIP PAGAMENTOS - V2</strong></p>
<p>Para configurar o seu módulo você deve selecionar um dos ambientes e após logar-se no moip você estará hapto a realizar suas transações.</p>
	<hr>
		

				
				<table class="moip-table">
					<caption>MOIP Pagamento - Configuração de Pagamento</caption>
					<thead>
					<tr >
						<th width="50%">
						Produção - Ambiente de compras reais 
							<span>
								<a href="https://conta.moip.com.br/">Moip Produção</a>
							</span>
						</th>
						<th width="50%" >
							Sandbox - Ambiente destinado a testes 
							<span>
								<a href="https://conta-sandbox.moip.com.br/">Moip Sandbox</a>
							</span>
						</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td class="{$state_prod}">Status para vendas reais em Ambiente de Produção: {$state_prod}</td>
						<td class="{$state_dev}">Status para vendas testes em Ambiente de Sandbox: {$state_dev}</td>
					</tr>
					<tr>
						<td>
							{if $ambiente == "producao"}
							<div> <a href="{$url_process}" class="btn btn-primary" >Autorizar Moip em Produção</a></div>
							{else}
								<p>Você selecinou o ambiente de testes, para configrar o ambiente de produção altere e salve o 	AMBIENTE DE COMUNICAÇÃO</p>
							{/if}
						</td>
						<td>
							{if $ambiente == "sandbox"}
								<div> <a href="{$url_process}" class="btn btn-primary" >Autorizar Moip em Sandbox</a></div>
							{else}
								<p>Você selecinou o ambiente de produção, para configrar o ambiente de teste altere e salve o 	AMBIENTE DE COMUNICAÇÃO</p>
							{/if}
						</td>
					</tr>
					
					</tbody>
				</table>
						
		


</div>


<style>
	caption {
	  font-size: 24px;
	  padding-bottom: 15px;
	}
	.moip-table {
		border:1px solid #C0C0C0;
		border-collapse:collapse;
		padding:5px;
	}
	.moip-table th {
		border:1px solid #C0C0C0;
		padding:5px;
		background:#F0F0F0;
	}
	.moip-table td {
		border:1px solid #C0C0C0;
		padding:5px;
	}
	td.ativo {
	  background-color: #8BC954;
	  color: #fff;
	  font-weight: bold;
	}
	td.inativo {
	  background-color: #D04437;
	  color: #fff;
	  font-weight: bold;
	}
	table.moip-table {
	  width: 100%;
	}
	.onclick{
		display: none;
		color: red;
		font-size: 34px;
	}
</style>