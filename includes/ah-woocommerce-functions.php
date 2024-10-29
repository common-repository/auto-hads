<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function auto_hads_woocommerce_content_data() {
    if (!isset($_POST['link_target'])) {
        return false;
    }
    
    $link_target = auto_hads_request('link_target');

    $tag_warper = auto_hads_request('tag_warper');
    $tag_link = auto_hads_request('tag_link');
    $tag_title = auto_hads_request('tag_title');
    $tag_short_description = auto_hads_request('tag_short_description');
    $tag_short_description_replace = auto_hads_request('tag_short_description_replace');
    $tag_image = auto_hads_request('tag_image');
    $tag_price = auto_hads_request('tag_price');
    $tag_sale_chk = absint(auto_hads_request('tag_sale_chk'));
    $tag_sale = auto_hads_request('tag_sale');
    $tag_price_replace = auto_hads_request('tag_price_replace');
    $pricereplaces = array($tag_price_replace);
    if($tag_price_replace)
    {
        $pricereplaces = str_split($tag_price_replace);
    }
    $tag_short_description_replaces = array($tag_short_description_replace);
    if($tag_short_description_replace)
    {
        $tag_short_description_replaces = explode(',', $tag_short_description_replace);
    }

    $elements = auto_hads_dom_url($tag_warper, $link_target);
    $lists = array();
    $htmltext = '';
    $i = 0;
    if(!$elements){
            return array(
            'list' => array(), 
            'html' => '', 
            'count' => 0
        );
    }
    foreach ($elements as $item) {
        $url = '';
        $src = '';
        $title = '';
        $price = '';
        $pricesale = '';
        $short ='';
		
        if (!empty($tag_title)) {
            foreach ($item->find($tag_title) as $el) {
                $title = $el->plaintext;
            }
        }
		if(!empty($title) && ! post_exists($title)){
			if (!empty($tag_link)) {
				foreach ($item->find($tag_link) as $link) {
					$url = auto_hads_check_url($link->href,$link_target);
				}
			}
			if (!empty($tag_image)) {
				foreach ($item->find($tag_image) as $img) {
					$src = auto_hads_check_url($img->src, $link_target);
					break;
				}
			}
			if (!empty($tag_short_description)) {
				foreach ($item->find($tag_short_description) as $el) {
					$short = str_replace($tag_short_description_replaces, '', $el->plaintext);
				}
			}
			
			if ($tag_sale_chk) {
				if (!empty($tag_price)) {
					foreach ($item->find($tag_price) as $k => $el) {
						if ($k == 0) {
							$price = str_replace($pricereplaces, '', $el->plaintext);
						} else {
							$pricesale = str_replace($pricereplaces, '', $el->plaintext);
						}
					}
				}
			} else {
				if (!empty($tag_price)) {
					foreach ($item->find($tag_price) as $k => $el) {
						if ($k == 0) {
							$price = str_replace($pricereplaces, '', $el->plaintext);
						}
					}
				}
				if (!empty($tag_sale)) {
					foreach ($item->find($tag_sale) as $k => $el) {
						if ($k == 0) {
							$pricesale = str_replace($pricereplaces, '', $el->plaintext);
						}
					}
				}
			}

			//$desc =  auto_hads_get_link_data($url);

			
			$list = array();
			$list['url'] = $url;
			$list['src'] = $src;
			$list['skip'] = 0;
			$list['title'] = $title;
			$list['short'] = $short;
			$list['price'] = $price;
			$list['pricesale'] = $pricesale;
			$lists[] = $list;
		}
    }
    $reverse_lists = array();
    
    $count = count($lists);
    for ($j=$count - 1; $j >= 0; $j--) {
        
        $list = $lists[$j];
        $htmltext .= '<tr class="col-url"><td colspan="4">' . $list['url'] . ' ||-----> <button class="get-my-info" data-id="' . $list['url'] . '">Test Description</button></td></tr>';
        $htmltext .= '<tr>';
        $htmltext .= '<td class="auto-hads-col-1">'
                . '<input class="edits-box textbox edit-image" value="' . $list['src'] . '" data-id="' . $j . '" data-key="src" type="text">'
                . '<img class="edit-image-src" src="' . $list['src'] . '"/>'
                . '</td>';
        $htmltext .= '<td><input class="edits-box textbox edit-title" value="' . $list['title'] . '" data-id="' . $j . '" data-key="title" type="text"></td>';
        $htmltext .= '<td><textarea class="edits-box textarea edit-short" rows="3" data-id="' . $j . '" data-key="short">' . $list['short'] . '</textarea></td>';
        $htmltext .= '<td><input class="edits-box textbox edit-price" value="' . $list['price'] . '" data-id="' . $j . '" data-key="price" type="text"></td>';
        $htmltext .= '<td><label><input class="edits-box edit-sale"  data-id="' . $j . '" data-key="skip" value="1" type="checkbox"> '.__('Skip item', 'autohads').'</label></td>';
        $htmltext .= '</tr>';
        
        $reverse_lists[] = $list;
    }
    return array('list' => $reverse_lists, 'html' => $htmltext, 'count' => $count);
}


