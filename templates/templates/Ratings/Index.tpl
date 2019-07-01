{* Smarty *}

{extends file="Layout.tpl"}

{block name=body}

    <table border="1" id="RatingList">
        <tr align="center">  <!-- заголовок -->
            <th>Id</th>
            <th>Название</th>
            <th>Описание</th>
        </tr>
        
        {foreach $Ratings as $rating}
        <tr id="RatingTR_{$rating->r_Id}">
            <td align="right">  {$rating->r_Id}</td>
            <td>                {$rating->r_Name|escape}</td>
        	<td>                {$rating->r_Description|escape}</td>
        </tr>
        {/foreach}
    </table>

{/block}
