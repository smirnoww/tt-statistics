{* Smarty *}
{* For including in New and Edit form *}
    <table>
        <tr>
            <td align="right">Наименование</td>
            <td>
                <input name="courtData[c_Name]" value="{$Court.c_Name|escape}" type="text" placeholder="Уникальное наименование площадки" size="45" maxlength="255">
            </td>
        </tr>
        <tr>
            <td align="right">Адрес</td>
            <td>
                <input name="courtData[c_Address]" value="{$Court.c_Address|escape}" type="text" placeholder="Адрес" size="45" maxlength="512">
            </td>
        </tr>
        <tr>
            <td align="right">Контакты</td>
            <td>
                <input name="courtData[c_Contacts]" value="{$Court.c_Contacts|escape}" type="text" placeholder="Контактная информация" size="45" maxlength="512">
            </td>
        </tr>
        <tr>
            <td align="right">URL</td>
            <td>
                <input name="courtData[c_URL]" value="{$Court.c_URL}" type="text" placeholder="Ссылка" size="45" maxlength="512">
            </td>
        </tr>
        <tr>
            <td align="right">Описание</td>
            <td>
                <textarea name="courtData[c_Description]" rows="10" cols="45" placeholder="Подробное описание условий">{$Court.c_Description|escape}</textarea>
            </td>
        </tr>
    </table>
