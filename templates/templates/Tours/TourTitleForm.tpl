{* Smarty *}
{* Форма редактирования заголовка турнира *}

{extends file="Tours/AdminTourLayout.tpl"}

{block name=AdminTourBody}
<h3>Новый турнир</h3>

<form action="?ctrl=Tours&act=Save" method="POST" enctype="multipart/form-data">
    <input name="BackURL" value="{$smarty.request.BackURL}" type="hidden">
    <table>
        {if isset($Tour.t_Id)}
            <tr>
                <td align="right">ID турнира</td>
                <td>
                    <input name="tourData[t_Id]" value="{$Tour.t_Id}" type="text" placeholder="ID турнира" size="5" maxlength="5" readonly>
                </td>
            </tr>
        {/if}
        <tr>
            <td align="right">Тип турнира</td>
            <td>
                <select name="tourData[t_TourTypeId]" >
                    {foreach $TourTypes as $tourType}
                        <option value="{$tourType.ttype_Id}"{if $tourType.ttype_Id==$Tour.t_TourTypeId} selected{/if}>{$tourType.ttype_Name}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td align="right">Наименование</td>
            <td>
                <input name="tourData[t_Name]" value="{$Tour.t_Name}" type="text" placeholder="Произвольное наименование турнира" size="45" maxlength="255">
            </td>
        </tr>
        <tr>
            <td align="right">Дата турнира</td>
            <td>
                <input id="TourDatePicker"  name="tourData[t_Date]" type="text" placeholder="дд.мм.гггг" size="10" maxlength="10">
                время
                <input id="TourTime" name="tourData[t_Time]" value="{if isset($Tour.t_DateTime)}{$Tour.t_DateTime->format('H:i')}{else}00:00{/if}" type="text" placeholder="ЧЧ:MM" size="5" maxlength="5" pattern="[0-9]{ldelim}2{rdelim}:[0-9]{ldelim}2{rdelim}">
            </td>
        </tr>
        <tr>
            <td align="right">Место проведения</td>
            <td>
                <select name="tourData[t_CourtId]" >
                    {foreach $Courts as $court}
                        <option value="{$court.c_Id}"{if $court.c_Id==$Tour.t_CourtId} selected{/if}>{$court.c_Name}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td align="right">Коэффициент значимости турнира</td>
            <td>
                <input name="tourData[t_Coefficient]" value="{$Tour.t_Coefficient}" type="text" size="5" maxlength="5">
            </td>
        </tr>
        <tr>
            <td align="right">Ссылка на обсуждение турнира в форуме</td>
            <td>
                <input name="tourData[t_URL]" value="{$Tour.t_URL}" type="text" size="96" maxlength="255">
            </td>
        </tr>
        <tr>
            <td align="right">Организатор турнира</td>
            <td>
                <select name="tourData[t_TourOrganizerId]" >
                    {foreach $TourOrganizers as $tourOrganizer}
                        <option value="{$tourOrganizer.to_Id}"{if $tourOrganizer.to_Id==$Tour.t_TourOrganizerId} selected{/if}>{$tourOrganizer.to_Name}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr title="1 - турнир в личном разряде">
            <td align="right">Размер команды</td>
            <td>
                <input name="tourData[t_TeamSize]" value="{$Tour.t_TeamSize|default:1}" type="text" size="1" maxlength="1">
            </td>
        </tr>
        <tr title="0 - без ограничения">
            <td align="right">Ограничение количества игроков/пар/команд</td>
            <td>
                <input name="tourData[t_TourMaxPlayersCount]" value="{$Tour.t_TourMaxPlayersCount|default:0}" type="text" size="3" maxlength="3">
            </td>
        </tr>
        <tr>
            <td align="right">Пользователь форума - организатор турнира</td>
            <td>
                <select name="tourData[t_ForumUserIdAdmin]" >
                    <option>- не определён -</option>
                    {foreach $ForumUsers as $forumUser}
                        <option value="{$forumUser.user_id}"{if $forumUser.user_id==$Tour.t_ForumUserIdAdmin} selected{/if}>{$forumUser.username}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
    </table>
    <div>
        {if $Tour}
            {$TourRatings = $Tour->TourRatingsList()}
        {/if}
        <table>
            <tr class="cat">
                <td>Id</td>
                <td>Рейтинг</td>
                <td>Влияет</td>
            </tr>
            {foreach $Ratings as $rating}
                <tr>
                    <td>{$rating.r_Id}</td>
                    <td>{$rating.r_Name}</td>
                    <td align="center"><input name="Influence[{$rating.r_Id}]" type="checkbox" {if $TourRatings[$rating.r_Id]}checked{/if}></td>
                </tr>
            {/foreach}
        </table>
    </div>
    <input type="submit" value="Сохранить">
</form>

<script>
    $(function() {
        $( "#TourDatePicker" ).datepicker({ldelim}firstDay: 1{rdelim});
        $( "#TourDatePicker" ).datepicker("option", "dateFormat", 'dd.mm.yy');
        //todo: установить значения выпадающих списков via jquery

        {if isset($Tour.t_DateTime)}
            $( "#TourDatePicker" ).datepicker("setDate", "{$Tour->t_DateTime->format('d.m.Y')}" );
        {/if}
    });
  </script>

{/block}
