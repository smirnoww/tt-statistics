{* Smarty *}

{block name=head}
    {$CallsDivId="CallForTour_{$Tour.t_Id}_Div"}
    {$AddMeBtnId="AddMe_{$Tour.t_Id}_Btn"}
    {$DeleteMeBtnId="DeleteMe_{$Tour.t_Id}_Btn"}
    
    {if $JQuery}
        <script type="text/javascript" src="jquery/external/jquery/jquery.js"></script>
    {/if}
{/block}

{block name=body}

{*----------------------------------------------------------*}
{*  Кнопка администрирования и печати для организаторов     *}

{if $Auth->CR($admin_AR + $tourorg_AR) > 0}
    
    <div align="right">
        <a target="_blank" href="{$curPageURL}?ctrl=CallsForTour&act=EditCalls&TourId={$Tour.t_Id}" title="TODO:доступно только организатору турнира и админам">редактировать</a>
        <a target="_blank" href="{$curPageURL}?ctrl=CallsForTour&act=PrintCalls&TourId={$Tour.t_Id}" title="TODO:доступно только организатору турнира и админам">печать</a>
    </div>
{/if}

{*----------------------------------------------------------*}
{*                  Таблица с заявками                      *}
{$Auth->Roles}
<div id="{$CallsDivId}">
идёт загрузка...
</div>

{*----------------------------------------------------------*}
{*                  Кнопки подать/снять заявку              *}
{if $AllowCall}
	<input  id="{$AddMeBtnId}"      name="addMe"    type="button"   value="Принять участие" title="Принять участие"> 
	<input  id="{$DeleteMeBtnId}"   name="deleteMe" type="button"   value="Отменить заявку" title="Отменить заявку">
{/if}

{*==========================================================*}
{*                          javascript                      *}

<script language="javascript">

{*----------------------------------------------------------*}
{*            Инициализация после загрузки страницы         *}
    $(function(){
        LoadCalls_{$Tour.t_Id}();
    });

    //  нажатие на кнопку ПРИНЯТЬ УЧАСТИЕ
    $('#{$AddMeBtnId}').click(function() {
		var comment=prompt("Хотите оставить комментарий к своей заявке?","");
		if (comment===null) comment = '';
		$.get('{$curPageURL}?ctrl=CallsForTour&act=CallOnline&TourId={$Tour.t_Id}&Comment='+encodeURIComponent(comment), 
				function (result) {  
					alert(result);
					// Загрузка актуальных заявок после добавления/удаления игрока
					LoadCalls_{$Tour.t_Id}();
				}
		);
	});


    // нажатие на кнопку ОТМЕНИТЬ ЗАЯВКУ
	$('#{$DeleteMeBtnId}').click(function() {
		$.get('{$curPageURL}?ctrl=CallsForTour&act=DeCallOnline&TourId={$Tour.t_Id}', 
			function (result) {  
				alert(result);
				// Загрузка актуальных заявок после добавления/удаления игрока
				LoadCalls_{$Tour.t_Id}();
			}
		);
	});
	
	
    // Функция загрузки заявок
	function LoadCalls_{$Tour.t_Id}() {
        $('#{$CallsDivId}').load('{$curPageURL}?ctrl=CallsForTour&act=GetCallsTable&TourId={$Tour.t_Id}');
	} // function LoadCalls

</script>

{/block}
