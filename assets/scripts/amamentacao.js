import $ from "../../node_modules/jquery";
import getCrianca from './services';
let estados = [];
let ultimoEstado = undefined;

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
        if (ultimoEstado == undefined)
        {
            ultimoEstado = novoObjeto();
            ultimoEstado.lado = lado;
        }
        else
        {
            if (lado != ultimoEstado.lado || "crianca" != ultimoEstado.crianca)
            {
                ultimoEstado.dhFim = `@${Math.round(Date.now()/1000)}`;
                estados.push(ultimoEstado);
                ultimoEstado = novoObjeto();
                ultimoEstado.lado = lado;
            }
        }
        $btn.text('Parar');
    }
    else
    {
        ultimoEstado.dhFim = `@${Math.round(Date.now()/1000)}`;
        estados.push(ultimoEstado);
        ultimoEstado = undefined;
        $btn.text('Iniciar');
    }

    $('#fim').attr('disabled', false);
});

$('#fim').on('click', function () {
    if (ultimoEstado != undefined)
    {
        ultimoEstado.dhFim = `@${Math.round(Date.now()/1000)}`;
        estados.push(ultimoEstado);
    }
    $.post($('#fim').data('end'), {'estados': estados},
        function (data, textStatus, jqXHR) {

        },
        "json"
    );
});

$('div.btn-group .lado-seio').on('click', function (sender) {
    let $el = $(sender.target);
    let sibl  = $($el.siblings());
    sibl.toggleClass('active');
    sibl.attr('aria-pressed', 'true');
    $el.attr('aria-pressed', 'false');
    if (ultimoEstado != undefined)
    {
        if ($el.text() != ultimoEstado.lado)
        {
            ultimoEstado.dhFim = `@${Math.round(Date.now()/1000)}`;
            estados.push(ultimoEstado);
            ultimoEstado = novoObjeto();
            ultimoEstado.lado = $el.text()
        }
    }
});

function novoObjeto() { // TODO: Implementar getCriança
    return {'lado': 'E',
    'dhInicio': `@${Math.round(Date.now()/1000)}`,
    'dhFim': null,
    'crianca': "crianca"
    };
}