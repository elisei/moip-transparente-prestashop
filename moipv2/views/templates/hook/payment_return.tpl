		
		{if $paymentMethod == 'CREDIT_CARD'}
		<h2>{l s='Seu pedido foi recebido e o status dele é' mod='moipv2'}:
			{if $moip_status == 'CANCELLED'}
				{l s='Cancelado' mod='moipv2'}
			{elseif $moip_status == 'IN_ANALYSIS'}
				{l s='Em análise' mod='moipv2'}
			{elseif $moip_status == 'AUTHORIZED'}
				{l s='Autorizado' mod='moipv2'}
			{elseif $moip_status == 'WAITING'}
				{l s='Aguardando Confirmação' mod='moipv2'}
			{else}
				{l s='Em análise' mod='moipv2'}
			{/if}
		</h2>

		{if $moip_status == 'CANCELLED'}
			<p class="motivo-cancelamento">
				{if $code_id_payment == 'id_1'}
					{l s='Dados informados inválidos. Você digitou algo errado durante o preenchimento dos dados do seu Cartão. Certifique-se de que está usando o Cartão correto e faça uma nova tentativa.' mod='moipv2'}
				{elseif $code_id_payment == 'id_2'}
					{l s='Houve uma falha de comunicação com o Banco Emissor do seu Cartão, tente novamente.' mod='moipv2'}
				{elseif $code_id_payment == 'id_3'}
					{l s='O pagamento não foi autorizado pelo Banco Emissor do seu Cartão. Entre em contato com o Banco para entender o motivo e refazer o pagamento.' mod='moipv2'}
				{elseif $code_id_payment == 'id_4'}
					{l s='A validade do seu Cartão expirou. Escolha outra forma de pagamento para concluir o pagamento.' mod='moipv2'}
				{elseif $code_id_payment == 'id_5'}
					{l s='O pagamento não foi autorizado. Entre em contato com o Banco Emissor do seu Cartão.' mod='moipv2'}
				{elseif $code_id_payment == 'id_6'}
					{l s='Esse pagamento já foi realizado. Caso não encontre nenhuma referência ao pagamento anterior, por favor entre em contato com o nosso Atendimento.' mod='moipv2'}
				{elseif $code_id_payment == 'id_7'}
					{l s='O pagamento não foi autorizado. Para mais informações, entre em contato com o nosso atendimento' mod='moipv2'}
				{elseif $code_id_payment == 'id_8'}
					{l s='O Comprador solicitou o cancelamento da transação diretamente ao Moip. Entre em contato com o Comprador para entender o ocorrido.' mod='moipv2'}
				{elseif $code_id_payment == 'id_9'}
					{l s='O Vendedor solicitou o cancelamento da transação diretamente ao Moip.' mod='moipv2'}
				{elseif $code_id_payment == 'id_10'}
					{l s='O pagamento não pode ser processado. Por favor, tente novamente. Caso o erro persista, entre em contato com o nosso atendimento.' mod='moipv2'}
				{elseif $code_id_payment == 'id_11'}
					{l s='Houve uma falha de comunicação com o Banco Emissor do seu Cartão, tente novamente.' mod='moipv2'}
				{elseif $code_id_payment == 'id_12'}
					{l s='Pagamento não autorizado para este Cartão. Entre em contato com o Banco Emissor para mais esclarecimentos.' mod='moipv2'}
				{elseif $code_id_payment == 'id_12'}
					{l s='Pagamento não autorizado. Entre em contato com o Atendimento e informe o ocorrido.' mod='moipv2'}
				{elseif $code_id_payment == 'id_13'}
					{l s='Pagamento não autorizado.' mod='moipv2'}
				{else}
					{l s='O pagamento não foi autorizado pelo Banco Emissor do seu Cartão. Entre em contato com o Banco para entender o motivo e refazer o pagamento.' mod='moipv2'}
				{/if}
			</p>
			<p>
				<div class="reorder">
					<a href="{$link->getPageLink('order-opc', true, NULL, "submitReorder=&id_order={{$id_order}}")|escape:'html':'UTF-8'}" class="button btn btn-default button-medium "><span>{l s='Refazer pedido' mod='moipv2'}<i class="icon-chevron-right right"></i></span></a>
				</div>
			</p>
			
		{/if}

		{elseif $paymentMethod == 'BOLETO'}
			

			<p>
				Boleto gerado sucesso, o código de barra do seu boleto é: 
			</p>
			<p>
				<strong>{{$code_line}}</strong>
			</p>
			<p>
				Ou se preferir:	
				<a href="{$redirectURI}/print" class="btn btn-success">Imprimir Boleto</a>
					
			</p>
		{elseif $paymentMethod == 'ONLINE_BANK_DEBIT'}
				<p>Para realizar o pagamento você será direcionado ao ambiente do banco.</p>
				<p><a href="{$redirectURI}" target="_blank" class="btnPrint button wide card-done left ">Clique aqui para ir ao banco </a></p>

			{else}

		{/if}
		{if $moip_status != 'CANCELLED'}
			{if !isset($reference)}
				<p>{l s='Não esqueça de anotar o número do seu pedido #%d.' sprintf=$id_order mod='moipv2'}</p>
			{else}
				<p>{l s='Não esqueça de anotar o número do seu pedido %s.' sprintf=$reference mod='moipv2'}</p>
			{/if}
		{/if}
		<p>{l s='Caso tenha alguma dúvida entre em contato conosco' mod='moipv2'} <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='por nosso SAC.' mod='moipv2'}</a></p>
	





				