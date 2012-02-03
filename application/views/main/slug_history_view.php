<table>
    <tr>
        <th>Caller</th>
        <th>Joined</th>
        <th>Dropped</th>
    </tr>
    <?php foreach($participants as $participant):?>
    <tr>
        <td>
            <p><?=$participant->number?></p>
        </td>
        <td>
            <p><?=date('F n, Y',strtotime($participant->join))?>
            <br/>
                <?=date('g:i A',strtotime($participant->join))?>
            </p>
        </td>
        <td>
            <p>
                <?php
                
                if($participant->drop == 'incall')
                {
                    echo '<span class="in_call">In Call</span>';
                }
                else 
                {
                    echo date('F n, Y',strtotime($participant->drop));
                    echo '<br/>';
                    echo date('g:i A',strtotime($participant->drop));
                }
               
                ?>
            </p>
        </td>
    </tr>
    <?php endforeach;?>
</table>