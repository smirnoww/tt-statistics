{* Smarty *}


<tr class="row1" MeetingNumber="{$Meeting.N}">
	<td align="center" name="DelButton">{if $Meeting.N > 1}<input type="button" value="X" onclick="delmeeting({$Meeting.N})">{/if}</td>
	<td name="PRCalculation"></td>
	<td name="MyDelta"></td>
	<td align="left">
	    <input type="button" name="MeetingResult" value="{if $Meeting.IWon}Я выиграл{else}Я проиграл{/if}" onclick="changewinner({$Meeting.N})">
	    <span class="scoreDependenced">
	        <input class="myScore" type="number" value="3" style="width: 50px;">
	        <input class="oppScore" type="number" value="0" style="width: 50px;">
        </span>
    </td>
	<td name="OpponentRate">
        <input required type="text" name="OppRate" value="{$Meeting.OpponentRate}">
        {$Meeting.OpponentName}
    </td>
	<td name="OpponentDelta"></td>
</tr>
