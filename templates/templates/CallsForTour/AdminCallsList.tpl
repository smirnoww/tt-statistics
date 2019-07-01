{* Smarty *}

{extends file="Tours/AdminTourLayout.tpl"}

{block name=AdminTourBody}

<h3>Заявки на турнир id={$Tour->t_Id}</h3>

	<p><a href="?ctrl=TourProfile&t_Id={$Tour->t_Id}">вернуться в профиль турнира</a></p>

 	<h3>Заявки на турнир</h3>
		
	<form action="?ctrl=CallsForTour&act=SaveTourCallsList" method="POST" enctype="multipart/form-data">
		<table>
			<tr>
				<td align="right">t_Id:</td><td><input	id="TourId" 	name="TourId"		type="text"		value="{$Tour.t_Id}"	size="4" readonly></td>
			</tr>
			<tr>
				<td align="right">tt_Name:</td><td>{$Tour->t_TourTypeId('Model_TournamentType')->ttype_Name}</td>
			</tr>
			<tr>
				<td align="right">t_DateTime:</td><td>{$Tour.t_DateTime->format('d.m.Y H:i')}</td>
			</tr>
			<tr>
				<td align="right">t_Info:</td><td>{$Tour.t_Name}</td>
			</tr>
		</table>
		
		
		<h4>Список заявок</h4>
		<table border="1" id="CallList">
			<tr>
				<th>cft_Act</th>
				<th>cft_Id</th>
				<th>cft_CallDateTime</th>
				<th>cft_Player</th>
				<th>cft_Comment</th>
				<th>Act</th>
			</tr>
			{* цикл по заявкам *}
			{foreach $CallsForTour as $call}
				<tr id="CallTR_{$call.cft_Id}">
					<td><input	id="cft_Act_{$call.cft_Id}" 			cft_id="{$call.cft_Id}" 	name=cft_Act[{$call.cft_Id}]			    	    type="text"		value="none" 							size="4"	readonly></td>
					<td><input	id="cft_Id_{$call.cft_Id}" 		    	cft_id="{$call.cft_Id}" 	name=cft_Data[{$call->cft_Id}][cft_Id]			    type="text"		value="{$call.cft_Id}"				size="4"	readonly></td>
					<td><input	id="cft_CallDateTime_{$call.cft_Id}" 	cft_id="{$call.cft_Id}" 	name=cft_Data[{$call->cft_Id}][cft_CallDateTime]	type="text"		value="{$call->cft_CallDateTime->format('d.m.Y H:i:s')}" 	size="23"></td>

					<td align="right">
						<input	id="cft_PlayerId_{$call.cft_Id}" 		cft_id="{$call.cft_Id}" 	name=cft_Data[{$call->cft_Id}][cft_PlayerId]		type="text"		value="{$call.cft_PlayerId}" 		                    size="4"	readonly>
						<input	id="cft_PlayerName_{$call.cft_Id}"		cft_id="{$call.cft_Id}" 	name=cft_Data[{$call->cft_Id}][cft_PlayerName]	    type="text"		value="" 	size="20"	>
						{* Проставим имя игрока отдельно. Если задать его сразу в элементе input, то при инициализации autocomplete открывается меню для выбора *}
						<script>		$( "#cft_PlayerName_{$call.cft_Id}" ).val("{$call->cft_PlayerId('Model_Player')->p_Name}");    </script> 

						<input	id="cft_PlayerRating_{$call.cft_Id}" 	cft_id="{$call.cft_Id}" 	name=cft_Data[{$call->cft_Id}][cft_PlayerRating]	type="text"		value="{$call.cft_PlayerRating}" 		                size="4">
					</td>

					<td><input	id="cft_Comment_{$call.cft_Id}" 		cft_id="{$call.cft_Id}" 	name=cft_Data[{$call->cft_Id}][cft_Comment]			type="text"		value="{$call.cft_Comment}" 	    size="50"></td>

					<td>
						<input id="cft_Delete_{$call.cft_Id}" 			cft_id="{$call.cft_Id}" 	name=cft_Delete{$call.cft_Id} 				type="button"	value="Х" title="Удалить"> 
					</td>
				</tr>
			{/foreach}
			<tr>
				<td colspan="7" align="right">
					<input id="addCall" name="addCall" type="button" value="+" title="Добавить заявку"> 
				</td>
			<tr>
		</table>
		
		
		<input id="submit" name="submit" type="submit" value="Сохранить заявки">
	</form>



	{literal}
	<script type="text/javascript">

	function DeleteCall(event) {
		cft_id = event.target.getAttribute('cft_id');

		if ($("#cft_Act_"+cft_id).val()=='new') {
			$("#CallTR_"+cft_id).remove();
		}
		else {
			if ($("#cft_Act_"+cft_id).val()!='del') {
				$("#cft_Act_"+cft_id).val('del');
				$("#CallTR_"+cft_id).css('background-color','#EE0000');
			}
			else {
				$("#cft_Act_"+cft_id).val('edit');
				$("#CallTR_"+cft_id).css('background-color','#0000EE');
			}
		}
		
	}


	function EditCall(event) {
		cft_id = event.target.getAttribute('cft_id'); 
		if ($("#cft_Act_"+cft_id).val()!='new') {
			$("#cft_Act_"+cft_id).val('edit');
			$("#CallTR_"+cft_id).css('background-color','#0000EE');
		}
	}					
				
	function PlayerSelected( event, ui ) {
        // set p_Id and p_Rate after select
        var cft_id = $(this).attr('cft_id');
        $('#cft_PlayerId_'+cft_id).val(ui.item.p_Id);
        $('#cft_PlayerRating_'+cft_id).val(ui.item.p_Rate);
        
        EditCall(event);
    }
				
	var NewCFT_Id = -1;
	function addCall(event) {
		$('#CallList tr').eq(-2).before(
				'<tr id="CallTR_'+NewCFT_Id+'">' +
				'	<td><input	id="cft_Act_'+NewCFT_Id+'" 				cft_id="'+NewCFT_Id+'" 	name=cft_Act['+NewCFT_Id+']				            type="text"		value="new" 			size="4"	readonly></td>' +
				'	<td><input	id="cft_Id_'+NewCFT_Id+'" 				cft_id="'+NewCFT_Id+'" 	name=cft_Data['+NewCFT_Id+'][cft_Id_new]	        type="text"		value="'+NewCFT_Id+'"	size="4"	readonly>' +
	{/literal}
				'	    <input	id="cft_TourId_'+NewCFT_Id+'" 			cft_id="'+NewCFT_Id+'" 	name=cft_Data['+NewCFT_Id+'][cft_TourId]	        type="hidden"	value="{$Tour->t_Id}"></td>' +
	{literal}
				'	<td><input	id="cft_CallDateTime_'+NewCFT_Id+'" 	cft_id="'+NewCFT_Id+'" 	name=cft_Data['+NewCFT_Id+'][cft_CallDateTime_new]	type="text"		value="" 				size="23"></td>' +
			
				'	<td align="right">' +			
				'		<input	id="cft_PlayerId_'+NewCFT_Id+'" 		cft_id="'+NewCFT_Id+'" 	name=cft_Data['+NewCFT_Id+'][cft_PlayerId]		type="text"		value="" 				size="3"	readonly>' +
				'		<input	id="cft_PlayerName_'+NewCFT_Id+'"		cft_id="'+NewCFT_Id+'" 	name=cft_Data['+NewCFT_Id+'][cft_PlayerName]	type="text"		value="" 				size="20"	>' +
				'		<input	id="cft_PlayerRating_'+NewCFT_Id+'" 	cft_id="'+NewCFT_Id+'" 	name=cft_Data['+NewCFT_Id+'][cft_PlayerRating]	type="text"		value="" 				size="4">' +
				'	</td>' +			
			
				'	<td><input	id="cft_Comment_'+NewCFT_Id+'" 		    cft_id="'+NewCFT_Id+'" 	name=cft_Data['+NewCFT_Id+'][cft_Comment]		type="text"		value="" 	size="20"></td>' +

				'	<td>' +
				'		<input id="cft_Delete_'+NewCFT_Id+'" 			cft_id="'+NewCFT_Id+'" 	name=cft_Delete'+NewCFT_Id+' 				type="button"	value="Х" title="Удалить"> ' +
				'	</td>' +
				'</tr>'
		);
		
		$("input[name='cft_Delete"+NewCFT_Id+"']").bind('click', DeleteCall);
		$("#CallTR_"+NewCFT_Id).children().bind('change',EditCall);
		
	 	$("#cft_PlayerName_"+NewCFT_Id).autocomplete({
											source: "?ctrl=Players&act=FilteredPlayers",
											minLength: 3,
											select: PlayerSelected
										});
			
        $("#cft_PlayerName_"+NewCFT_Id).focus();
        
		$("#CallTR_"+NewCFT_Id).css('background-color','#00EE00');
		NewCFT_Id -= 1;
	}
	
	$('#addCall').bind('click', addCall);
	$("input[name*='cft_Delete']").bind('click', DeleteCall); 
	$("input[cft_id]").bind('change', EditCall);
	$("input[name*='cft_PlayerName']").autocomplete({
											source: "?ctrl=Players&act=FilteredPlayers",
											minLength: 3,
											select: PlayerSelected
										});
			
	</script>
	{/literal}



{/block}

