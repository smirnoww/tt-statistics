{* Smarty *}
<div>
    <!-- Статистика за все годы-->
    
    {$TotalTourCount = 0}
    {$TotalFirstPlaces = 0}
    {$TotalSecondPlaces = 0}
    {$TotalThirdPlacest = 0}
    {$TotalWins = 0}
    {$TotalMeetings = 0}
    
    <h3>Статистика участия</h3>
    <br>
    {* Плашки со статистикой за все годы*}
    <span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Принял участие в турнирах">
    	<img class="VertMidAlign" height="24" src="images/tours.png"> x <span id="TotalTourCount"/>
    </span>
    <span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Всего провёл встреч">
    	<img class="VertMidAlign" height="24" src="images/vs.png"> х <span  id="TotalMeetings"/>
    </span>
    <span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Выиграл встреч">
    	<img class="VertMidAlign" height="24" src="images/win.png"> х <span id="TotalWins"/>
    </span>
    <span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Выиграно встреч">
    	<img class="VertMidAlign" height="24" src="images/percent.png"> = <span  id="TotalWinPercent"/>
    </span>
    
    <br>
    <br>
    
    <span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Занято первых мест">
    	<img class="VertMidAlign" height="24" src="images/medal1.png"> x <span id="TotalFirstPlaces"/>
    </span>
    <span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Занято вторых мест">
    	<img class="VertMidAlign" height="24" src="images/medal2.png"> x <span id="TotalSecondPlaces"/>
    </span>
    <span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Занято третьих мест">
    	<img class="VertMidAlign" height="24" src="images/medal3.png"> x <span  id="TotalThirdPlacest"/>
    </span>
    
    {* годы горизонтальными закладками *}
    <br><br>
    <h3>По годам</h3>
    <div id="YearTabsHor">
        <ul>
            {foreach $Years as $year}
            
                {* считаем общие количества турниров, встреч, ... *}
                {$TotalTourCount = $TotalTourCount + $year.TourCount}
                {$TotalFirstPlaces = $TotalFirstPlaces + $year.FirstPlaces}
                {$TotalSecondPlaces = $TotalSecondPlaces + $year.SecondPlaces}
                {$TotalThirdPlacest = $TotalThirdPlacest + $year.ThirdPlaces}
                {$TotalWins = $TotalWins + $year.Wins}
                {$TotalMeetings = $TotalMeetings + $year.Meetings}
                
                <li><a href="?ctrl=Profile&act=PlayerTournaments&PlayerId={$p_Id}&Year={$year.Year}">{$year.Year}</a></li>
            {/foreach}
        </ul>
    </div>
    
    <script>
        // Расставим суммарные показатели вверху страницы
        $('#TotalTourCount').html('{$TotalTourCount}');
        $('#TotalFirstPlaces').html('{$TotalFirstPlaces}');
        $('#TotalSecondPlaces').html('{$TotalSecondPlaces}');
        $('#TotalThirdPlacest').html('{$TotalThirdPlacest}');
        {$TotalWinPercent=$TotalWins*100/$TotalMeetings}
        $('#TotalWins').html('{$TotalWins}');
        $('#TotalMeetings').html('{$TotalMeetings}');
        $('#TotalWinPercent').html('{$TotalWinPercent|round}%');
    
        $( "#YearTabsHor" ).tabs({
    							beforeLoad: function( event, ui ) { // Загружаем содержимое страниц только один раз
    																if ( ui.tab.data( "loaded" ) ) {
    																	event.preventDefault();
    																	return;
    																}
    														 
    																ui.jqXHR.success(function() {
    																	ui.tab.data( "loaded", true );
    																});
    										},
                        		{$ActiveTabCookieName='Profile_'|cat:$p_Id|cat:'_ActiveYearTab'}
        						activate:   function( event, ui ) { 
        						                //Запомним открытую закладку в куках
        						                var activeTabN = $( event.target ).tabs( "option", "active" );
        						                document.cookie = '{$ActiveTabCookieName}='+activeTabN;
        						            },
    						    active: getCookie('{$ActiveTabCookieName}') {* восстановим открытую закладку из куков*}
    
    							});
    
    </script>
</div>