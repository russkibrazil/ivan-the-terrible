import { $ } from "jquery";

$('#crianca-ativa').on('click', function () {
    $('#crianca-menu').toggle('slow');
});

$('.crianca-opcao').on('click', function (e) {
    let $senderImg = $(e.target.firstElementChild);
    let $criancaAtiva = $('crianca-ativa');
    if ($senderImg.attr('src') != $criancaAtiva.attr('src')){
        $criancaAtiva.attr('src', $senderImg.attr('src'));
        $('.crianca-opcao').removeClass('border border-5 border-danger');
        $criancaAtiva.addClass('border border-5 border-danger');
        atualizarCookie($senderImg.attr('src'));
    }
    else
    {
        atualizarCookie($criancaAtiva.attr('src'));
    }
    $('#crianca-menu').fadeOut('slow');
});

function atualizarCookie(valor) {
    let data = new Date();
    data.setTime(data.getTime() + (12 * 60 * 60 * 1000));
    let cookie = `cra=${valor};expires=${data.toUTCString()};path=/;`;
    document.cookie = cookie;
}
