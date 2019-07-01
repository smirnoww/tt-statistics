{* Smarty *}

{*----------------------------------------------------------*}
{*                  Таблица с заявками                      *}
	<table border="1" id="{$TableId}">
	    <thead>
            <tr class="cat" align="center"> 
    			<td>№ пп</td>
    			<td>Игрок</td>
    			<td>Рейтинг</td>
    		</tr>
	    </thead>
        <tbody id="{$TableBodyId}"> 		
        {foreach $CallList as $call}
            <tr  id="CallTR_{$call.cft_id}">
				<td align="center" title="Заявка подана {$call.cft_CallDateTime}">{counter}</td>
				<td>
				    <a href="{$curPageURL}?ctrl=Profile&PlayerId={$call.cft_PlayerId}">
				        {$call.PlayerName}
                    </a>
			        {if $call.cft_Comment}
                        <img src="{$ImageFolderURL}speechBalloon.png" title="{$call.cft_Comment}" onclick="alert('{$call.cft_Comment}');" height="16">				    
                    {/if}
				</td>
				<td align="right" title="Рейтинг на момент подачи заявки = {$call.cft_PlayerRating|number_format:0}">{$call.ActualRate->pr_Rate|number_format:0}</td>
            </tr>
        {/foreach}
        </tbody>
	</table>