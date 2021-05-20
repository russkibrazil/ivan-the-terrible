import $ from jquery;
import getCrianca from './services';
let estados = [];
let ultimoEstado = {};

$(() => {
});

$('#acao').on('click', function () {
    let $btn = $('#acao');
    $btn
        .toggleClass('btn-success')
        .toggleClass('btn-danger');
    $('#progresso').toggle();
    if ($btn.text().toLowerCase() == 'iniciar')
    {
        let lado = $('div.btn-group > button.active').text();
        if (ultimoEstado == {})
        {
            ultimoEstado = novoObjeto();
            ultimoEstado.lado = lado;
        }
        else
        {
            if (lado != ultimoEstado.lado || getCrianca() != ultimoEstado.crianca)
            {
                ultimoEstado.dhFim = `@${Date.now()/1000}`;
                estados.push(ultimoEstado);
                ultimoEstado = novoObjeto();
                ultimoEstado.lado = lado;
            }
        }
    }
    else
    {
        ultimoEstado.dhFim = `@${Date.now()/1000}`;
        estados.push(ultimoEstado);
        ultimoEstado = {};
    }
});
$('#fim').on('click', function () {
    $.post($('#fim').data('end'), {'estados': estados},
        function (data, textStatus, jqXHR) {

        },
        "json"
    );
});

function novoObjeto() {
    return {'lado': 'E',
    'dhInicio': `@${Date.now()/1000}`,
    'dhFim': null,
    'crianca': getCrianca()
    };
}