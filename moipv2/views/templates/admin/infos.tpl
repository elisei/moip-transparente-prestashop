{*
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
*
*  @author MOIP DEVS - <prestashop@moip.com.br>
*  @copyright  2017-2018 Moip Wirecard Brasil
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<script type="text/javascript" src="../modules/moipv2/views/js/admin/admin.js"></script>
	<div class="container">
		<div class="jumbotron">
			<h1>Moip Wirecard Brasil</h1>
			<div class="ambiente">
				<p>
					{if $ambiente == "producao"}
						<span class="small">Ambiente de Produção</span>
					{else}
						<span class="small">Ambiente de Teste</span>
					{/if}
				</p>
			</div>
			<div class="typereceipt">
				<p>
					Tipo de recebimento:
						<span class="type">
							{if $MOIPV2_TYPE_RECEIPT == "1"}
							Receber em 2 dias
							{elseif $MOIPV2_TYPE_RECEIPT == "2"}
							Receber em 14 dias
							{elseif $MOIPV2_TYPE_RECEIPT == "3"}
							Receber em 30 dias
							{elseif $MOIPV2_TYPE_RECEIPT == "4"}
							Recebimento definido pela conta Moip
							{else}
							Não há configuração de recibimento ainda.
							{/if}
						</span>
				</p>
			</div>
			{if $state_prod != 'inativo'}
				<div class="status">
					Seu aplicativo está {$state_prod|escape:'htmlall':'UTF-8'}
				</div>
			{/if}
			<div class="oauth">
					{if $state_prod == 'inativo'}
						<a href="{$url_process|escape:'htmlall':'UTF-8'}" class="btn btn-primary">
							Autorizar Aplicativo
						</a>
					{/if}
			</div>
			
		</div>	
	</div>
<hr>
<script type="text/javascript">
	jQuery( window ).load(function() {
		ChangeType();
	});
</script>