import $ from "../../node_modules/jquery";

const drop_item = `<li>
<button type="button" class="dropdown-item imagem-crianca-recente">
    <img src="" alt="" class="rounded-circle" style="height: 32px;">
</button>
</li>`;
const btngroup_item = `<button type="button" class="btn btn-outline-primary imagem-crianca-recente"><img src="" alt="" class="rounded-circle" style="height: 32px;"></button>`;

export function getCrianca() {
    const cookies = decodeURIComponent(document.cookie).split(';');
    const cra = cookies.find(el => el.startsWith('cra=')).split('=')[1].split(',');
    return cra[0];
}

export function setCrianca(novo_id, novo_path) {
    let $ativa_anterior = $('button.imagem-crianca-ativa img');
    if (novo_id != $ativa_anterior.attr('alt'))
    {
        const craUpdatedCookie = `cra=${encodeURIComponent(novo_id+','+novo_path)};max_age=${Math.round(Date.now() / 1000) + 60 * 60 * 12};SameSite=Lax;Path=/;`;
        const anterior_data = [$ativa_anterior[0].attributes['alt'].nodeValue, $ativa_anterior[0].attributes['src'].nodeValue];
        let $menu_desktop = $('div.btn-group ul.dropdown-menu');
        let $menu_mobile = $('div.collapse div.btn-group');
        const idx_select = $menu_desktop.find('img').filter(function (e) { return $(e).attr('alt') == novo_id});

        $ativa_anterior
            .attr('src', novo_path)
            .attr('alt', novo_id)
        ;

        if (idx_select.length == 0)
        {
            $menu_desktop.children().last().remove();
            $menu_mobile.children().last().remove();
        }
        else
        {
            $menu_desktop.children().eq(idx_select).remove();
            $menu_mobile.children().eq(idx_select).remove();
        }

        $menu_desktop.prepend(novoElementoPerfil(drop_item, anterior_data));
        $menu_mobile.prepend(novoElementoPerfil(btngroup_item, anterior_data));

        let data = `{"criancas": [{"${novo_id}": "${novo_path.slice(novo_path.lastIndexOf('/') + 1)}"}`;
        for (const el of $menu_desktop.find('img')) {
            const path = el.attributes['src'].nodeValue;
            data += `, {"${el.attributes['alt'].nodeValue}": "${path.slice(path.lastIndexOf('/') + 1)}"}`;
        }
        data += ']}';
        $.post($ativa_anterior.parent().data('update'), JSON.parse(data),
            function (data, textStatus, jqXHR) { },
            "json"
        );

        const $recentes_atualizados = $menu_desktop.find('img');
        let crUpdatedCookie = 'cr=';
        for (const el of $recentes_atualizados) {
            crUpdatedCookie += encodeURIComponent(`${el.attributes['alt'].nodeValue},${el.attributes['src'].nodeValue}|`);
        }
        crUpdatedCookie += `;max_age=${Math.round(Date.now() / 1000) + 60 * 60 * 12};SameSite=Lax;Path=/;`

        document.cookie = craUpdatedCookie;
        document.cookie = crUpdatedCookie;
    }
}

function ativarCrianca(e) {
    let $ativa = $(e.currentTarget).find('img');
    let $ativa_anterior = $('button.imagem-crianca-ativa img');
    if ($ativa.attr('alt') != $ativa_anterior.attr('alt'))
    {
        const craUpdatedCookie = `cra=${encodeURIComponent(`${$ativa.attr('alt')},${$ativa.attr('src')}`)};max_age=${Math.round(Date.now() / 1000) + 60 * 60 * 12};SameSite=Lax;path='/'`;
        const novo_data = [$ativa.attr('alt'), $ativa.attr('src')];
        const anterior_data = [$ativa_anterior[0].attributes['alt'].nodeValue, $ativa_anterior[0].attributes['src'].nodeValue];
        let $menu_desktop = $('div.btn-group ul.dropdown-menu');
        let $menu_mobile = $('div.collapse div.btn-group');
        const idx_select = $menu_desktop.find('img').index($ativa);

        $ativa_anterior
            .attr('src', novo_data[1])
            .attr('alt', novo_data[0])
            ;

        $menu_desktop.children().eq(idx_select).remove();
        $menu_mobile.children().eq(idx_select).remove();

        //closer to the pointer?
        $menu_desktop.prepend(novoElementoPerfil(drop_item, anterior_data));
        $menu_mobile.prepend(novoElementoPerfil(btngroup_item, anterior_data));


        let data = `{"criancas": [{"${novo_data[0]}": "${novo_data[1].slice(novo_data[1].lastIndexOf('/') + 1)}"}`;
        for (const el of $menu_desktop.find('img')) {
            const path = el.attributes['src'].nodeValue;
            data += `, {"${el.attributes['alt'].nodeValue}": "${path.slice(path.lastIndexOf('/') + 1)}"}`;
        }
        data += ']}';
        $.post($ativa_anterior.parent().data('update'), JSON.parse(data),
            function (data, textStatus, jqXHR) { },
            "json"
        );

        const $recentes_atualizados = $menu_desktop.find('img');
        let crUpdatedCookie = 'cr=';
        for (const el of $recentes_atualizados) {
            crUpdatedCookie += encodeURIComponent(`${el.attributes['alt'].nodeValue},${el.attributes['src'].nodeValue}|`);
        }
        crUpdatedCookie += `;max_age=${Math.round(Date.now() / 1000) + 60 * 60 * 12};SameSite=Lax;Path=/;`

        document.cookie = craUpdatedCookie;
        document.cookie = crUpdatedCookie;
        location.reload();
    }
}

function novoElementoPerfil(tipo, dados) {
    let x = $(tipo);
    x.on('click', ativarCrianca);
    x.find('img')
        .attr('src', criaPath(dados[1]))
        .attr('alt', dados[0])
        ;
    return x;
}

function criaPath(path) {
    if (path.startsWith('/')) {
        path = path.slice(path.lastIndexOf('/') + 1);
    }
    if (path.length == 5) {
        return `/static/${path}`;
    }
    else {
        return `/img/${path}`;
    }
}
