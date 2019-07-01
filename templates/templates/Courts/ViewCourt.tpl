{* Smarty *}
{extends file="Layout.tpl"}

{block name=body}
<h2>{$Court.c_Name}</h2>
    <table border="1">
        <tr>
            <td align="right">Адрес</td>
            <td>{$Court.c_Address}</td>
        </tr>
        <tr>
            <td align="right">Контакты</td>
            <td>{$Court.c_Contacts}</td>
        </tr>
        <tr>
            <td align="right">URL</td>
            <td>
                {if $Court.c_URL}
                    <a href="{$Court.c_URL}"><img src="images/LinkToWeb.png"></a>
                {/if}
            </td>
        </tr>
        <tr>
            <td align="right">Описание</td>
            <td>{$Court.c_Description|nl2br}</td>
        </tr>
    </table>

{/block}