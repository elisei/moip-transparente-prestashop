(function($) {
MoipOauth = function(){
	
	$('.modal').on('hidden.bs.modal', function () {
		$("#moip_page").attr("src","https://conta.moip.com.br/sair");
    	location.reload();
	})
};
update_iframe = function(){
	$(".progress-striped").fadeOut('active');
	$("#moip_page").fadeIn('active');
}
})(jQuery);
