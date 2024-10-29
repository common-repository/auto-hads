<?php ?>
<div class="wrap">
    <h2><?php esc_html_e('Manual Products of Woocommerce', 'auto-hadsportfolio'); ?></h2>

    <div id="wpcom-stats-meta-box-container" class="metabox-holder">
        <?php
        wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
        wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
        ?>
        <div class="postbox-container-auto-hads postbox-auto-hads" style="width: 100%;">
            <div id="normal-sortables" class="meta-box-sortables ui-sortable auto-hads-postbox-warp clearfix">
                <form name="auto-hadsconf" id="hads-conf" action='<?php echo $urlpost ?>' method='post' >
                    <div id="referrers" class="postbox auto-hads-has-sidebar">
                        <h3 class="hndle"><span><?php esc_html_e('Imports localhost', 'autohads'); ?></span></h3>
                        <div class="inside">
                            <div class="hads-row">
                                <div id="titlediv">
                                    <label class="lable-title">Link Target:</label>
                                    <div id="titlewrap">
                                        <input class="auto-hads-bot url-title" placeholder="http://example-your-target-domain.com/news" size="30" name="link_target" value="<?php echo esc_attr($link_target) ?>" id="title" spellcheck="true"  type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="hads-row">
                                <div class="hads-lable-group"><?php _e('All Elements in Page List', 'autohads') ?>  </div>
                                <table class="wp-list-table widefat fixed striped tags hads-tables">
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Warper Element', 'autohads') ?></label> </td>
                                        <td><input class="auto-hads-bot tags tag-warper" placeholder="#contaner ul.products li" name="tag_warper" value="<?php echo esc_attr($tag_warper) ?>" type="text"></td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Link Element', 'autohads') ?></label> </td>
                                        <td><input class="auto-hads-bot tags tag-link" placeholder=".item-title a" name="tag_link" value="<?php echo esc_attr($tag_link) ?>" type="text"></td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Title Element', 'autohads') ?></label> </td>
                                        <td><input class="auto-hads-bot tags tag-title" placeholder=".item-title a" name="tag_title" value="<?php echo esc_attr($tag_title) ?>" type="text"></td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Image Element', 'autohads') ?></label> </td>
                                        <td><input class="auto-hads-bot tags tag-image" name="tag_image" placeholder=".item-thumb img" value="<?php echo esc_attr($tag_image) ?>" type="text"></td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Short Description Element', 'autohads') ?></label> </td>
                                        <td>
                                            <input class="auto-hads-bot tags tag-short-description" placeholder=".item-title .desc" name="tag_short_description" value="<?php echo esc_attr($tag_short_description) ?>" type="text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Short Description Replace', 'autohads') ?></label> </td>
                                        <td><input class="auto-hads-bot tags tag-short-description-replace" name="tag_short_description_replace" value="<?php echo esc_attr($tag_short_description_replace) ?>"  placeholder="<?php _e('abc,def... to empty', 'autohads') ?>" type="text"></td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Price Element', 'autohads') ?></label> </td>
                                        <td>
                                            <input class="auto-hads-bot tags tag-price" placeholder="span.price" name="tag_price" value="<?php echo esc_attr($tag_price) ?>" type="text"> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Price Sale Element Index 2', 'autohads') ?></label> </td>
                                        <td><input class="auto-hads-bot tag-sale-chk" name="tag_sale_chk" type="checkbox" value="1" <?php echo checked('1', $tag_sale_chk) ?>></td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Price Replace', 'autohads') ?></label> </td>
                                        <td><input class="auto-hads-bot tags tag-price-replace" name="tag_price_replace" value="<?php echo esc_attr($tag_price_replace) ?>" placeholder="<?php _e('$,#... to empty', 'autohads') ?>" type="text"></td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Price Sale Element', 'autohads') ?></label> </td>
                                        <td>
                                            <input class="auto-hads-bot tags tag-sale" placeholder="span.price-sale" name="tag_sale" value="<?php echo esc_attr($tag_sale) ?>" type="text"> 
                                        </td>
                                    </tr>
                                </table>
                                <div class="hads-lable-group"> <?php _e('All Elements in Detail Page', 'autohads') ?> </div>
                                <table class="wp-list-table widefat fixed striped tags hads-tables">
                                    
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Detail Short Description Element', 'autohads') ?></label> </td>
                                        <td>
                                            <input class="auto-hads-bot tags tag-short-description" placeholder="#contaner .item-short-desc" name="tag_short" value="<?php echo esc_attr($tag_short) ?>" type="text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Detail Short Description Replace', 'autohads') ?></label> </td>
                                        <td><input class="auto-hads-bot tags tag-short-description-replace" name="tag_short_replace" value="<?php echo esc_attr($tag_short_replace) ?>"  placeholder="<?php _e('abc,def... to empty', 'autohads') ?>" type="text"></td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Warper Content Element', 'autohads') ?></label> </td>
                                        <td><input class="auto-hads-bot-detail tags tag-warper-detail" placeholder="#contaner .item-description" name="tag_warper_content" value="<?php echo esc_attr($tag_warper_content) ?>" type="text"></td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Content Remove', 'autohads') ?></label> </td>
                                        <td>
                                            <textarea rows="1" class="auto-hads-bot-detail tags tag-content-replace" type="textarea" name="tag_content_replace" placeholder="<?php _e('The content will be removed per line...', 'autohads') ?>"><?php echo $tag_content_replace ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Keep Images Element', 'autohads') ?></label> </td>
                                        <td>
                                            <input class="auto-hads-bot-detail tag-keep-img" name="tag_keep_img"  value="1" type="checkbox" <?php echo checked('1', $tag_keep_img) ?>/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Download Content Image', 'autohads') ?></label> </td>
                                        <td>
                                             <input class="auto-hads-bot-detail tag-download-chk" name="tag_download_chk" type="checkbox" value="1" <?php echo checked('1', $tag_download_chk) ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('First Image as Featured Image', 'autohads') ?></label> </td>
                                        <td>
                                             <input class="auto-hads-bot-detail tag-image-chk" name="tag_image_chk" type="checkbox" value="1" <?php echo checked('1', $tag_image_chk) ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Images Alignment', 'autohads') ?></label> </td>
                                        <td>
                                            <select class="auto-hads-bot-detail tag-images-alignment" name="tag_images_alignment">
                                                <option value="" <?php echo selected('', $tag_images_alignment) ?>><?php _e('None Alignment', 'autohads') ?></option>
                                                <option value="left" <?php echo selected('left', $tag_images_alignment) ?>><?php _e('Left Alignment', 'autohads') ?></option>
                                                <option value="center" <?php echo selected('center', $tag_images_alignment) ?>><?php _e('Center Alignment', 'autohads') ?></option>
                                                <option value="right" <?php echo selected('right', $tag_images_alignment) ?>><?php _e('Right Alignment', 'autohads') ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Enable Gallery Image', 'autohads') ?></label> </td>
                                        <td>
                                             <input class="auto-hads-bot-detail tag-gallery-chk" name="tag_gallery_chk" type="checkbox" value="1" <?php echo checked('1', $tag_gallery_chk) ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Warper Images Element', 'autohads') ?></label> </td>
                                        <td><input class="auto-hads-bot-detail tags tag-warper-image" name="tag_warper_image" value="<?php echo esc_attr($tag_warper_image) ?>" type="text"></td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Keep Tags HTML', 'autohads') ?></label> </td>
                                        <td>
                                            <label class="auto-hads-desc"><input class="auto-hads-bot-detail tag-keep-tags" name="tag_keep_tags"  value="1" type="checkbox" <?php echo checked('1', $tag_keep_tags) ?>/>
                                                <?php _e('Some tags we keeps in content : h1, h2, h3, h4, h5, h6, b, i, strong, em, ul, ol', 'autohads')?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Keep Table HTML', 'autohads') ?></label> </td>
                                        <td>
                                            <label class="auto-hads-desc"><input class="auto-hads-bot-detail tag-keep-table" name="tag_keep_table"  value="1" type="checkbox" <?php echo checked('1', $tag_keep_table) ?>/>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Skip Element', 'autohads') ?></label> </td>
                                        <td>
                                            <input class="auto-hads-bot-detail tags tag-skip-element" name="tag_skip_element" placeholder="Will skip with exp: .class-ads" value="<?php echo esc_attr($tag_skip_element) ?>" type="text"/>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Enable Tags', 'autohads') ?></label> </td>
                                        <td>
                                            <input class="auto-hads-bot-detail tag-tags-chk" name="tag_tags_chk" type="checkbox" value="1" <?php echo checked('1', $tag_tags_chk) ?>> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Warper Tags Element', 'autohads') ?></label> </td>
                                        <td>
                                            <input class="auto-hads-bot-detail tags tag-tags" name="tag_tags" placeholder="#contaner .tags a" value="<?php echo esc_attr($tag_tags) ?>" type="text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="hads-first-row"> <label><?php _e('Tags Replace', 'autohads') ?></label> </td>
                                        <td>
                                             <input class="auto-hads-bot-detail tag-tags-replace" name="tag_tags_replace" placeholder="<?php _e('abc,def... to empty', 'autohads') ?>"  value="<?php echo esc_attr($tag_tags_replace) ?>" type="text">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="message">
                                <span class="message-url"></span>
                            </div>
                            <div class="hads-row">
                                <div class="">
                                    <input type="hidden" value="<?php echo esc_attr($url_id) ?>" name="id"/>
                                    <input type="hidden" value="woocommerce" name="type"/>
                                    <input type="hidden"  name="fnc" class="auto-hads-bot auto-hads-bot-detail" value="woocommerce"/>
                                    <input type="button" class="button button-action auto-hads-get-posts-from-url" value="<?php _e('Get Data from URL', 'autohads') ?>" /> 
                                    <input type="submit" name="submit" class="button button-primary" value="<?php _e('Save Autorun', 'autohads') ?>" /> 
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="postbox auto-hads-sidebar">
                        <h3 class="hndle"><span><?php esc_html_e('Category', 'autohads'); ?></span></h3>
                        <div class="category-sidebar-2">
                            <div class="tabs-panel">
                                <div class="checkbox-container">
                                    <ul class="categorychecklist hads-categories-box">
                                        <?php
                                        $args = array(
                                            'selected_cats' => $post_category,
                                            'descendants_and_self' => 0,
                                            'popular_cats' => false,
                                            'taxonomy' => 'product_cat',
                                            'checked_ontop' => false);
                                        ?>
                                        <?php auto_hads_terms_checklist(0, $args); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <h3 class="hndle"><span><?php esc_html_e('Brands', 'autohads'); ?></span></h3>
                        <div class="category-sidebar-2">
                            <div class="tabs-panel">
                                <div class="checkbox-container">
                                    <ul class="categorychecklist hads-brands-box">
                                        <?php
                                        $args = array(
                                            'selected_cats' => $post_brands,
                                            'descendants_and_self' => 0,
                                            'popular_cats' => false,
                                            'taxonomy' => 'pwb-brand',
                                            'checked_ontop' => false);
                                        ?>
                                        <?php auto_hads_terms_checklist(0, $args); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="postbox-container-auto-hads-insert postbox-auto-hads" style="width: 100%;">
                <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                    <div id="referrers" class="postbox ">
                        <div class="handlediv" title="Click to toggle"><br></div>
                        <h3 class="hndle"><span><?php esc_html_e('Imports', 'autohads'); ?></span></h3>
                        <form name="auto-hadsconf" id="hads-conf" method="POST">
                            <div class="inside">
                                <div class="hads-row">
                                    <div id="auto_hads_projess_bar" style="width: 100%;height: 20px;background: #999;position: relative;box-sizing: border-box;">
                                        <div id="auto_hads_projess_bar_in" style="width: 0%;background: blueviolet;height: 20px;box-sizing: border-box;">

                                        </div>
                                        <div id="auto_hads_results_post_format" style="position: absolute;color: #fff; width: 100%;text-align: center;top: 0;left: 0;box-sizing: border-box;">

                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div>
                                    <input type="button" class="button button-primary auto-hads-set-posts-fortmat" value="Run to insert data" /> 
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="postbox-auto-hads">
                <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                    <div id="referrers" class="postbox ">
                        <div class="handlediv" title="Click to toggle"><br></div>
                        <h3 class="hndle"><span><?php esc_html_e('Posts will insert database', 'autohads'); ?></span></h3>
                        <div class="inside" id="inside-content-post">
                            <table class="wp-list-table widefat fixed striped tags">
                                <thead>
                                    <tr>
                                        <th scope="col" class="auto-hads-col-1">
                                            <span>Images</span>
                                        </th>
                                        <th scope="col" class="auto-hads-col-3">
                                            <span>Title</span>
                                        </th>
                                        <th scope="col" class="auto-hads-col-3">
                                            <span>Short Description</span>
                                        </th>
                                        <th scope="col" class="auto-hads-col-4">
                                            <span>Price</span>
                                        </th>
                                        <th scope="col" class="auto-hads-col-2">
                                            <span>Skip</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="auto-hads-content-body-url">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="auto-hads-test-data auto-hads-panel">
    <div class="auto-hads-panel-overplay"></div>
    <div class="auto-hads-test-data-warp">
        <button type="button" class="auto-hads-close"><span class="dashicons dashicons-no-alt"></span></button>
        <div class="auto-hads-test-data-content">

        </div>
    </div>
</div>
<div class="auto-hads-message-data auto-hads-panel">
    <div class="auto-hads-panel-overplay"></div>
    <div class="auto-hads-message-warp">
        <div class="auto-hads-message-data-content">
        </div>
    </div>
</div>
<div class="auto-hads-loading loading-url">
    <div class="auto-hads-loading-warp">
        <div class="auto-hads-loading-content">
            <img class="loading-img" src="<?php echo AUTO_HADS_PLUGIN_URL . 'images/loading.gif'; ?>" >
            <span><?php _e('Loading data...', 'autohads') ?></span>
        </div>
    </div>
</div>
