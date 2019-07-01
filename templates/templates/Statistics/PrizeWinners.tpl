{* Smarty *}

{if $PrizeWinners}
	<table class="tablebg" cellspacing="1" width="100%">
		<tr class="cat" align="center"><td><b>Призёры за 7 дней</b></td></tr>
		
		{foreach $PrizeWinners as $tourInfo => $tour}
			<tr class="cat" align="center"><td><b>{$tourInfo}</b></td></tr>
			{foreach $tour as $groupInfo => $group}
				<tr class="cat"><td>{$groupInfo}</td></tr>
				{foreach $group as $gp_Id => $gp}
					<tr class="row1">
						<td align="center" {if $gp.gp_Note}title="{$gp.gp_Note|escape}"{/if}>
							<a href="{$curPageURL}?ctrl=Profile&PlayerId={$gp.gp_PlayerId}">
								{$gp.p_Name}<br>
								<img src="{$ImageFolderURL}medal{$gp.gp_Place}.png">
								<img src="{$curPageURL}?ctrl=Players&act=GetAvatar&PlayerId={$gp.gp_PlayerId}">
							</a>
						</td>
					</tr>
					{/foreach}
			{/foreach}
		{/foreach}
	</table>
{else}
	<!-- Призёры за последнюю неделю отсутствуют -->
{/if}