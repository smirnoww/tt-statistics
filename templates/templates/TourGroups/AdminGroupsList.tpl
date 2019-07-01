{* Smarty *}

{extends file="Tours/AdminTourLayout.tpl"}

{block name=AdminTourBody}


<h3>Группы турнира id={$Tour->t_Id}</h3>

	<p><a href="?ctrl=TourProfile&t_Id={$Tour->t_Id}">вернуться в профиль турнира</a></p>

	<form action="?ctrl=TourGroups&act=SaveTourGroupsList" method="POST" enctype="multipart/form-data">
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
		
		
		<h4>Список групп</h4>
		<table border="1" id="GroupsList">
			<tr>
				<th>g_Act</th>
				<th>g_Id</th>
				<th>g_Name</th>
				<th>g_Criterion</th>
				<th>g_Color</th>
				<th>Act</th>
			</tr>
			{* цикл по группам *}
			{foreach $GroupsList as $group}
				<tr id="GroupTR_{$group.g_Id}">
					<td><input	id="g_Act_{$group.g_Id}" 		g_id="{$group.g_Id}" 	name=g_Act[{$group.g_Id}]			    	type="text"		value="none" 							size="4"	readonly></td>
					
					<td>
						<input	id="g_Id_{$group.g_Id}" 		g_id="{$group.g_Id}" 	name=g_Data[{$group->g_Id}][g_Id]			type="text"		value="{$group.g_Id}"				size="4"	readonly>
						<input	id="g_TourId_{$group.g_Id}" 	g_id="{$group.g_Id}" 	name=g_Data[{$group->g_Id}][g_TourId]		type="hidden"	value="{$group.g_TourId}">
					</td>
					<td><input	id="g_Name_{$group.g_Id}" 		g_id="{$group.g_Id}" 	name=g_Data[{$group->g_Id}][g_Name]			type="text"		value="{$group.g_Name|escape}"		size="24"	></td>
					<td><input	id="g_Criterion_{$group.g_Id}" 	g_id="{$group.g_Id}" 	name=g_Data[{$group->g_Id}][g_Criterion]	type="text"		value="{$group.g_Criterion|escape}"	size="48"	></td>
					<td><input	id="g_Color_{$group.g_Id}" 		g_id="{$group.g_Id}" 	name=g_Data[{$group->g_Id}][g_Color]		type="text"		value="{$group.g_Color|escape}"		size="6"	></td>
					
					<td>
						<input id="g_Delete_{$group.g_Id}" 		g_id="{$group.g_Id}" 	name=g_Delete{$group.g_Id} 				type="button"	value="Х" title="Удалить"> 
					</td>
				</tr>
			{/foreach}
			<tr>
				<td colspan="6" align="right">
					<input id="addGroup" name="addGroup" type="button" value="+" title="Добавить группу"> 
				</td>
			<tr>
		</table>
		
		
		<input id="submit" name="submit" type="submit" value="Сохранить группы">
	</form>



	{literal}
	<script>

	function DeleteGroup(event) {
		g_id = event.target.getAttribute('g_id');

		if ($("#g_Act_"+g_id).val()=='new') {
			$("#GroupTR_"+g_id).remove();
		}
		else {
			if ($("#g_Act_"+g_id).val()!='del') {
				$("#g_Act_"+g_id).val('del');
				$("#GroupTR_"+g_id).css('background-color','#EE0000');
			}
			else {
				$("#g_Act_"+g_id).val('edit');
				$("#GroupTR_"+g_id).css('background-color','#0000EE');
			}
		}
		
	}


	function EditGroup(event) {
		g_id = event.target.getAttribute('g_id'); 
		if ($("#g_Act_"+g_id).val()!='new') {
			$("#g_Act_"+g_id).val('edit');
			$("#GroupTR_"+g_id).css('background-color','#0000EE');
		}
	}					
				

	var NewG_Id = -1;
	function addGroup(event) {
		$('#GroupsList tr').eq(-2).before(
				'<tr id="GroupTR_'+NewG_Id+'">' +
				'	<td><input	id="g_Act_'+NewG_Id+'" 				g_id="'+NewG_Id+'" 	name=g_Act['+NewG_Id+']				            type="text"		value="new" 			size="4"	readonly></td>' +
				'	<td><input	id="NewG_Id_'+NewG_Id+'" 			g_id="'+NewG_Id+'" 	name=g_Data['+NewG_Id+'][NewG_Id_new]	        type="text"		value="'+NewG_Id+'"	size="4"	readonly>' +
	{/literal}
				'	    <input	id="g_TourId_'+NewG_Id+'" 		    g_id="'+NewG_Id+'" 	name=g_Data['+NewG_Id+'][g_TourId]	            type="hidden"	value="{$Tour->t_Id}"></td>' +
	{literal}

				'	<td><input	id="g_Name_'+NewG_Id+'"     	g_id="'+NewG_Id+'" 	name=g_Data['+NewG_Id+'][g_Name]	    type="text"		value="" 	size="24"></td>' +
				'	<td><input	id="g_Criterion_'+NewG_Id+'" 	g_id="'+NewG_Id+'" 	name=g_Data['+NewG_Id+'][g_Criterion]	type="text"		value="" 	size="46"></td>' +
				'	<td><input	id="g_Color_'+NewG_Id+'" 	    g_id="'+NewG_Id+'" 	name=g_Data['+NewG_Id+'][g_Color]		type="text"		value="" 	size="6"></td>' +

				'	<td>' +
				'		<input id="g_Delete_'+NewG_Id+'" 			g_id="'+NewG_Id+'" 	name=g_Delete'+NewG_Id+' 				        type="button"	value="Х" title="Удалить"> ' +
				'	</td>' +
				'</tr>'
		);
		
		$("input[name='g_Delete"+NewG_Id+"']").bind('click', DeleteGroup);
		$("#GroupTR_"+NewG_Id).children().bind('change',EditGroup);
		

		$("#GroupTR_"+NewG_Id).css('background-color','#00EE00');
		NewG_Id -= 1;
	}
	
	$('#addGroup').bind('click', addGroup);
	$("input[name*='g_Delete']").bind('click', DeleteGroup); 
	$("input[g_Id]").bind('change', EditGroup);

			
	</script>
	{/literal}



{/block}

