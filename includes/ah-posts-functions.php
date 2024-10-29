<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function auto_hads_posts_content_data() {
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
    
    $tag_attribute_image = auto_hads_request('tag_attribute_image');
    if($tag_short_description_replace)
    {
        $tag_short_description_replaces = explode(',', $tag_short_description_replace);
    }
    $request = wp_remote_get($link_target);
    $htmlContent = wp_remote_retrieve_body( $request );
    //    $htmlContent = auto_hads_send_get_request($link_target);
    //    
    //            
    $elements = auto_hads_dom_html_parser($tag_warper,$link_target,$htmlContent);
    
    if(!$elements){
            return array(
            'list' => array(), 
            'html' => '', 
            'count' => 0
        );
    }
    $lists = array();
    $htmltext = '';
    $i = 0;
//    $html_test = '';
    foreach ($elements as $item) {
//        $html_test .= $item->innerHTML;
        $url = '';
        $src = '';
        $title = '';
        $short = '';
        if (!empty($tag_title)) {
            foreach ($item->find($tag_title) as $el) {
                $title =  $el->plaintext;
                break;
            }
        }
        if(!empty($title) && ! post_exists($title)){
          if (!empty($tag_link)) {
                foreach ($item->find($tag_link) as $link) {
                    $url = auto_hads_check_url($link->href,$link_target);
                    break;
                }
            }
            if (!empty($tag_image)) {

                foreach ($item->find($tag_image) as $img) {
                    $src = auto_hads_check_url($img->src, $link_target);
                    if(!empty($tag_attribute_image) && !auto_hads_is_image($src)){
                        $src = $img->get($tag_attribute_image);
                    }
                    break;
                }
            }
            if (!empty($tag_short_description)) {
                foreach ($item->find($tag_short_description) as $el) {
                    $short = str_replace($tag_short_description_replaces, '', $el->plaintext);
                    $short = preg_replace('~\r\n?~', "\n", $short);
                    break;
                }
            }

            $list = array();
            $list['url'] = $url;
            $list['title'] = $title;
            $list['src'] = $src;
            $list['short'] = $short;
            $list['skip'] = 0;
            $lists[$i] = $list;
            $i++;
        }
        
    }
    $reverse_lists = array();
    
   // return $html_test;
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
        $htmltext .= '<td><label><input class="edits-box edit-sale"  data-id="' . $j . '" data-key="skip" value="1" type="checkbox"> '.__('Skip item', 'autohads').'</label></td>';
        $htmltext .= '</tr>';
        
        $reverse_lists[] = $list;
    }
    return array(
        'list' => $reverse_lists, 
        'html' => $htmltext, 
        'count' => $count
    );
}




