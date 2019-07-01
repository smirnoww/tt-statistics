{* Smarty *}


<tr class="row1" MeetingNumber="{$Meeting.N}">
	<td align="center" name="DelButton">{if $Meeting.N > 1}<input type="button" value="X" onclick="delmeeting({$Meeting.N})">{/if}</td>
	<td name="PRCalculation"></td>
	<td name="MyDelta"></td>
	<td align="center">
	    <input type="button" name="MeetingResult" value="{if $Meeting.IWon}Я выиграл{else}Я проиграл{/if}" onclick="changewinner({$Meeting.N})">
    </td>
	<td name="OpponentRate">
        <input required type="text" name="OppRate" value="{$Meeting.OpponentRate}">
        {$Meeting.OpponentName}
    </td>
	<td name="OpponentDelta"></td>
</tr>
