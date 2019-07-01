{* Smarty *}
{* Форма редактирования взыскания *}

{extends file="AdminLayout.tpl"}

{block name=AdminBody}
<h3>Взыскание</h3>

<form action="?ctrl=Penalties&act=Save" method="POST" enctype="multipart/form-data">
    <table>
        <tr>
            <td align="right">Id</td>
            <td>
                {if isset($Penalty.pnlt_Id)}
                    <input name="pnltData[pnlt_Id]" value="{$Penalty.pnlt_Id}" type="text" placeholder="Id взыскания" size="5" maxlength="5" readonly>
                {else}
                    Новое взыскание
                {/if}
            </td>
        </tr>

        <tr>
            <td align="right">Дата</td>
            <td>
                <input id="PenaltyDatePicker"  name="pnltData[pnlt_Date]" type="text" placeholder="дд.мм.гггг" size="10" maxlength="10">
            </td>
        </tr>

        <tr>
            <td align="right">Игрок</td>
            <td>
				<input	id="pnlt_PlayerId" 		name=pnltData[pnlt_PlayerId]	    type="text"		value="{$Penalty.pnlt_PlayerId}"    size="4"	readonly>
				<input	id="pnlt_PlayerName"	name=pnltData[pnlt_PlayerName]	    type="text"	    value="" 	size="20"	>
            </td>
        </tr>
        <tr>
            <td align="right">Вынес взыскание</td>

                {* По умолчанию подставляем текущего пользователя*}
                {$OrgPlayerId = $Auth->AuthPlayerId}
                {$OrgPlayerName = $Auth->AuthPlayer->p_Name}
                {$OrgTdAttr = " bgcolor=\"yellow\" title=\"По умолчанию установлен текущий пользователь.\nЭто значение ещё не сохранено\""}
                
                {* Если указан, то того, кто указан*}           
                {if isset($Penalty)}
                    {if $Penalty.pnlt_OrgPlayerId>0}
                        {$OrgPlayerId = $Penalty.pnlt_OrgPlayerId}
                        {$OrgPlayerName = $Penalty->pnlt_OrgPlayerId('Model_Player')->p_Name}
                        {$OrgTdAttr = ""}
                    {/if}
                {/if}

            <td{$OrgTdAttr}>
				<input	id="pnlt_OrgPlayerId" 	name=pnltData[pnlt_OrgPlayerId]	    type="text"		value="{$OrgPlayerId}"    size="4"	readonly>
				<input	id="pnlt_OrgPlayerName"	name=pnltData[pnlt_OrgPlayerName]	type="text"	    value="" 	size="20"	>
            </td>
        </tr>
		{* Проставим имя игрока и организатора отдельно. Если задать его сразу в элементе input, то при инициализации autocomplete открывается меню для выбора *}
		<script>		
		    {if isset($Penalty)}
		        $( "#pnlt_PlayerName" ).val("{$Penalty->pnlt_PlayerId('Model_Player')->p_Name}");    
		    {/if}
	        $( "#pnlt_OrgPlayerName" ).val("{$OrgPlayerName}");    
		</script> 
        
        <tr>
            <td align="right">Тип взыскания</td>
            <td>
                <select name="pnltData[pnlt_PenaltyTypeId]" >
                    {foreach $PenaltyTypes as $pnltType}
                        <option value="{$pnltType.pt_Id}"{if $pnltType.pt_Id==$Penalty.pnlt_PenaltyTypeId} selected{/if}>{$pnltType.pt_Name}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
        
        <tr>
            <td align="right">Обоснование</td>
            <td>
                <input name="pnltData[pnlt_Description]" value="{$Penalty.pnlt_Description}" type="text" placeholder="Описание причины вынесения взыскания" size="45" maxlength="255">
            </td>
        </tr>

        <tr>
            <td align="right">Дата окончания</td>
            <td>
                <input id="PenaltyExpDatePicker"  name="pnltData[pnlt_ExpDate]" type="text" placeholder="дд.мм.гггг" size="10" maxlength="10">
            </td>
        </tr>

    </table>
   
    <input type="submit" value="Сохранить">
    <a href="?ctrl=Penalties&act=Delete&PenaltyId={$Penalty.pnlt_Id}" onclick="return window.confirm('Уверены, что хотите удалить? Восстановление будет не возможно!')">Удалить</a>
    
</form>

<script>
	function PlayerSelected( event, ui ) {
        // set p_Id after select
        targetObj = $(event.target);
        targetObjname = targetObj.attr('name');
        var p_id = ui.item.p_Id;
        if (targetObjname.indexOf('pnlt_PlayerName')>=0) {
            $('#pnlt_PlayerId').val(p_id);
        }
        else {
            $('#pnlt_OrgPlayerId').val(p_id);
        }
    }

    $(function() {
        $( "#PenaltyDatePicker" ).datepicker({ldelim}firstDay: 1{rdelim});
        $( "#PenaltyDatePicker" ).datepicker("option", "dateFormat", 'dd.mm.yy');
        
        $( "#PenaltyExpDatePicker" ).datepicker({ldelim}firstDay: 1{rdelim});
        $( "#PenaltyExpDatePicker" ).datepicker("option", "dateFormat", 'dd.mm.yy');
        
        //todo: установить значения выпадающих списков via jquery
        {if $Penalty.pnlt_Date>''}
            $( "#PenaltyDatePicker" ).datepicker("setDate", "{$Penalty->pnlt_Date}" );
        {/if}
        {if $Penalty.pnlt_ExpDate>''}
            $( "#PenaltyExpDatePicker" ).datepicker("setDate", "{$Penalty->pnlt_ExpDate}" );
        {/if}
        
        autocompleteObj = {
							source: "?ctrl=Players&act=FilteredPlayers",
							minLength: 3,
							select: PlayerSelected
						};
     	$("#pnlt_PlayerName").autocomplete(autocompleteObj);
     	$("#pnlt_OrgPlayerName").autocomplete(autocompleteObj);
    });
  </script>

{/block}