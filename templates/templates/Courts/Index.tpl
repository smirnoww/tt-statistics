{* Smarty *}
{extends file="Layout.tpl"}

{block name=body}
    <table border="1" id="RatingList">
        <tr align="center">  <!-- заголовок -->
            <th>Название</th>
            <th>Адрес</th>
            <th>Контакты</th>
            <th>Описание</th>
        </tr>
        
        {foreach $Courts as $court}
        <tr id="CourtTR_{$court->c_Id}">
            <td>
                <a href="?ctrl=Courts&act=ViewCourt&c_Id={$court.c_Id}">{$court.c_Name}</a>
                {if $court.c_URL}
                    <a href="{$court.c_URL}"><img src="images/LinkToWeb.png"></a>
                {/if}
            </td>
        	<td>                {$court.c_Address}</td>
        	<td>                {$court.c_Contacts}</td>
            <td width="300">    {$court.c_Description|nl2br}</td>
        </tr>
        {/foreach}
    </table>

{/block}