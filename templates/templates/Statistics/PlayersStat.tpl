{* Smarty *}
{extends file="Layout.tpl"}

{block name=body}

<h1>{$TourOrg.to_Name}</h1>
    <table class="tablebg" cellspacing="1">
        <tr class="cat">
            <th width="200">Игрок</th>
            <th>Турниров</th>
            <th>Призовых мест</th>
            <th>Рейтинг</th>
        </tr>
        {foreach $Players as $player}
        <tr class="row1">
            <td width="200"><img style="vertical-align:middle" height="24" src="images/profile.png"> {$player.p_Name}</td>
            <td align="center">{$player.TourCount}</td>
            <td align="center">
                {if $player.FirstPlaces}<img class="VertMidAlign" height="24" src="images/medal1.png"> x {$player.FirstPlaces}{/if}
                {if $player.SecondPlaces}<img class="VertMidAlign" height="24" src="images/medal2.png"> x {$player.SecondPlaces}{/if}
                {if $player.ThirdPlaces}<img class="VertMidAlign" height="24" src="images/medal3.png"> x {$player.ThirdPlaces}{/if}
            </td>
            <td align="right">{if $player.Rate}{$player.Rate|round:0}{else} - {/if}</td>
        </tr>
        {/foreach}
    </table>
{/block}