{* Smarty *}
{* В этом шаблоне выводится результат обсчёта рейтинга *}
    <table border="1">
        <tr align="center">  <!-- заголовок -->
            <th>№ п/п</th>
            <th>Операция</th>
            <th>Результат</th>
        </tr>
        
        {foreach $CalcLog as $log}
        <tr id="RatingTR_{$rating->r_Id}">
            <td align="right">  {$log.Id}</td>
            <td>                {$log.Operation}</td>
        	<td>                {$log.Info}</td>
        </tr>
        {/foreach}
    </table>
