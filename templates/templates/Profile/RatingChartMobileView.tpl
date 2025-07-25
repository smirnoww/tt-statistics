{* Smarty *}


{block name=body}

{* если есть история рейтинга, покажем данные*}
{if $RatingHistory|@count>0}

    {* Текущее значение рейтингка *}
    <table>
        <tr>
            <td width="120">Текущий рейтинг:</td>
            <td>{$RatingHistory[$RatingHistory|@count-1].pr_Rate|round:0}</td>
        </tr>
        <tr>
            <td width="120">Макс. рейтинг:</td>
            <td>{$MaxRatingHistoryRow.pr_Rate|round:0} ({$MaxRatingHistoryRow.pr_Date})</td>
        </tr>
    </table>

{else}
    История рейтинга пока отсутствует. После участия в турнире здесь появятся данные.
{/if}
{/block}