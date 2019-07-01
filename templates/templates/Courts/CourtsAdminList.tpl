{* Smarty *}
{extends file="Courts/AdminCourtLayout.tpl"}

{block name=AdminCourtBody}

<a href="?ctrl=Courts&act=NewCourt">Добавить</a>

    <table border="1" id="RatingList">
        <tr align="center">  <!-- заголовок -->
            <th>Id</th>
            <th>Название</th>
            <th>Адрес</th>
            <th>Описание</th>
            <th>Действия</th>
        </tr>
        
        {foreach $Courts as $court}
        <tr id="CourtTR_{$court->c_Id}">
            <td align="right">  {$court.c_Id}</td>
            <td>                {$court.c_Name}</td>
        	<td>                {$court.c_Address}</td>
            <td>                {$court.c_Description|truncate:80:"..."}</td>
        	<td>                
        	        <a href="?ctrl=Courts&act=EditCourt&CourtId={$court.c_Id}">редактировать</a>
        	        <a href="?ctrl=Courts&act=Delete&CourtId={$court.c_Id}" onClick="return window.confirm('Уверены, что хотите удалить? Восстановление будет не возможно!')">удалить</a>
        	</td>
        </tr>
        {/foreach}
    </table>

{/block}