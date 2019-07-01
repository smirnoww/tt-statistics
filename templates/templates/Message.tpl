{* Smarty *}

	{* Если передано сообщение, то отобразим его *}
	{if (isset($Message))}
		<table border="1">
		{foreach from=$Message item=mess}
			<tr bgcolor="{if (isset($mess.Type))}{if ($mess.Type|upper=='ERROR')}red{elseif ($mess.Type|upper=='WARNING')}yellow{else}green{/if}{else}green{/if}">
			<td>
				<h4>{$mess.Title}</h4>
				<p>{$mess.Body}</p>
				<p><i>{$mess.TechInfo}</i></p>
			</td>
			</tr>
		{/foreach}
		</table>
	{/if}
	
	{* Если передано сообщение об ошибке, то отобразим его *}
	{if (isset($ErrorMessage))}
		{if ($ErrorMessage>"")}
		
			<h4>Ошибка: {$ErrorMessage}</h4>
			
		{/if}
	{/if}