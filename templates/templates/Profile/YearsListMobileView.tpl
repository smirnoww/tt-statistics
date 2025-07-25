{* Smarty *}
<div>
    <!-- Статистика за все годы-->
    
    {$TotalTourCount = 0}
    {$TotalFirstPlaces = 0}
    {$TotalSecondPlaces = 0}
    {$TotalThirdPlacest = 0}
    {$TotalWins = 0}
    {$TotalMeetings = 0}
    

    {* Плашки со статистикой за все годы*}
    
    <table>
        <tr>
            <td width="120">Кол-во турниров:</td>
            <td>
                <span id="TotalTourCount"/></span>
            </td>
            
        </tr>
        
        <tr>
            <td width="120">Кол-во встреч:</td>
            <td><span id="TotalMeetings"/></span></td>
        </tr>

        <tr>
            <td width="120">Выиграно встреч:</td>
            <td><span id="TotalWins"/> (<span id="TotalWinPercent"/></span>)</span></td>
        </tr>

        <tr>
            <td>
                <span style="white-space:nowrap; padding-left:0px; padding-right:25px;" title="Занято первых мест">
    	            <img class="VertMidAlign" height="24" src="images/medal1.png"> x <span id="TotalFirstPlaces"/>
                </span>
            </td>
 
        </tr>
        
        <tr>
            <td>
                <span style="white-space:nowrap; padding-left:0px; padding-right:25px;" title="Занято вторых мест">
    	            <img class="VertMidAlign" height="24" src="images/medal2.png"> x <span id="TotalSecondPlaces"/>
                </span>
            </td>

        </tr>
        
        <tr>
            <td>
                <span style="white-space:nowrap; padding-left:0px; padding-right:25px;" title="Занято третьих мест">
    	            <img class="VertMidAlign" height="24" src="images/medal3.png"> x <span  id="TotalThirdPlacest"/>
                </span>
            </td>

        </tr>

    </table>
    

    {*<h3>По годам!!!!</h3>
    <div id="YearTabsHor">
        <ul>*}
            {foreach $Years as $year}
            
                {* считаем общие количества турниров, встреч, ... *}
                {$TotalTourCount = $TotalTourCount + $year.TourCount}
                {$TotalFirstPlaces = $TotalFirstPlaces + $year.FirstPlaces}
                {$TotalSecondPlaces = $TotalSecondPlaces + $year.SecondPlaces}
                {$TotalThirdPlacest = $TotalThirdPlacest + $year.ThirdPlaces}
                {$TotalWins = $TotalWins + $year.Wins}
                {$TotalMeetings = $TotalMeetings + $year.Meetings}
                
                {*<li><a href="?ctrl=Profile&act=PlayerTournaments&PlayerId={$p_Id}&Year={$year.Year}">{$year.Year}</a></li>*}
            {/foreach}
        {*</ul>
    </div>*}
    
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