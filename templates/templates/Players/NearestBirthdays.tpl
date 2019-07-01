{* Smarty *}

{*extends file="Layout.tpl"*}

{block name="body"}
<div style="width:194">
<table class="tablebg" cellspacing="1" width="100%"> 
    {foreach $Players as $Player}
        <tr class="row1">
            <td align="center" title="{$Player.p_Birthdate|date_format:"%d.%m.%Y"}">
                <a href="{$smarty.server.SCRIPT_NAME}?ctrl=Profile&PlayerId={$Player.p_Id}"><b>{$Player.p_Name}</b><br>
    					<img height="80" src="{$smarty.server.SCRIPT_NAME}?ctrl=Players&act=GetAvatar&PlayerId={$Player.p_Id}">
    			</a>
    			<br>
                <b>
                {if $Player.DaysToBirthday == 0}
                    <span style="color:red">Cегодня</span>
                {else}
                    Через {$Player.DaysToBirthday} дн.
                {/if}
                </b>
    		</td>
    	</tr>
    {/foreach}
</table>
</div>
{/block}
