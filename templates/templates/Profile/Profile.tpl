{* Smarty *}
{extends file="Layout.tpl"}

{block name=head}
    <meta name="description" content="Профиль игрока {$Player->p_Name} саратовской любительской лиги по настольному теннису. Рейтинг, статистика участия в турнирах, инвентарь, разряды, ..." />
{/block}

{block name=body}

    <table border="0" class="tablebg1" width="100%">
        <tr class="cat">
            <th width="200">Общий рейтинг</th>
            <th width="300">{$Player->p_Name}</th>
            <th><img style="vertical-align:middle" height="24" src="images/profile.png"> Профиль</th>
        <tr>
        <tr class="row1">
        <td width="180" valign="top">
			<div class="postbody">
					<div id="ratingdiv"><img src="images/tt.gif"><br>Идёт загрузка рейтинга</div>
					<small>
					* Дельта показана только для участвовавших в последнем турнире
					<br>
					* Во всплывающей подсказке к ячейкам с рейтингом показывается дата последнего изменения рейтинга
					</small>
				<script>
				$("#ratingdiv").load('?ctrl=Statistics&act=RatingModule');
				</script>
			</div>
		</td>

        <td valign="top" width="300">
            <div align="center">
                <img width="300" src="?ctrl=Players&act=GetPhoto&p_Id={$Player.p_Id}">
            </div>
            
            <table>
                {$ForumUser = $Player->GetForumUserInfo()}
                {if (isset($ForumUser))}
                    <tr>
                        <td colspan="2">
    						<a href="http://tt-saratov.ru/phpbb3/ucp.php?i=pm&mode=compose&u={$ForumUser.user_id}"><img src="http://tt-saratov.ru/phpbb3/styles/subsilver2/imageset/ru/icon_contact_pm.gif" alt="Отправить личное сообщение" title="Отправить личное сообщение"></a>
    						<a href="http://tt-saratov.ru/phpbb3/memberlist.php?mode=viewprofile&u={$ForumUser.user_id}"><img src="http://tt-saratov.ru/phpbb3/styles/subsilver2/imageset/ru/icon_user_profile.gif" alt="Профиль" title="Профиль"></a>
    						{* <img src="images/discount_btn.gif" alt="Скидка в ttshop" title="Скидка в ttshop" onclick="prompt ('Скопируйте ссылку и укажите её в комментарии к заказу','http://tt-saratov.ru/p.php?p_Id={$Player.p_Id}');" style="cursor: pointer;"> *}
                        </td>
                    </tr>
                {/if}
                <tr>
                    <td>Год рождения:</td>
                    <td>{$Player->p_Birthdate->format('Y')}</td>
                </tr>
                <tr>
                    <td>Пол:</td>
                    <td>
                        {if ($Player->p_Sex)}
                            <img src="images/male_32.png">
                        {else}
                            <img src="images/female_32.png">
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td>Город:</td>
                    <td>{$Player->p_City}</td>
                </tr>
                <tr>
                    <td></td>
                    <td><img src="images/gerb/{$Player->p_City}.png" width="50" alt="Герб города {$Player->p_City}"></td>
                </tr>
            </table>
        </td>
        
        {* Посчитаем количество взысканий*}
        {$ExpPenaltiesCount = $ExpiredPenaltiesList|count}
        {$NonExpPenaltiesCount = $NonExpiredPenaltiesList|count}
        {$PenaltiesCount = $NonExpPenaltiesCount+$ExpPenaltiesCount}
        
        <td valign="top">
        
            <div id="tabs">
            	<ul>
            		<li><a href="?ctrl=Profile&act=RatingChart&p_Id={$Player->p_Id}"><img style="vertical-align:middle" height="16" src="images/rating.png"/> Рейтинг</a></li>  
            		<li><a href="?ctrl=Profile&act=PlayerActiveYears&PlayerId={$Player->p_Id}"><img style="vertical-align:middle" height="16" src="images/tours.png"/> Турниры</a></li>
            		<li><a href="?ctrl=Statistics&act=HeadToHeadStatForm&FixedPlayerId={$Player->p_Id}"><img style="vertical-align:middle" height="16" src="images/vs.png"/> Статистика личных встреч</a></li>
            		<li><a href="#tabs-4"><img style="vertical-align:middle" height="16" src="images/raketka.png"/> Ракетки</a></li>
            		<li><a href="?ctrl=Profile&act=Ranks&PlayerId={$Player->p_Id}"><img style="vertical-align:middle" height="16" src="images/black_medal_16.png"/>Разряды/звания</a></li>

            		{if $PenaltiesCount>0}
            		    <li><a href="#tabs-Others"><img style="vertical-align:middle" height="16" src="images/others.png"/> Прочее</a></li>
            	    {/if}
            		
            	</ul>

            	<div id="tabs-4">
            	    <br><br>
        		    {$BBCodedpInfo|replace:"\n":"<br>"}
            	</div>
        		{if $PenaltiesCount>0}
                	<div id="tabs-Others">
                		{if $PenaltiesCount>0}
                		    <h3>Взыскания</h3>
                		{/if}
                		{if $NonExpPenaltiesCount>0}
                		    <h4>Действующие - {$NonExpPenaltiesCount}шт.</h4>
                            <table border="1" id="NonExpiredPenaltiesList">
                                <tr class="cat">  <!-- заголовок -->
                                    <td>Дата</td>
                                    <td>Тип</td>
                                    <td>Описание</td>
                                    <td>Дата окончания</td>
                                </tr>
                                
                                {foreach $NonExpiredPenaltiesList as $Penalty}
                                <tr class="row1" id="PenaltyTR_{$Penalty.pnlt_Id}">
                                    <td>                {$Penalty.pnlt_Date}</td>
                                	<td bgcolor="{$Penalty.pt_Color}">                {$Penalty.pt_Name}</td>
                                	
                                    <td width="300">    
                                                        {$Penalty.pnlt_Description|nl2br}
                                    </td>
                                    
                                    <td>                {$Penalty.pnlt_ExpDate}</td>
                                </tr>
                                {/foreach}
                            </table>
                        {/if}
    
                		{if $ExpPenaltiesCount>0}
                		    <h4>Погашенные - {$ExpPenaltiesCount}шт.</h4>
                            <table border="1" id="ExpiredPenaltiesList">
                                <tr class="cat">  <!-- заголовок -->
                                    <td>Дата</td>
                                    <td>Тип</td>
                                    <td>Описание</td>
                                    <td>Дата окончания</td>
                                </tr>
                                
                                {foreach $ExpiredPenaltiesList as $Penalty}
                                <tr class="row1" id="PenaltyTR_{$Penalty.pnlt_Id}">
                                    <td>                {$Penalty.pnlt_Date}</td>
                                	<td bgcolor="{$Penalty.pt_Color}">                {$Penalty.pt_Name}</td>
                                	
                                    <td width="300">    
                                                        {$Penalty.pnlt_Description|nl2br}
                                    </td>
                                    
                                    <td>                {$Penalty.pnlt_ExpDate}</td>
                                </tr>
                                {/foreach}
                            </table>
                        {/if}        		    
                	</div>
                {/if}
            </div>

        </td>
        </tr>
    </table>
    <!-- Запустим jqueryUI -->
    <script>
        $( "#tabs" ).tabs({
							beforeLoad: function( event, ui ) { // Загружаем содержимое страниц только один раз
																if ( ui.tab.data( "loaded" ) ) {
																	event.preventDefault();
																	return;
																}
														 
																ui.jqXHR.success(function() {
																	ui.tab.data( "loaded", true );
																});
										},
                    		{$ActiveTabCookieName='Profile_'|cat:$Player.p_Id|cat:'_ActiveTab'}
    						activate:   function( event, ui ) { 
    						                //Запомним открытую закладку в куках
    						                var activeTabN = $( event.target ).tabs( "option", "active" );
    						                document.cookie = '{$ActiveTabCookieName}='+activeTabN;
    						            },
						    active: getCookie('{$ActiveTabCookieName}') {* восстановим открытую закладку из куков*}
						});        
      //  $( "#Birthdatepicker" ).datepicker();
    </script>

{/block}