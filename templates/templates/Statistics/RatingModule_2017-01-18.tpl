{* Smarty *}

{if $Players}
	<table class="tablebg" cellspacing="1" width="100%">
		<tr class="cat"><td colspan="3" align="center" title="рейтинг всех игроков, за всю историю"><b>{$RatingModuleHeader|default:"общий рейтинг"}</b></td></tr>		
		{foreach $Players as $player}
			<tr class="row1">
				<td>{$player.nn}</td>
				<td><a href="{$curPageURL}?ctrl=Profile&p_Id={$player.pr_PlayerId}">{$player.p_Name}</a></td>
				<td align="center" title="Последнее изменение рейтинга было {$player.pr_Date|date_format:'%d.%m.%Y'}{if $player.pre_Date}: 
{$player.pre_Rate}({$player.pre_Date|date_format:'%d.%m.%Y'}) 
{if $player.Delta > 0}+{/if}{$player.Delta} 
= {$player.pr_Rate}{/if} {$player.pr_Note}">

					{$player.pr_Rate|number_format:0:".":"'"}<br>
					
					{if $player.Delta > 0}
						<small><font color="#00AA00">+{$player.Delta|number_format:1:".":"'"}</font></small>
					{else if $player.Delta < 0}
						<small><font color="#AA0000">{$player.Delta|number_format:1:".":"'"}</font></small>
					{/if}
				</td>
			</tr>		
		{/foreach}
	</table><!--test-->
{else}
	<!-- Игроки с рейтингом отсутствуют -->
{/if}