{* Smarty *}
{extends file="Layout.tpl"}

{block name=body}

<style>
  .ui-autocomplete-loading {
    background: white url("images/ui-anim_16x16.gif") right center no-repeat;
  }
  
  .newActTR {
    background-color: #00EE00;
  }

  .editActTR {
    background-color: #0000EE;
  }

  .delActTR {
    background-color: #EE0000;
  }

  .noneActTR {
    background-color: transparent;
  }
</style>

<h1>Администрирование</h1>
	{* Admin menu *}
	<table border="0">
		<tr class="row1" align="center">
			<td><a href="?ctrl=Ratings&act=AdminRatingsList">Рейтинги</a></td>
			<td><a href="?ctrl=TournamentTypes&act=AdminTourTypesList">Типы турниров</a></td>
			<td><a href="?ctrl=Tours&act=AdminList">Турниры</a></td>
			<td>
				<a href="?ctrl=Players&act=AdminPlayers">Игроки (на тестировании)</a>
			    <a href="http://tt-saratov.ru/statistics/xjak/admrating.php?module=playersadmin">- старое (будет удалено)</a>
			    <small> [<a href="?ctrl=Players&act=xml">Player.xml</a>]</small>
			</td>
			<td><a href="?ctrl=Penalties">Взыскания</a></td>
			<td><a href="?ctrl=Courts&act=AdminList">Площадки/игродромы</a></td>
			{* <td><a href="?ctrl=Admin&act=ETLToX">Загрузить в X структуру</a></td> *}
		</tr>
	</table>

	{block name=AdminBody}
		Добро пожаловать в админку, {$Auth->AuthPlayer->p_Name|default:$Auth->username}!
		
        <h3>Новости</h3>
        <ul>
			<li>03.11.2016 - планируется запуск авторасчёта опорного рейтинга</li>
        </ul>
    
        <h3>ToDo всё перенесено в <a href="http://redmine.off-def.ru">redmine</a></h3>
        <ul>
			<li><s>Профиль игрока - группа турнира, игродром</s> #21</li>
			<li><s>Заявки
			    <ul>
			        <li>Показать резерв</li>
			        <li>Заявка форумчан без привязки к профилю</li>
			        <li>Лог заявок</li>
			        <li>Предварительное разделение по группам</li>
			        <li>Командная онлайн заявка</li>
			        <li>Печать заявок для организаторов</li>
			        <li>google table для сортировок</li>
			    </ul></s> #26
		    </li>
			<li><s>Убрать из обсчёта парные встречи - в редактировании встреч убирать галку для обсчёта</s> #11</li>
			<li><s>Управление игроками</s> #20</li>
			<li><s>Разграничение доступа по организаторам</s> #9</li>
			<li>Кэширование вывода</li>
			<li>2. Почему то новая онлайн-форма вообще не реагирует на изменения рейтинга игроков. Т.е. она один раз зарегистривовал игрока с рейтингом и далее она его так отображает. Хотя рейтинг у этого игрока уже изменился допустим.</li>
			<li><s>3. Очень хочется чтобы появилась опять ссылка на полный рейтинг на главной странице.(страница статистики)</s> #22</li>
			<li><s>5. Очень много городов у нас уже в базе как саратовской области, так и другие области. Было бы прикольно если бы вместе с названием города рядом бы красовался герб этого города))) это так для красоты</s> #23</li>
			<li><s>6. Указывать в профиле игроков разряд. Федерация присваивает разряды и можно у них взять эти данные. Например Волков Олег в прошлом году получил 3-й взрослый.</s> #24</li>
			<li><s>Редактирование инвентаря в профиле</s> #4</li>
			<li><s>Связать пользователя форума и игрока по id, а не по текстовому логину. Проверить можно на пользователе: <a href="http://tt-saratov.ru/phpbb3/memberlist.php?mode=viewprofile&u=689">Евгений</a>. Его же надо уведомить о решении.</s> #25</li>
			
			<hr>
			
			<li>+Видео встреч</li>
			<li>+4. В профиле игрока писать не день рождения, а год рождения.</li>
			<li>+Восстановил тесты</li>
			<li>+Калькулятор</li>
			<li>+Аватары в статистике личных встреч</li>
			<li>+Вывести шапку к списку редактирования рейтинга</li>
			<li>+Обсчёт турниров</li>
			<li>+Переключить онлайн форму с тестового ландшафта</li>
			<li>+Журнал запросов к админке</li>
			<li>+Управление историей рейтинга</li>
			<li>+Перенести фотки из старой БД</li>
			<li>+При сохранении встречи менять игроков местами по счёту</li>
			<li>+Схлопнуть игровок 1945 и 1002 - Два Селенковых</li>
			<li>+Схлопнуть игровок 1020 и 1012 - Два Мельниковых Дмитрия</li>
			<li>+онлайн форма заявки</li>
			<li>+ переписать запросы в профиле на новые таблицы</li>
            <li>+ удалил старые формы онлайн заявок из форума<pre>
UPDATE phpbb1_posts as p join 
(SELECT
    topic_id,
	post_id,
    Locate(':', bb) closeBBBracket,
	mid(bb,7,Locate(':', bb)-7) TourId,

	bb,
    afterBB,
    Locate(']', afterBB)+TourEndPos closeAfterBBBracket,
    TourStartPos,
    TourEndPos,
    post_text,
    INSERT(post_text,TourStartPos,Locate(']', afterBB)+TourEndPos-TourStartPos, '<<< Онлайн форма заявок удалена в связи с переходом на новый движок обсчёта рейтинга. Все турниры, встречи и обсчёт рейтинга остался без изменений >>> (с) Аминистрация сайта (04.02.2016)') updated
FROM
(
select 
	post_id,
    topic_id,
    Locate('[tour=', post_text) TourStartPos,
    Locate('[/tour', post_text) TourEndPos, 
    mid(post_text,Locate('[tour=', post_text),Locate('[/tour', post_text)-Locate('[tour=', post_text)+6) bb,
    mid(post_text,Locate('[/tour', post_text)) afterBB,
	post_text
from `phpbb1_posts`                                                    
WHERE post_text like '%[tour=%') pred
) as u on p.post_id = u.post_id
 set p.post_text = u.updated
            </pre></li>
        </ul>

	{/block}

{/block}

