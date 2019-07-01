{* Smarty *}

{extends file="TourTypes/AdminTourTypeLayout.tpl"}

{block name=AdminTourTypeBody}

<form  action="?ctrl=TournamentTypes&act=SaveTourTypesList" method="POST" enctype="multipart/form-data">
    <table border="1" id="TourTypesList">
        <tr align="center">  <!-- заголовок -->
            <th>Act</th>
            <th>Id</th>
            <th>Название</th>
            <th>Действия</th>
        </tr>
        
        {foreach $TourTypes as $tourtype}
        <tr id="TourTypeTR_{$tourtype->ttype_Id}">
            <td>                <input ttype_Id="{$tourtype->ttype_Id}"   id="ttype_Act_{$tourtype->ttype_Id}"           name="ttype_Act[{$tourtype->ttype_Id}]"                  value="none"            readonly    size="4"></td>
            <td align="right">  <input ttype_Id="{$tourtype->ttype_Id}"   id="ttype_Id_{$tourtype->ttype_Id}"            name="ttype_Data[{$tourtype->ttype_Id}][ttype_Id]"           value="{$tourtype->ttype_Id}" readonly    size="4"></td>
            <td>                <input ttype_Id="{$tourtype->ttype_Id}"   id="ttype_Name_{$tourtype->ttype_Id}"          name="ttype_Data[{$tourtype->ttype_Id}][ttype_Name]"         value="{$tourtype->ttype_Name}"></td>

        	<td>          
					            <input ttype_Id="{$tourtype->ttype_Id}"   id="ttype_Delete{$tourtype->ttype_Id}" 		 name="ttype_Delete{$tourtype->ttype_Id}"         value="Х"   type="button"   title="Удалить">
        	</td>
        </tr>
        {/foreach}
		<tr>
			<td colspan="5" align="right">
				<input id="addTourType" name="addTourType" type="button" value="+" title="Добавить тип турнира"> 
			</td>
		<tr>
    </table>
    <input id="submit" name="submit" type="submit" value="Сохранить">
</form>
	{literal}
	<script type="text/javascript">
        
		function DeleteTourType(event) {
			ttype_Id = event.target.getAttribute('ttype_Id');

			if ($("#ttype_Act_"+ttype_Id).val()=='new') {
				$("#TourTypeTR_"+ttype_Id).remove();
			}
			else {
				if ($("#ttype_Act_"+ttype_Id).val()!='del') {
					$("#ttype_Act_"+ttype_Id).val('del');
					$("#TourTypeTR_"+ttype_Id).css('background-color','#EE0000');
				}
				else {
					$("#ttype_Act_"+ttype_Id).val('edit');
					$("#TourTypeTR_"+ttype_Id).css('background-color','#0000EE');
				}
			}
			
			Modified = true;
		}

		function EditTourType(event) {
			ttype_Id = event.target.getAttribute('ttype_Id'); 
			if ($("#ttype_Act_"+ttype_Id).val()!='new') {
				$("#ttype_Act_"+ttype_Id).val('edit');
				$("#TourTypeTR_"+ttype_Id).css('background-color','#0000EE');
			}
		}					


		var Newttype_Id = -1;
		function addTourType(event) {
		
			$('#TourTypesList tr').eq(-2).before(
					'<tr id="TourTypeTR_'+Newttype_Id+'">'+
					'	<td><input	id="ttype_Act_'+Newttype_Id+'" 		    ttype_Id="'+Newttype_Id+'" 	name=ttype_Act['+Newttype_Id+']		            type="text"		value="new" 			size="4"	readonly></td>'+
					'	<td><input	id="ttype_Id_'+Newttype_Id+'" 		    ttype_Id="'+Newttype_Id+'" 	name=ttype_Data['+Newttype_Id+'][ttype_Id_new]  type="text"		value="'+Newttype_Id+'"	    size="4"	readonly></td>'+
					'	<td><input	id="ttype_Name_'+Newttype_Id+'" 	    ttype_Id="'+Newttype_Id+'" 	name=ttype_Data['+Newttype_Id+'][ttype_Name]	type="text"		value="" 	></td>'+

					'	<td>'+
					'		<input id="ttype_Delete_'+Newttype_Id+'" 		ttype_Id="'+Newttype_Id+'" 	name=ttype_Delete'+Newttype_Id+' 		type="button"	value="Х" title="Удалить"> '+
					'	</td>'+
					'</tr>'
			);
			
			$("input[name='ttype_Delete"+Newttype_Id+"']").bind('click', DeleteTourType);
			$("#TourTypeTR_"+Newttype_Id).children().bind('change',EditTourType);

			$("#TourTypeTR_"+Newttype_Id).css('background-color','#00EE00');
			Newttype_Id -= 1;
		}


    	$('#addTourType').bind('click', addTourType);
    	$("input[name*='ttype_Delete']").bind('click', DeleteTourType); 
    	$("input[ttype_Id]").bind('change',EditTourType);
	</script>
	{/literal}    
{/block}
