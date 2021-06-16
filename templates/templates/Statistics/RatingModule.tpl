{* Smarty *}

{if $Players}
<style>
table.ratingTable tr td:last-child {
  text-align: center;
}
</style>

<div style="white-space: nowrap;"><img src="images/filter.png" height="16"><input id="playerFilter" style="display:table-cell; width:90%" title="фильтр начинае работать, если введено 3 и более символов" size="16"/></div>

<div id="accordionRating">
    {$closed    = true}
    {$rangeFrom = 100000}
    {$rangeTo   = 100001}
    {$PlayerCount = 0}
	{foreach $Players as $player}
	    {if $player.pr_Rate < $rangeFrom}
	        {if !$closed}
                	</table>
	            </div>
	            {$PlayerCounts['RatingRange'|cat:$rangeFrom|cat:'-'|cat:$rangeTo] = $PlayerCount}
                {$PlayerCount = 0}
	            {$closed = true}
	        {/if}
	        {$rangeFrom = 100* ($player.pr_Rate/100)|floor}
	        {$rangeTo = 100* ($player.pr_Rate/100)|ceil}
	        
	        <h3>{$rangeFrom} - {$rangeTo}  <span id="RatingRange{$rangeFrom}-{$rangeTo}"></span></h3>
	        <div>
        	    <table class="tablebg ratingTable" cellspacing="1" width="100%" ratingRange="RatingRange{$rangeFrom}-{$rangeTo}">
	            {$closed = false}
	    {/if}
					<tr class="row1">
						<td>{$player.nn}</td>
						<td>
							<a href="{$curPageURL}?ctrl=Profile&p_Id={$player.pr_PlayerId}">{$player.p_Name}</a>
							{IF $player.pr_RankName}<img src="images/black_medal_16.png" title="{$player.pr_RankName} ({$player.pr_DateFrom|date_format:"%d.%m.%Y"})" onclick="alert(this.title);">{/IF}
						</td>
						<td title="Последнее изменение рейтинга было {$player.pr_Date|date_format:'%d.%m.%Y'}{if $player.pre_Date}: 
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
        {$PlayerCount = $PlayerCount+1}
	{/foreach}
    {* Закроем последний диапазон игроков*}
    {if !$closed}
				</table>
			</div>
        {$PlayerCounts['RatingRange'|cat:$rangeFrom|cat:'-'|cat:$rangeTo] = $PlayerCount}
        {$PlayerCount = 0}
        {$closed = true}
    {/if}
</div>

<script>
    $( function() {
		$.getScript('js/ratingModule.js',function() { ratingModuleInit(); });
    } );
</script>	
{else}
	<!-- Игроки с рейтингом отсутствуют -->
{/if}