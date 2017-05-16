(function($) {
MoipPagamentos = function(){
    datacardinfo("all", null);
    
    $("#cpfPortador").mask("999.999.999-99");
    $('#cartaoNumero').on("paste keyup", function() {
        card_data =  $("#cartaoNumero").val();
        card = cardType(card_data, true);
        
        if(card.brand){
            setMaskForBrand(card.brand);
            datacardinfo("cardBrand", card.brand);
        } else{
            datacardinfo("cardBrand", null);
            $("#cartaoNumero").mask("999999999999999999999999");
        }
        datacardinfo("cardNumber", card_data);
        $('input:hidden[name=paymentForm]').val(card.brand);
    });

    $('#nomePortador').on("paste keyup", function() {
        card_data_name =  $("#nomePortador").val();
        datacardinfo("cardName", card_data_name);
    });

    $('#cartaoMes').on("paste keyup change", function() {
        card_data_mes =  $("#cartaoMes").val();
        datacardinfo("cardMes", card_data_mes);
        
    });

    $('#cartaoAno').on("paste keyup change", function() {
        card_data_ano =  $("#cartaoAno").val();
        datacardinfo("cardAno", card_data_ano);
    });

    $('#segurancaNumero').on("paste keyup", function() {
         card_data_cvv =  $("#segurancaNumero").val();
         datacardinfo("cardCvv", card_data_cvv);
    });

    $('#segurancaNumero').on("focusin", function() {
        $(".flip-container").addClass('hover');
    });

    $('#segurancaNumero').on("focusout", function() {
        $(".flip-container").removeClass('hover');
    });
    
    $('input[name="payment"]').change(function(){
        var form_id = this.value;
        $('.escolha:visible').fadeOut('fast');
        $('#' + form_id).fadeIn('fast');
        $('input:radio[name=payment]').each(function() {
            if ($(this).is(':checked'))
               var paymentForm = $(this).attr('id');
        });        
        $("input:hidden[name=paymentMethod]").val(form_id);
    });

    $('select[name=parcelamentoCartao]').click(function(){
        parcelamentoCartao = $("select[name=parcelamentoCartao]").find('option').filter(':selected').attr('title');
        $(".parcelamentoCartao").text(parcelamentoCartao);
    });
     
    $('.moip-btn').click(function() {
       
        disableButton();
        paymentMethod = $(this).attr('id');
            
        if(paymentMethod == 'CREDIT_CARD'){
            
            var cc = new Moip.CreditCard({
                        number  : $("#cartaoNumero").val(),
                        cvc     : $("#segurancaNumero").val(),
                        expMonth: $("#cartaoMes").val(),
                        expYear : $("#cartaoAno").val(),
                        pubKey  : $("#id-chave-publica").val()
                    });
             
            if(cc.isValid()){
              $("#paymentHASH").val(cc.hash());
            }
            else{
                
                $("#paymentHASH").val('');
            }
            $(".formulario").validate({
                rules : {
                    
                    paymentHASH : {
                        required : true
                    },
                    nomePortador: {
                        required : true
                    },
                   
                    cpfPortador: {
                        required : true
                    }
                },
                messages : {
                    paymentHASH: "Dados de cartão inválido, verifique os dados e tente novamente",
                    cartaoNumero: "Informe o número do cartão de crédito corretamente",
                    segurancaNumero: "Preencha o código de segurança",
                    cartaoMes: "Preencha o mês de vencimento do cartão",
                    cartaoAno: "Preencha o ano de vencimento do cartão",
                   
                    nomePortador : "Preencha o nome do titular do cartão",
                    
                    cpfPortador : "Preencha o CPF do titular do cartão (<i>Ex. 111.111.111-11</i>)"
                },
                errorClass: "validate_erro",
                errorElement: "li",
                ignore: ".ignore",
                errorLabelContainer: "#alert-area",
                invalidHandler: function(){
                     enableButton();
                },
                submitHandler: function() {
                     $("#"+paymentMethod).submit();
                },
            });

            
        }else {
            
            $(".formulario").validate({
                ignore: ".required",
                submitHandler: function() {
                  $("#"+paymentMethod).submit();
                }
            });
        }
    });
}

calcParcela = function(){
    var url_mod_dir = $("input:hidden[name=paymentUrl]").val();
    var ammount_calc = $("input:hidden[name=paymentOrderValue]").val();
    $.ajax({
              method: "GET",
              url: url_mod_dir+"?fc=module&module=moipv2&controller=installment",
              data: { Method: "cart", price_order: ammount_calc}
            }).done(function( data ) {
               response = $.parseJSON(data);
               $.each(response, function(key, value){
                    $('#parcelamentoCartao').append('<option value="' + key + '" label="' + key + ' x ' + value + '" title="Parcelado em ' + key + ' x ' + value + '" class="pagamentoParcelado">' + key + ' x ' + value + '</option>');
               })
    });
}


setMaskForBrand = function(card){
  
        if(card == "AMEX"){
            $("#cartaoNumero").mask("9999 999999 99999");
            $("#segurancaNumero").mask("9999");
        }else if(card == "DINERS"){
            
            $("#cartaoNumero").mask("9999 999999 999")
            $("#segurancaNumero").mask("999");
        }else if (card == "HIPERCARD"){
             
            $("#cartaoNumero").mask("9999 9999 9999 9999");   
            $("#segurancaNumero").mask("999");
        }else if (card == "MASTERCARD"){
            
            $("#cartaoNumero").mask("9999 9999 9999 9999");
            $("#segurancaNumero").mask("999");
        }else if (card == "ELO"){
            
            $("#cartaoNumero").mask("9999 9999 9999 9999"); 
            $("#segurancaNumero").mask("999");
        }else if (card == "HIPER"){
            
            $("#cartaoNumero").mask("9999 9999 9999 9999"); 
            $("#segurancaNumero").mask("999");
        }else if (card == "VISA"){
            
            $("#cartaoNumero").mask("9999 9999 9999 9999");
            $("#segurancaNumero").mask("999");
        }else{
            $("#cartaoNumero").mask("999999999999999999999999");
        }
}

disableButton = function(){
        $(".moip-btn").fadeOut();
        $(".spinner_moip").fadeIn();    
}

enableButton = function(){
    $(".moip-btn").fadeIn();
    $(".spinner_moip").fadeOut();
}

datacardinfo = function(field, value){
  
    if(field == "cardNumber"){
        if(value.length == 0){
         
         $('.card-info-number').html("1234 5678 9012 3456");
        }
        else{
            $('.card-info-number').html(value);
        }
    }
    if(field == "cardCvv"){
        if(value == ""){
         $('.card-info-cvv').html("***");
        }
        else{
            $('.card-info-cvv').html(value);
        }
    }
    if(field == "cardName"){
        if(value == ""){
         $('.card-info-name').html("NOME IMPRESSO");
        }
        else{
            $('.card-info-name').html(value);
        }
    }
    if(field == "cardMes"){
        if(value == ""){
         $('.card-info-mes').html("Mês");
        }
        else{
            $('.card-info-mes').html(value);
        }
    }
    if(field == "cardAno"){
        if(value == ""){
            $('.card-info-ano').html("Ano");
        }
        else{
            $('.card-info-ano').html(value);
        }
    }
    if(field == "cardBrand"){
        if(value == ""){
          $('#card-info-brand').removeClass(); 
        } 
        else{
           $('#card-info-brand').removeClass().addClass(value);
        }
    } if(field == "all"){
        $('.card-info-name').html("NOME IMPRESSO");
        $('.card-info-number').html("1234 5678 9012 3456");
        $('.card-info-cvv').html("***");
        $('.card-info-mes').html("Mês");
        $('.card-info-ano').html("Ano");
        $('#card-info-brand').removeClass()
    }
}

})($);