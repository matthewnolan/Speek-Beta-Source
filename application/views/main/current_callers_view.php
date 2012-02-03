<?php if($organizer_active == TRUE):?>
<table>
    <tr>
        <th>Caller</th>
        <th></th>
    </tr>
    <?php foreach($participants as $participant):?>
        <?php if($participant->active == TRUE):?>
    <tr>
        <td><?=$participant->number?></td>
        <td>
        <input type="submit" value="remove" onclick="removeCaller('<?=$call_id?>','<?=$participant->number?>'); return false;" />
    </td>
        <?php endif;?>
    <?php endforeach;?>
</table>
<?php endif;?>