function auto_hads_posts_detail_data($istest = false) {
    $url = auto_hads_request('url');
    $htmlContent = auto_hads_send_get_request($url);
    
    
    $tag_warper_content = auto_hads_request('tag_warper_content');
    $tag_content_replace = auto_hads_request('tag_content_replace');
    $tag_skip_element = auto_hads_request('tag_skip_element');
    $tag_keep_tags = absint(auto_hads_request('tag_keep_tags'));
    
    $tag_download_chk = absint(auto_hads_request('tag_download_chk'));
    
    $tag_tags_chk = absint(auto_hads_request('tag_tags_chk'));
    $tag_tags = auto_hads_request('tag_tags');
    $tag_tags_replace = auto_hads_request('tag_tags_replace');
    
    $tag_keep_img = absint(auto_hads_request('tag_keep_img'));
    $tag_images_alignment = auto_hads_request('tag_images_alignment');
    
    $tag_image_chk = absint(auto_hads_request('tag_image_chk'));
    $tag_keep_table = absint(auto_hads_request('tag_keep_table'));
    
    $tag_content_replaces = array($tag_content_replace);
    if(!empty($tag_content_replace))
    {
        $tag_content_replaces = explode(',', $tag_content_replace);
    }
    
    $tag_tags_replaces = array($tag_tags_replace);
    if(!empty($tag_tags_replace))
    {
        $tag_tags_replaces = explode(',', $tag_tags_replace);
    }
    
    $elements = auto_hads_dom_html_parser($tag_warper_content,$url, $htmlContent);
    $textcontent = '';
    foreach ($elements as $el) {
        $textcontent = $el->getContentText($tag_content_replaces,$tag_images_alignment, $tag_skip_element, $tag_keep_img, $tag_keep_tags, $tag_keep_table,$tag_download_chk);
        break;
    }
    
    $tags_array = array();
    if(!empty($tag_tags) && $tag_tags_chk)
    {
        $els_tags = auto_hads_dom_html_parser($tag_tags, $htmlContent);
    
        foreach ($els_tags as $el) {
            $tag = str_replace($tag_tags_replaces, '', $el->plaintext);
            $tags_array[] = $tag;
        }
    }
    
    $images_array = array();
    if($tag_image_chk)
    {
        $els_images = auto_hads_dom_html_parser($tag_warper_content . ' img',$url, $htmlContent);
        foreach ($els_images as $img) {
            $src = auto_hads_check_url($img->src, $url);
            $images_array[] = $src;
        }
    }
    if($istest)
    {
        $tags_Return = '';
        if(count($tags_array) && $tag_tags_chk)
        {
            $tags_Return = '<h3 class="auto-hads-tags-title">'.  __('Here custom tags will insert to Database', $textcontent).'</h3><ul class="auto-hads-tags-list clearfix">';
            foreach ($tags_array as $v) {
                $tags_Return .= '<li><a class="tag" href="#">'.$v.'</a></li>';
            }
            $tags_Return .= '</ul>';
        }
        $htmlReturn = $textcontent.$tags_Return;
        return array('post'=> wpautop($htmlReturn));
    }
    
    return array(
        'post'=> $textcontent,
        'tags' => $tags_array,
        'images' => $images_array,
    );
}

function auto_hads_posts_import_post_data() {
    global $wpdb;
    if (!isset($_POST['cats'])) {
        return false;
    }
    $thetitle = auto_hads_request('title');
    
   if(post_exists($thetitle))
    {
        return true;
    }
    
    $url = auto_hads_request('url');
    $id = absint(auto_hads_request('id')) ;
    $src =  auto_hads_request('src');
    $cats = auto_hads_request('cats');
    $short =  auto_hads_request('short');
    $tag_image_chk = absint(auto_hads_request('tag_image_chk')) ;

    $posts_data = auto_hads_posts_detail_data();
    if(!empty($short))
    {
        $short = $short . '<!--more-->'.PHP_EOL.PHP_EOL;
    }
    
    $postcontent = $short.$posts_data['post'];
    $tags = $posts_data['tags'];
    $galleries = $posts_data['images'];
    $cat_ids = array_map('intval', $cats);
    
    
    $post = array(
        'post_content' => $postcontent,
        'post_status' => "publish",
        'post_title' => $thetitle,
        'post_parent' => '',
        'post_type' => "post",
    );

    
    if($tag_image_chk && empty($src) && $galleries)
    {
        $src = $galleries[0];
    }
    
//Create post
    $post_id = wp_insert_post($post);
    if ($post_id && $tag_image_chk) {

        $attachs= auto_hads_add_attachment_url_resize($src);
        add_post_meta($post_id, '_thumbnail_id', $attachs['attach_id']);
    }

    if ($cat_ids) {
        $cat_ids = array_unique($cat_ids);
        wp_set_object_terms($post_id, $cat_ids, 'category');
    }
    
    if($tags){
        wp_set_post_tags( $post_id, $tags );
    }
    
    $tabledb_post = $wpdb->base_prefix . 'auto_hads_posts';
    //	id 	hads_id 	post_id 	link 	image 	title 	intro 	price 	pricesale 	status ;
    $wpdb->insert($tabledb_post, array(
        'hads_id' => $id,
        'post_id' => $post_id,
        'link' => $url,
        'src' => $src,
        'title' => $thetitle,
        'status' => 1
        ));
    return true;
}


