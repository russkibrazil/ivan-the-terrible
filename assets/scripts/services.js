import $ from jquery;
function getCrianca() {
    return $('#crianca-ativa').attr('src').split('/').lastItem;
}