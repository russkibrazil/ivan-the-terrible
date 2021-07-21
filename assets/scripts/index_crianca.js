import $ from "../../node_modules/jquery";
import { setCrianca } from './services';

$(() => {
    $('.acoes-crianca').on('click', function (sender) {
        const $sender = $(sender.currentTarget);
        const $item = $sender.parents('.row');
        const $el_ativo = $item.find('img');

        setCrianca($el_ativo.attr('alt'), $el_ativo.attr('src'));

        if ($sender.text() == 'Registros')
        {
            location.href = $('#paths').data('reg');
        }
        else
        {
            location.href = $('#paths').data('req');
        }
    });
})