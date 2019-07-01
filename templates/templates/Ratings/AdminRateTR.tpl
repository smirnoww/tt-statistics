{* Smarty *}


<tr id="RatingTR_{$Rate.pr_Id}" pr_id="{$Rate.pr_Id}" class="{$Rate.pr_Act|default:'none'}ActTR">
        <td>                <input pr_id="{$Rate.pr_Id}"   id="pr_Act_{$Rate.pr_Id}"        name="pr_Act"               value="{$Rate.pr_Act|default:''}"           readonly    size="4"  tabindex="-1"></td>
        <td align="right">  <input pr_id="{$Rate.pr_Id}"   id="pr_Id_{$Rate.pr_Id}"         name="pr_Data[pr_Id{if $Rate.pr_Id<0}_New{/if}]"  value="{$Rate.pr_Id}"       readonly    size="6"  tabindex="-1"></td>
        <td>                <input pr_id="{$Rate.pr_Id}"   id="pr_Date_{$Rate.pr_Id}"       name="pr_Data[pr_Date]"     value="{$Rate.pr_Date}" size="10"></td>
    	<td>                <input pr_id="{$Rate.pr_Id}"   id="pr_Rate_{$Rate.pr_Id}"       name="pr_Data[pr_Rate]"     value="{$Rate.pr_Rate}"></td>
    	<td>                <input pr_id="{$Rate.pr_Id}"   id="pr_Note_{$Rate.pr_Id}"       name="pr_Data[pr_Note]"     value="{$Rate.pr_Note|escape}" size="32"></td>
    	<td>          
    	    <input pr_id="{$Rate.pr_Id}"   id="pr_Save{$Rate.pr_Id}" 	name="pr_Save_{$Rate.pr_Id}"    value="Save"   type="submit"   title="Сохранить">
            <input pr_id="{$Rate.pr_Id}"   id="pr_Delete{$Rate.pr_Id}" 	name="pr_Delete_{$Rate.pr_Id}"  value="Х"      type="button"   title="Удалить" tabindex="-1">
    	</td>
</tr>

<script type="text/javascript">
    {* Настроим обработчики событий. Функция определена в AdminRateHistory.tpl *}
	$(function() {	    AdjustRateTR({$Rate.pr_Id});	});
</script>