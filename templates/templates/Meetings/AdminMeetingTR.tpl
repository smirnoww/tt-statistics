<tr id="MeetingTR_{$Meeting.m_Id}" m_Id="{$Meeting.m_Id}"  class="{$Meeting.m_Act|default:'none'}ActTR">
	<td>
		<input	id="m_Act_{$Meeting.m_Id}" 				m_id="{$Meeting.m_Id}" 	name="m_Act"						type="text"		value="{$Meeting.m_Act|default:''}" 				size="4"	readonly tabindex="-1">
	</td>
	<td>
		<input	id="m_Id_{$Meeting.m_Id}" 				m_id="{$Meeting.m_Id}" 	name="m_Data[m_Id{if $Meeting.m_Id<0}_New{/if}]" type="text"	value="{$Meeting.m_Id}"	size="6"	readonly tabindex = "-1">
	</td>
	<td align="right">
		<input	id="m_WinnerPlayerId_{$Meeting.m_Id}" 	m_id="{$Meeting.m_Id}" 	name="m_Data[m_WinnerPlayerId]"		type="text"		value="{$Meeting.m_WinnerPlayerId}"		size="4"	readonly tabindex = "-1">
		<input	id="WinnerName_{$Meeting.m_Id}"      	m_id="{$Meeting.m_Id}" 	name="m_Data[WinnerName]"		        type="text"		value="{$Meeting.WinnerName}"	        size="20"	>    
	</td>
	<td align="right" class="assistant">
		<input	id="m_Winner2PlayerId_{$Meeting.m_Id}" 	m_id="{$Meeting.m_Id}" 	name="m_Data[m_Winner2PlayerId]"		type="text"		value="{$Meeting.m_Winner2PlayerId}" 	size="4"	readonly tabindex = "-1">
		<input	id="Winner2Name_{$Meeting.m_Id}"		m_id="{$Meeting.m_Id}" name="m_Data[Winner2Name]"	        type="text"		value="{$Meeting.Winner2Name}" 	        size="20"	>    
	</td>

	<td><input	id="m_WinnerScore_{$Meeting.m_Id}" 		m_id="{$Meeting.m_Id}" 	name="m_Data[m_WinnerScore]"			type="text"		value="{$Meeting.m_WinnerScore}" 	    size="3"></td>
	<td><input	id="m_LoserScore_{$Meeting.m_Id}" 		m_id="{$Meeting.m_Id}" 	name="m_Data[m_LoserScore]"			type="text"		value="{$Meeting.m_LoserScore}" 		    size="3"></td>
	
	<td align="right">
		<input	id="m_LoserPlayerId_{$Meeting.m_Id}" 	m_id="{$Meeting.m_Id}" 	name="m_Data[m_LoserPlayerId]"		type="text"		value="{$Meeting.m_LoserPlayerId}" 		size="4"	readonly tabindex = "-1">
		<input	id="LoserName_{$Meeting.m_Id}" 	        m_id="{$Meeting.m_Id}" 	name="m_Data[LoserName]"		        type="text"		value="{$Meeting.LoserName}" 	        size="20"	>    
	</td>
	<td align="right" class="assistant">
		<input	id="m_Loser2PlayerId_{$Meeting.m_Id}" 	m_id="{$Meeting.m_Id}" 	name="m_Data[m_Loser2PlayerId]"		type="text"		value="{$Meeting.m_Loser2PlayerId}" 		size="4"	readonly tabindex = "-1">
		<input	id="Loser2Name_{$Meeting.m_Id}"	        m_id="{$Meeting.m_Id}" 	name="m_Data[Loser2Name]"		        type="text"		value="{$Meeting.Loser2Name}"        	size="20"	>    
	</td>

	<td align="center">
		<input	id="m_AffectRating_{$Meeting.m_Id}" 	m_id="{$Meeting.m_Id}" 	name="m_Data[m_AffectRating]"			type="checkbox"	{if $Meeting.m_AffectRating|default:0 > 0}checked{/if}>
	</td>
	
	<td><input	id="m_Note_{$Meeting.m_Id}" 			m_id="{$Meeting.m_Id}" 	name="m_Data[m_Note]"					type="text"		value="{$Meeting.m_Note}" 			size="20"></td>
	<td><input	id="m_VideoURL_{$Meeting.m_Id}" 		m_id="{$Meeting.m_Id}" 	name="m_Data[m_VideoURL]"				type="text"		value="{$Meeting.m_VideoURL}" 		size="10"></td>

	<td>
		<input id="m_Save_{$Meeting.m_Id}" 		m_id="{$Meeting.m_Id}" 	name="m_Save_{$Meeting.m_Id}" 		type="button"	value="Save" title="Сохранить"> 
		<input id="m_Delete_{$Meeting.m_Id}" 	m_id="{$Meeting.m_Id}" 	name="m_Delete_{$Meeting.m_Id}" 		type="button"	value="Х" title="Удалить" tabindex="-1"> 
	</td>
</tr>


<script type="text/javascript">
    {* Настроим обработчики событий. Функция определена в AdminMeetingList.tpl *}
	$(function() {	    AdjustMeetingTR({$Meeting.m_Id});	});
</script>