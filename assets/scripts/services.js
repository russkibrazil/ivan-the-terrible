import { $ } from "jquery";

export function getCrianca() {
    return $('#crianca-ativa').attr('src').split('/').lastItem;
}