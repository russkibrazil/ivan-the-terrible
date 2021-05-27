import $ from 'jquery';
$(() => {});

$('.btn-link').on('click', function (sender) {
    let $detalhes = $(sender.target.closest('div.col-md').previousElementSibling).filter('p');
    $('#nomeUsuario').text($detalhes[0]);
    $('input[name=parentesco]').val();
    $('[data-e]').attr('data-e', $detalhes[1]);
});

$('.modal-footer button:last-of-type').on('click', function () {
    let nome = $('#nomeUsuario').text();
    let email = $('[data-e]').data('e');
    $.post($('[data-link]').data('link'), {'nome': nome, 'email': email, 'parentesco': $('div.modal-body input').val()},
        function (data, textStatus, jqXHR) {
            let $callerCol = $(`p[innerText = ${email}]`).closest('col-md').next();
            let $botoes = $callerCol.find('button');
            $botoes[0].setAttribute('disabled', 'true');
        },
        "json"
    );
});

$('.btn-dismiss').on('click', function (sender) {
    let linha = sender.target.closest('div.col-md').parentElement;
    linha.remove();
    // TODO: Verificar se há um vínculo ativo e confirmar se o usuáriuo deseja excluí-lo também
});