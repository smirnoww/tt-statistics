{* Smarty *}
{extends file="LayoutMobileView.tpl"}

{block name=head}
    <meta name="description" content="Профиль игрока {$Player->p_Name} саратовской любительской лиги по настольному теннису. Рейтинг, статистика участия в турнирах, инвентарь, разряды, ..." />

	<script src="https://www.gstatic.com/charts/loader.js"></script>
	<script>
	  google.charts.load('current', { packages: ['corechart'] } );
	</script>
{/block}

{block name=body}


    <table border="0" class="tablebg1" width="100%">
        <tr class="cat">
            <th width="300">{$Player->p_Name}</th>
        </tr>
        
        <tr class="row1">
            <td valign="top" width="300">
                <div align="center">
                    <img width="300" src="?ctrl=Players&act=GetPhoto&p_Id={$Player.p_Id}">
                </div>
            </td>
        </tr>
        
        <tr class="row1">
            <td>        
                <table>
                    {$ForumUser = $Player->GetForumUserInfo()}
                    {if (isset($ForumUser))}
                    <tr>
                        <td colspan="2">
			    		    <a href="http://tt-saratov.ru/phpbb3/ucp.php?i=pm&mode=compose&u={$ForumUser.user_id}"><img src="http://tt-saratov.ru/phpbb3/styles/subsilver2/imageset/ru/icon_contact_pm.gif" alt="Отправить личное сообщение" title="Отправить личное сообщение"></a>
				    	    <a href="http://tt-saratov.ru/phpbb3/memberlist.php?mode=viewprofile&u={$ForumUser.user_id}"><img src="http://tt-saratov.ru/phpbb3/styles/subsilver2/imageset/ru/icon_user_profile.gif" alt="Профиль" title="Профиль"></a>
                        </td>
                    </tr>
                    {/if}
                    
                    <tr>
                        <td width="120">Год рождения:</td>
                        <td>{$Player->p_Birthdate->format('Y')}</td>
                    </tr>
                
                    <tr>
                        <td width="120">Город:</td>
                        <td>{$Player->p_City}</td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr class="row1">
            <td>
                <div id="ratingChart"></div>
            </td>
        </tr>
        
        <tr class="row1">
            <td>
                <div id="activeYears"></div>
            </td>
        </tr>
        
        <tr class="row1">
            <td>
                <table cellpadding="5">
                    <tr>
                        <td>
                            <div id="tabs-4">
	                            <b><u>Ракетка:</u></b><br>
	                            {$BBCodedpInfo|replace:"\n":"<br>"}
	                            <br>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr class="row1">
            <td>
                <table cellpadding="5">
                    <tr>
                        <td>
                            <div id="ranks"></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
    </table>
   
    <!-- Запустим jqueryUI -->
    <script>
		$(function() {
			// загрузим модули
			$("#ratingChart").load('?ctrl=Profile&act=RatingChart&p_Id={$Player->p_Id}&mobileview=true');
			$("#activeYears").load('?ctrl=Profile&act=PlayerActiveYears&PlayerId={$Player->p_Id}&mobileview=true');
			$("#ranks").load('?ctrl=Profile&act=Ranks&PlayerId={$Player->p_Id}&mobileview=true');

			// закладки в профиле
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

		}); // jquery.ready()
		
    </script>

{/block}