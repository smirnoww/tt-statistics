{* Smarty *}

{extends file="Layout.tpl"}

{block name=body}

    <table border="1" id="TourTypeList">
        <tr align="center">  <!-- заголовок -->
            <th>Id</th>
            <th>Название</th>
        </tr>
        
        {foreach $TourTypes as $tourtype}
        <tr id="TourTypeTR_{$tourtype->ttype_Id}">
            <td align="right">  {$tourtype->ttype_Id}</td>
            <td>                {$tourtype->ttype_Name}</td>
        </tr>
        {/foreach}
    </table>

{/block}
