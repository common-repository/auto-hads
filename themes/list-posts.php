<div class='wrap'>
    <h2><?php _e('AutoPosts Manager - List', 'autohads')?></h2>

    <?php
    if ($key_word != "") {
        echo "<div class='updated'><p>Found " . number_format($total) . " results for: $key_word &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='" . $urllist . "'>Exit Search</a></p></div>";
    }
    // column names
    $condition = array('search' => $key_word);
    ?>


    <form action='<?php echo $urllist ?>' method='post' name='search'>
        <p class='search-box'>
            <input type='search' name='search' placeholder='<?php _e('Search &hellip;', 'autohads')?>' value='' />
        </p>
    </form>

    <?php if (empty($items)) { ?>
        <table class='wp-list-table striped tags hads-tables'><tr><th><?php _e('No results found.', 'autohads')?></th></tr></table>

    <?php } else { ?>
        <table class='wp-list-table widefat fixed striped tags'>
            <thead>
                <th><?php _e('Link', 'autohads')?></th>
                <th class="auto-hads-col-1"><?php _e('Type', 'autohads')?></th>
                <th class="auto-hads-col-4"></th>
            </thead>
            <tbody>
                <?php
                foreach ($items as $row) {
                    ?>
                    <tr>
                        <td>
                            <?php echo $row->link;?>
                        </td>
                        <td>
                            <?php echo ($row->type == 'posts'? __('Posts','autohads') :  __('Products','autohads') );?>
                        </td>
                        <td class="auto-hads-col-1">
                            <a class="button button-primary" href="<?php echo esc_url('admin.php?page=auto-hads-'.$row->type.'&id='.$row->id)?>"><?php _e('Edit & Run Manual', 'autohads')?></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>

        <div class='tablenav bottom'>
            <div class='tablenav-pages'>
                <span class='displaying-num'> <?php _e('Total', 'autohads')?> <?php echo number_format($total) ?></span>
                <span class='pagination-links'>
                    <?php
                    $qry = http_build_query(array('search' => $key_word, 'orderby' => $orderby, 'order' => $order));
                    if (0 < $begin_row) {
                        echo "<a title='first page' href='" . $urllist . "&#038beginrow=0&#038" . $qry . "'>&laquo;</a>";
                        echo "<a title='previous page' href=" . $urllist . "&#038beginrow=" . ($begin_row - $rows_per_page) . "&#038" . $qry . "'>&lsaquo;</a>";
                    } else {
                        echo "<a class='first-page disabled' title='first page'>&laquo;</a>";
                        echo "<a class='prev-page disabled' title='previous page'>&lsaquo;</a>";
                    }
                    echo "<span class='paging-input'> " . number_format($begin_row + 1) . " - <span class='total-pages'>" . number_format($next_begin_row) . " </span></span>";
                    if ($next_begin_row < $total) {
                        echo "<a class='next-page' title='next page' href='" . $urllist . "&#038beginrow=$next_begin_row&#038" . $qry . "'>&rsaquo;</a>";
                        echo "<a class='last-page' title='last page' href='" . $urllist . "&#038beginrow=$last_begin_row&#038" . $qry . "'>&raquo;</a>";
                    } else {
                        echo "<a class='next-page disabled' title='next page'>&rsaquo;</a>";
                        echo "<a class='last-page disabled' title='last page'>&raquo;</a>";
                    }
                    ?>
                </span>
            </div><br class='clear' />
        </div>
    <?php } ?>
</div>