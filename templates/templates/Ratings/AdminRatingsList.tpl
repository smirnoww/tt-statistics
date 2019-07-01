{* Smarty *}

{extends file="AdminLayout.tpl"}

{block name=AdminBody}
<h2>Управление рейтингами!</h2>
<form  action="?ctrl=Ratings&act=SaveRatingsList" method="POST" enctype="multipart/form-data">
    <table border="1" id="RatingList">
        <tr align="center">  <!-- заголовок -->
            <th>Act</th>
            <th>Id</th>
            <th>Название</th>
            <th>Описание</th>
            <th>Действия</th>
        </tr>
        
        {foreach $Ratings as $rating}
        <tr id="RatingTR_{$rating->r_Id}">
            <td>                <input r_id="{$rating->r_Id}"   id="r_Act_{$rating->r_Id}"           name="r_Act[{$rating->r_Id}]"                  value="none"            readonly    size="4"></td>
            <td align="right">  <input r_id="{$rating->r_Id}"   id="r_Id_{$rating->r_Id}"            name="r_Data[{$rating->r_Id}][r_Id]"           value="{$rating->r_Id}" readonly    size="4"></td>
            <td>                <input r_id="{$rating->r_Id}"   id="r_Name_{$rating->r_Id}"          name="r_Data[{$rating->r_Id}][r_Name]"         value="{$rating->r_Name|escape}"></td>
        	<td>                <input r_id="{$rating->r_Id}"   id="r_Description_{$rating->r_Id}"   name="r_Data[{$rating->r_Id}][r_Description]"  value="{$rating->r_Description|escape}"></td>

        	<td>          
					            <input r_id="{$rating->r_Id}"   id="r_Delete_{$rating->r_Id}" 		 name="r_Delete_{$rating->r_Id}"    value="Х"       type="button"   title="Удалить">
					            <a href="?ctrl=Ratings&act=Calculation&RatingId={$rating->r_Id}">Обсчёт ...</a>
        	</td>
        </tr>
        {/foreach}
		<tr>
			<td colspan="5" align="right">
				<input id="addRating" name="addRating" type="button" value="+" title="Добавить рейтинг"> 
			</td>
		<tr>
    </table>
    <input id="submit" name="submit" type="submit" value="Сохранить">
</form>
	{literal}
	<script type="text/javascript">
        
		function DeleteRating(event) {
			r_id = event.target.getAttribute('r_id');

			if ($("#r_Act_"+r_id).val()=='new') {
				$("#RatingTR_"+r_id).remove();
			}
			else {
				if ($("#r_Act_"+r_id).val()!='del') {
					$("#r_Act_"+r_id).val('del');
					$("#RatingTR_"+r_id).css('background-color','#EE0000');
				}
				else {
					$("#r_Act_"+r_id).val('edit');
					$("#RatingTR_"+r_id).css('background-color','#0000EE');
				}
			}
			
			Modified = true;
		}

		function EditRating(event) {
			r_id = event.target.getAttribute('r_id'); 
			if ($("#r_Act_"+r_id).val()!='new') {
				$("#r_Act_"+r_id).val('edit');
				$("#RatingTR_"+r_id).css('background-color','#0000EE');
			}
		}					


		var NewR_Id = -1;
		function addRating(event) {
		
			$('#RatingList tr').eq(-2).before(
					'<tr id="RatingTR_'+NewR_Id+'">'+
					'	<td><input	id="r_Act_'+NewR_Id+'" 		    r_id="'+NewR_Id+'" 	name=r_Act['+NewR_Id+']		            type="text"		value="new" 			size="4"	readonly></td>'+
					'	<td><input	id="r_Id_'+NewR_Id+'" 		    r_id="'+NewR_Id+'" 	name=r_Date['+NewR_Id+'][r_Id]		    type="text"		value="'+NewR_Id+'"	    size="4"	readonly></td>'+
					'	<td><input	id="r_Name_'+NewR_Id+'" 	    r_id="'+NewR_Id+'" 	name=r_Data['+NewR_Id+'][r_Name]		type="text"		value="" 	></td>'+
					'	<td><input	id="r_Description_'+NewR_Id+'" 	r_id="'+NewR_Id+'" 	name=r_Data['+NewR_Id+'][r_Description]	type="text"		value="" 	></td>'+

					'	<td>'+
					'		<input id="r_Delete_'+NewR_Id+'" 		r_id="'+NewR_Id+'" 	name=r_Delete_'+NewR_Id+' 		type="button"	value="Х" title="Удалить"> '+
					'	</td>'+
					'</tr>'
			);
			
			$("input[name='r_Delete_"+NewR_Id+"']").bind('click', DeleteRating);
			$("#RatingTR_"+NewR_Id).children().bind('change',EditRating);

			$("#RatingTR_"+NewR_Id).css('background-color','#00EE00');
			NewR_Id -= 1;
		}


    	$('#addRating').bind('click', addRating);
    	$("input[name*='r_Delete_']").bind('click', DeleteRating); 
    	$("input[r_id]").bind('change',EditRating);
	</script>
	{/literal}    
{/block}
