{* Smarty *}
{extends file="Layout.tpl"}

{block name=body}

<table border="0" class="tablebg" width="100%">
    <tr align="center" class="cat">
		<th>
			<span style="text-align: center">Видео встречи</span>
		</th>
	</tr>

	<tr class="row1">
		<td>
			<p align="center"><input type="button" name="back" value="Вернуться" onclick="history.go(-1); "></p>
		</td>
	</tr>
				
    {foreach $VideoIds as $VideoId}
		<tr class="row1">
			<td>
	            <div align="center">
	                <iframe type="text/html" width="640" height="390" src="http://www.youtube.com/embed/{$VideoId|strip}" frameborder="0" allowfullscreen></iframe>    
	            </div>
			</td>
		</tr>';
	{/foreach}

</table>
{/block}

