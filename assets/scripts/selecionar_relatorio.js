import $ from 'jquery';
const r_acesso_url = $('span').data('access');

$('.rqr-btn').on('click', function (sender) {
    let $sender = $(sender.target);
    $.post(r_acesso_url, {'relatorio': $sender.data('id')},
        function (data, textStatus, jqXHR) {},
        "json"
    );
});
$('.btn[data-link]').on('click', function (e) {
    $.post($('.btn[data-link]').data('link'), {},
        function (data, textStatus, jqXHR) {},
        "json"
    );
});