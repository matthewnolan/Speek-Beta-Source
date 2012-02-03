<table>
    <tr>
        <th>Previous Slugs</th>
        <th><a href="" onclick="hidePreviousSlugs(); return false;">Hide</a></th>
    </tr>
    <?php foreach($slugs as $slug):?>
    <tr>
        <td>
            <a href="<?=base_url().$slug->slug?>"><?=$slug->slug?></a>
        </td>
        <td>
            <?php if($current_slug != $slug->slug):?>
            <input type="submit" value="Delete" onclick="deleteSlug('<?=$slug->slug?>'); return false;"/>
            <?php else:?>
                <p>In Use</p>
            <?php endif;?>
        </td>
    </tr>
    <?php endforeach;?>
</table>