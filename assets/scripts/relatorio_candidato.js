import $ from "../../node_modules/jquery";

$(() => {
    $('.btn-rqr').on('click', function (e) {
        $.post($('#target').data('access'), JSON.parse(`{relatorio: ${e.currentTarget.dataset['acesso']}}`),
            function (data, textStatus, jqXHR) {},
            "json"
        );
    });

    $('#force-new').on('click', function (e) {
        $.post($('#force-new').data('link'), JSON.parse('{}'),
            function (data, textStatus, jqXHR) {},
            "json"
        );
    });
})