function auto_hads_woocommerce_detail_data($istest = false) {
    $url = auto_hads_request('url');
    $request = wp_remote_get($url);
    $htmlContent = wp_remote_retrieve_body( $request );
    
    $tag_short= auto_hads_request('tag_short');
    $tag_short_replace = auto_hads_request('tag_short_replace');
    if(!empty($tag_short_replace))
    {
        $tag_short_replaces = explode(',', $tag_short_replace);
    }
    $tag_warper_content = auto_hads_request('tag_warper_content');
    $tag_content_replace = auto_hads_request('tag_content_replace');
    
    
    $tag_skip_element = auto_hads_request('tag_skip_element');
    $tag_keep_tags = absint(auto_hads_request('tag_keep_tags'));
    $tag_download_chk = absint(auto_hads_request('tag_download_chk'));
    
    $tag_keep_table = absint(auto_hads_request('tag_keep_table'));
    
    $tag_tags_chk = absint(auto_hads_request('tag_tags_chk'));
    $tag_tags = auto_hads_request('tag_tags');
    $tag_tags_replace = auto_hads_request('tag_tags_replace');
    
    $tag_image_chk = absint(auto_hads_request('tag_image_chk'));
    $tag_gallery_chk = absint(auto_hads_request('tag_gallery_chk'));
    
    $tag_images_chk = ($tag_image_chk || $tag_gallery_chk);
    $tag_warper_image = auto_hads_request('tag_warper_image');
    
    $tag_keep_img = absint(auto_hads_request('tag_keep_img'));
    $tag_images_alignment = auto_hads_request('tag_images_alignment');
    
    $tag_content_replaces = $tag_content_replace;
   
    $tag_tags_replaces = array($tag_tags_replace);
    if(!empty($tag_tags_replace))
    {
        $tag_tags_replaces = explode(',', $tag_tags_replace);
    }
    $textcontent = '';
    if(!empty($tag_warper_content))
    {
        $elements = auto_hads_dom_html_parser($tag_warper_content,$url, $htmlContent);

        foreach ($elements as $el) {
            $textcontent = $el->getContentText($tag_content_replaces,$tag_images_alignment, $tag_skip_element, $tag_keep_img, $tag_keep_tags, $tag_keep_table,$tag_download_chk);
            break;
        }
    }
    $text_short = '';
    if(!empty($tag_short))
    {
        $elements_short = auto_hads_dom_html_parser($tag_short,$url, $htmlContent);

        foreach ($elements_short as $el) {
            $text_short = str_replace($tag_short_replaces, '', $el->plaintext);
            break;
        }
    }
    
    $tags_array = array();
    if(!empty($tag_tags) && $tag_tags_chk)
    {
        $els_tags = auto_hads_dom_html_parser($tag_tags,$url, $htmlContent);
    
        foreach ($els_tags as $el) {
            $tag = str_replace($tag_tags_replaces, '', $el->plaintext);
            $tags_array[] = $tag;
        }
    }
    $images_array = array();
    if(!empty($tag_warper_image) && $tag_images_chk)
    {
        $els_images = auto_hads_dom_html_parser($tag_warper_image,$url, $htmlContent);
        foreach ($els_images as $img) {
            $src = auto_hads_check_url($img->src, $url);
            $images_array[] = $src;
        }
    }
    if($istest)
    {
        $short_Return = '';
        if(!empty($text_short)){
            $text_short = '<h3 class="auto-hads-tags-title">'.  __('Short Description', $textcontent).'</h3>';
            $short_Return .= '<div>'.$text_short.'</div>';
        }
        $tags_Return = '';
        if(count($tags_array) && $tag_tags_chk)
        {
            $tags_Return = '<h3 class="auto-hads-tags-title">'.  __('Here custom tags will insert to Database', $textcontent).'</h3><ul class="auto-hads-tags-list clearfix">';
            foreach ($tags_array as $v) {
                $tags_Return .= '<li><a class="tag" href="#">'.$v.'</a></li>';
            }
            $tags_Return .= '</ul>';
        }
        $images_Return = '';
        if(count($images_array) && $tag_images_chk)
        {
            $images_Return = '<h3 class="auto-hads-tags-title">'.  __('Here list images will insert to Gallery or Featured Image', $textcontent).'</h3>';
            foreach ($images_array as $v) {
                $images_Return .= '<div><img class="auto-hads-images-item" src="'.$v.'"/></div>';
            }
        }
        $htmlReturn = $textcontent.$short_Return.$tags_Return.$images_Return;
        return array('post'=> wpautop($htmlReturn));
    }
    
    return array(
        'post'=> $textcontent,
        'tags' => $tags_array,
        'images' => $images_array,
        'short' => $text_short
    );
}

