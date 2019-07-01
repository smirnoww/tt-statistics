{* Smarty *}
{* For including in New and Edit form *}
				<tr>
					<td>
						<img class="deletePlayerRank" pr_Id="{$rank.pr_Id}" src="images/Button-Delete-icon.png" height="16">
						
						<input name="playerRankData[{$rank.pr_Id}][pr_Id]" 			value="{$rank.pr_Id}"			size="4" readonly	type="hidden">				
						<input name="playerRankData[{$rank.pr_Id}][pr_PlayerId]"	value="{$rank.pr_PlayerId}"		size="4" readonly	type="hidden">
						<input name="playerRankData[{$rank.pr_Id}][Delete]" 		value="0" class="deleteThis"	size="1" readonly	type="hidden">
												
						<select name="playerRankData[{$rank.pr_Id}][pr_RankId]">
							{foreach $Ranks as $r}
								<option value="{$r.r_Id}" {if $r.r_Id==$rank->pr_RankId}selected{/if}>{$r.r_Name}</option>
							{/foreach}
						</select>
					</td>
					<td><input class="date" name="playerRankData[{$rank.pr_Id}][pr_DateFrom]" value="{$rank.pr_DateFrom}"></td>
					<td><input name="playerRankData[{$rank.pr_Id}][pr_Organization]" value="{$rank.pr_Organization}" size="25"></td>
					<td><input class="date expireDate" name="playerRankData[{$rank.pr_Id}][pr_DateTo]" value="{$rank.pr_DateTo}"></td>
					<td><input name="playerRankData[{$rank.pr_Id}][pr_Note]" pr_Id="{$rank.pr_Id}" value="{$rank.pr_Note}" size="40"></td>
				</tr>
