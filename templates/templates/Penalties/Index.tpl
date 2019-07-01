{* Smarty *}
{extends file="AdminLayout.tpl"}

{block name=AdminBody}
	<h2>Управление взысканиями</h2>
    <a href="?ctrl=Penalties&act=NewPenalty">Добавить</a>

    <table border="1" id="PenaltiesList">
        <tr align="center">  <!-- заголовок -->
            <th>Дата</th>
            <th>Игрок</th>
            <th>Тип</th>
            <th>Описание</th>
            <th>Дата окончания</th>
            <th>Действия</th>
        </tr>
        
        {foreach $PenaltiesList as $Penalty}
        <tr id="PenaltyTR_{$Penalty.pnlt_Id}">
            <td>                {$Penalty.pnlt_Date}</td>
        	<td>                {$Penalty.p_Name}</td>
        	<td bgcolor="{$Penalty.pt_Color}">                {$Penalty.pt_Name}</td>
        	
            <td width="300">    
                                {$Penalty.pnlt_Description|nl2br}
            </td>
            
            <td>                {$Penalty.pnlt_ExpDate}</td>
            <td>
                <a href="?ctrl=Penalties&act=EditPenalty&PenaltyId={$Penalty.pnlt_Id}">Редактировать</a>
            </td>
        </tr>
        {/foreach}
    </table>

{/block}