function auto_hads_woocommerce_import_post_data() {
    global $wpdb;
    if (!isset($_POST['cats'])) {
        return false;
    }
    $thetitle = auto_hads_request('title');
    
    if(post_exists($thetitle))
    {
        return true;
    }
    
    $price =  auto_hads_request('price');
    $src =  auto_hads_request('src');
    $pricesale = auto_hads_request('pricesale');
    $short = auto_hads_request('short');
    $tag_image_chk = absint(auto_hads_request('tag_image_chk'));
    $tag_gallery_chk = absint(auto_hads_request('tag_gallery_chk'));
    
    
    $cats = auto_hads_request('cats');
    $brands = auto_hads_request('brands');
    
    $postinfo = auto_hads_woocommerce_detail_data();
    
    $postcontent = $postinfo['post'];
    $tags = $postinfo['tags'];
    
    $galleries = $postinfo['images'];
    
    $cat_ids = array_map('intval', $cats);
    $brands_ids = array();
    if($brands){
        $brands_ids = array_map('intval', $brands);
    }
    
    
    if(empty($short)){
        $short = $postinfo['short'];
    }
    
    
    $post = array(
        'post_content' => $postcontent,
        'post_status' => "publish",
        'post_title' => $thetitle,
        'post_parent' => '',
        'post_type' => "product",
        'post_excerpt' => $short,
    );
//Create post
    $post_id = wp_insert_post($post);
    
    if($tag_image_chk && $galleries)
    {
        $src = $galleries[0];
    }
    

    if ($cat_ids) {
        $cat_ids = array_unique($cat_ids);
        wp_set_object_terms($post_id, $cat_ids, 'product_cat');
    }

    if ($brands_ids) {
        $brands_ids = array_unique($brands_ids);
        wp_set_object_terms($post_id, $brands_ids, 'pwb-brand');
    }

    
    if($tags){
        wp_set_post_tags( $post_id, $tags );
    }
    
    update_post_meta($post_id, '_visibility', 'visible');
    update_post_meta($post_id, '_stock_status', 'instock');
    update_post_meta($post_id, 'total_sales', '0');
    update_post_meta($post_id, '_downloadable', 'no');
    update_post_meta($post_id, '_virtual', 'no');
    update_post_meta($post_id, '_regular_price', $price);
    update_post_meta($post_id, '_sale_price', $pricesale);
    update_post_meta($post_id, '_purchase_note', "");
    update_post_meta($post_id, '_featured', "no");
    update_post_meta($post_id, '_weight', "");
    update_post_meta($post_id, '_length', "");
    update_post_meta($post_id, '_width', "");
    update_post_meta($post_id, '_height', "");
    update_post_meta($post_id, '_sku', "");
    update_post_meta($post_id, '_product_attributes', array());
    update_post_meta($post_id, '_sale_price_dates_from', "");
    update_post_meta($post_id, '_sale_price_dates_to', "");
    update_post_meta($post_id, '_price', $price);
    update_post_meta($post_id, '_sold_individually', "");
    update_post_meta($post_id, '_manage_stock', "no");
    update_post_meta($post_id, '_backorders', "no");
    update_post_meta($post_id, '_stock', "");

    update_post_meta($post_id, '_downloadable_files', '');
    update_post_meta($post_id, '_download_limit', '');
    update_post_meta($post_id, '_download_expiry', '');
    update_post_meta($post_id, '_download_type', '');
    try {
        if ($post_id) {
            $attachs = auto_hads_add_attachment_url_resize($src);
            add_post_meta($post_id, '_thumbnail_id', $attachs['attach_id']);
        }

        if($tag_gallery_chk && $galleries){
            $attach_ids = array();

            foreach ($galleries as $src) {
                $attachs = auto_hads_add_attachment_url_resize($src);
                if(!empty($attachs['attach_id']))
                {
                    $attach_ids[] = $attachs['attach_id'];
                }
            }
            $image_gallery = implode(',', $attach_ids);
            update_post_meta($post_id, '_product_image_gallery', $image_gallery);
        }
        else{
            update_post_meta($post_id, '_product_image_gallery', '');
        }
    } catch (Exception $ex) {
        return true;
    }
    return true;
}
