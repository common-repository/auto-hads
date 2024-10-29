<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Output an unordered list of checkbox input elements labelled with term names.
 *
 * Taxonomy-independent version of wp_category_checklist().
 *
 * @since 3.0.0
 * @since 4.4.0 Introduced the `$echo` argument.
 *
 * @param int          $post_id Optional. Post ID. Default 0.
 * @param array|string $args {
 *     Optional. Array or string of arguments for generating a terms checklist. Default empty array.
 *
 *     @type int    $descendants_and_self ID of the category to output along with its descendants.
 *                                        Default 0.
 *     @type array  $selected_cats        List of categories to mark as checked. Default false.
 *     @type array  $popular_cats         List of categories to receive the "popular-category" class.
 *                                        Default false.
 *     @type object $walker               Walker object to use to build the output.
 *                                        Default is a Walker_Category_Checklist instance.
 *     @type string $taxonomy             Taxonomy to generate the checklist for. Default 'category'.
 *     @type bool   $checked_ontop        Whether to move checked items out of the hierarchy and to
 *                                        the top of the list. Default true.
 *     @type bool   $echo                 Whether to echo the generated markup. False to return the markup instead
 *                                        of echoing it. Default true.
 * }
 */
function auto_hads_terms_checklist( $post_id = 0, $argsin = array() ) {
 	$defaults = array(
		'descendants_and_self' => 0,
		'selected_cats' => false,
		'popular_cats' => false,
		'walker' => null,
		'taxonomy' => 'category',
		'checked_ontop' => true,
		'echo' => true,
	);

	/**
	 * Filters the taxonomy terms checklist arguments.
	 *
	 * @since 3.4.0
	 *
	 * @see wp_terms_checklist()
	 *
	 * @param array $args    An array of arguments.
	 * @param int   $post_id The post ID.
	 */
	$params = apply_filters( 'wp_terms_checklist_args', $argsin, $post_id );

	$r = wp_parse_args( $params, $defaults );

	if ( empty( $r['walker'] ) || ! ( $r['walker'] instanceof Walker ) ) {
		$walker = new Walker_Category_Checklist;
	} else {
		$walker = $r['walker'];
	}

	$taxonomy = $r['taxonomy'];

	$args = array( 'taxonomy' => $taxonomy );

	$tax = get_taxonomy( $taxonomy );
	$args['disabled'] = ! current_user_can( $tax->cap->assign_terms );

	$args['list_only'] = ! empty( $r['list_only'] );

	if ( is_array( $r['selected_cats'] ) ) {
		$args['selected_cats'] = $r['selected_cats'];
	}
	$categories = (array) get_terms( $taxonomy, array( 'get' => 'all' ) );

	$output = '';
	// Then the rest of them
	$output .= call_user_func_array( array( $walker, 'walk' ), array( $categories, 0, $args ) );

	if ( $r['echo'] ) {
		echo $output;
	}

	return $output;
}
function auto_hads_is_image($url) {
    $arr_extension = array('bmp','gif','jpeg', 'jpg','png');
    $sourceUrl = parse_url($url);
    $extension = pathinfo($sourceUrl['path'], PATHINFO_EXTENSION);
    $ext = strtolower($extension);
    return in_array($ext, $arr_extension);
}


function auto_hads_check_url($url,$target) {
    $sourceUrl = parse_url($url);
    $scheme = 'http';
    $host = '';
    $host_target = parse_url($target);
    
    if(isset($host_target['scheme']))
    {
        $scheme = $host_target['scheme'];
    }
    
    if(isset($sourceUrl['host']))
    {
        $host = $sourceUrl['host'];
        if(!isset($sourceUrl['scheme']))
        {
            $fix = trim($sourceUrl['path'],'/');
            $url = $scheme.'://'.$host.'/'.$fix;
        }
        return $url;
    }
    
    if(isset($host_target['host']))
    {
        $host = $host_target['host'];
    }
    $fix = trim($url,'/');
    return $scheme.'://'.$host.'/'.$fix;
}

function auto_hads_request($k) {
    if(isset($_POST[$k]))
    {
        return $_POST[$k];
    }
    return '';
}

/**
 * Insert an attachment from an URL address.
 *
 * @param  String $url 
 * @param  Int    $post_id 
 * @param  Array  $meta_data 
 * @return Int    Attachment ID
 */
function auto_hads_add_attachment_url($url, $post_id = null) {
    if (!class_exists('WP_Http'))
    {
        include_once( ABSPATH . WPINC . '/class-http.php' );
    }
    $attach = auto_hads_get_attachment_item($url);
    if($attach)
    {
        return absint($attach->attach_id);
    }
    try {
        $http = new WP_Http();
        $response = $http->request($url);
        if ($response['response']['code'] != 200) {
            return '';
        }
        $upload = wp_upload_bits(basename($url), null, $response['body']);
        if (!empty($upload['error'])) {
            return '';
        }
    } catch (Exception $exc) {
        return '';
    }
    $file_path = $upload['file'];
    $file_name = basename($file_path);
    $file_type = wp_check_filetype($file_name, null);
    $attachment_title = sanitize_file_name(pathinfo($file_name, PATHINFO_FILENAME));
    $wp_upload_dir = wp_upload_dir();
    $attach_url = $wp_upload_dir['url'] . '/' . $file_name;
    $post_info = array(
        'guid' => $wp_upload_dir['url'] . '/' . $file_name,
        'post_mime_type' => $file_type['type'],
        'post_title' => $attachment_title,
        'post_content' => '',
        'post_status' => 'inherit',
    );

    // Create the attachment
    $attach_id = wp_insert_attachment($post_info, $file_path, $post_id);

    // Include image.php
    require_once( ABSPATH . 'wp-admin/includes/image.php' );

    // Define attachment metadata
    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);

    // Assign metadata to attachment
    wp_update_attachment_metadata($attach_id, $attach_data);
    auto_hads_add_attachment_item($url, $attach_id, $attach_url, $file_path);
    return $attach_id;
}

/**
 * Insert an attachment from an URL address.
 *
 * @param  String $url 
 * @param  Int    $post_id 
 * @param  Array  $meta_data 
 * @return array()
 */
function auto_hads_add_attachment_url_resize($url,$width = 1024) {
    if (!class_exists('WP_Http'))
    {
        include_once( ABSPATH . WPINC . '/class-http.php' );
    }
    $attach = auto_hads_get_attachment_item($url);
    if($attach)
    {
        return array(
            'attach_id' => $attach->attach_id,
            'attach_url' => $attach->attach_url
        );
    }
    try {
        $http = new WP_Http();
        $response = $http->request($url);
        if ($response['response']['code'] != 200) {
            return '';
        }
        $upload = wp_upload_bits(basename($url), null, $response['body']);
        if (!empty($upload['error'])) {
            return '';
        }
    } catch (Exception $exc) {
        return '';
    }
    $file_path = $upload['file'];
    $file_name = basename($file_path);
    $file_type = wp_check_filetype($file_name, null);
    $name = pathinfo($file_name, PATHINFO_FILENAME);
    $attachment_title = sanitize_file_name($name);
    $wp_upload_dir = wp_upload_dir();
    
    
    $img = new auto_hads_image($file_path);
    if($img)
    {
        if($img->get_width() > 1024){
            $img->fit_to_width($width);
            $path = pathinfo($file_path, PATHINFO_DIRNAME);

            $file_name_new = $name.'.jpg';
            $file_path = $path.'/'.$file_name_new;
            if($img->save($path.'/'.$file_name_new)){
                $file_name = $file_name_new;
                $file_type = wp_check_filetype($file_name, null);
            }
        }
    }
    $attach_url = $wp_upload_dir['url'] . '/' . $file_name;
    $post_info = array(
        'guid' => $attach_url,
        'post_mime_type' => $file_type['type'],
        'post_title' => $attachment_title,
        'post_content' => '',
        'post_status' => 'inherit',
    );

    // Create the attachment
    $attach_id = wp_insert_attachment($post_info, $file_path);

    // Include image.php
    require_once( ABSPATH . 'wp-admin/includes/image.php' );

    // Define attachment metadata
    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);

    // Assign metadata to attachment
    wp_update_attachment_metadata($attach_id, $attach_data);

    auto_hads_add_attachment_item($url, $attach_id, $attach_url, $file_path);
    
    return array(
        'attach_id' => $attach_id,
        'attach_url' => $attach_url
    );
}

function auto_hads_add_attachment_item($link_target, $attach_id, $attach_url,$file_path) {
    global $wpdb;
    
    $tabledb_attach = $wpdb->base_prefix . 'auto_hads_attachs';
    
    $wpdb->insert($tabledb_attach, array(
        'attach_id' => $attach_id,
        'link_target' => $link_target,
        'attach_url' => $attach_url,
        'file_path' => $file_path
    ));
}
function auto_hads_get_attachment_item($link_target) {
    global $wpdb;
    $tabledb_attach = $wpdb->base_prefix . 'auto_hads_attachs';
    $sql = $wpdb->prepare("SELECT * FROM `$tabledb_attach` WHERE `link_target` = '%s'", $link_target);
    return $wpdb->get_row($sql);
}

 /**
  * 
  * @param type $selector
  * @param type $url
  * @return {(auto_hads_html_parser|Array)}
  */
function auto_hads_dom_url($selector, $url) {
    $request = wp_remote_get($url);
    $htmlContent = wp_remote_retrieve_body( $request );
    return auto_hads_dom_html_parser($selector,$url,$htmlContent);
}

function auto_hads_get_row($id) {
    global $wpdb;
    $tabledb_post = $wpdb->base_prefix . 'auto_hads_posts';
    $sql = $wpdb->prepare("SELECT * FROM `$tabledb_post` WHERE `link` = '%s'", $id);
    return $wpdb->get_row($sql);
}

function auto_hads_dom_html_parser($selector,$url, $htmlContent) {
    $html = auto_hads_set_normalise($htmlContent);
    if(empty($html))
    {
        return;
    }
    
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXpath($dom);
    $elements = $xpath->evaluate(auto_hads_selector_to_xpath($selector));
    
    $array = array();
    for ($i = 0, $length = $elements->length; $i < $length; ++$i) {
        if ($elements->item($i)->nodeType == XML_ELEMENT_NODE) {
            $node = new auto_hads_html_node($elements->item($i),$xpath,$url);
            $array[] = $node;
        }
    }
    return $array;
}

function auto_hads_set_normalise($html) {
        $text = preg_replace('#<!--.*?-->#si', '', $html);
        $text = preg_replace('#<(script|option|textarea)[^>]*>.*?</\1>#si', '', $text);
        return trim(trim($text), "\xA0");        // TODO: The \xAO is a &nbsp;. Add a test for this.
}
function auto_hads_parser_atts( $pairs, $atts) {
	$out = array();
	foreach ($pairs as $name => $default) {
		if ( array_key_exists($name, $atts) )
			$out[$name] = $atts[$name];
		else
			$out[$name] = $default;
	}
	return $out;
}



/**
 * Convert $selector into an XPath string.
 */
function auto_hads_selector_to_xpath($selector) {
    // remove spaces around operators
    $selector = preg_replace('/\s*>\s*/', '>', $selector);
    $selector = preg_replace('/\s*~\s*/', '~', $selector);
    $selector = preg_replace('/\s*\+\s*/', '+', $selector);
    $selector = preg_replace('/\s*,\s*/', ',', $selector);
    $selectors = preg_split('/\s+(?![^\[]+\])/', $selector);

    foreach ($selectors as &$selector) {
        // ,
        $selector = preg_replace('/,/', '|descendant-or-self::', $selector);
        // input:checked, :disabled, etc.
        $selector = preg_replace('/(.+)?:(checked|disabled|required|autofocus)/', '\1[@\2="\2"]', $selector);
        // input:autocomplete, :autocomplete
        $selector = preg_replace('/(.+)?:(autocomplete)/', '\1[@\2="on"]', $selector);
        // input:button, input:submit, etc.
        $selector = preg_replace('/:(text|password|checkbox|radio|button|submit|reset|file|hidden|image|datetime|datetime-local|date|month|time|week|number|range|email|url|search|tel|color)/', 'input[@type="\1"]', $selector);
        // foo[id]
        $selector = preg_replace('/(\w+)\[([_\w-]+[_\w\d-]*)\]/', '\1[@\2]', $selector);
        // [id]
        $selector = preg_replace('/\[([_\w-]+[_\w\d-]*)\]/', '*[@\1]', $selector);
        // foo[id=foo]
        $selector = preg_replace('/\[([_\w-]+[_\w\d-]*)=[\'"]?(.*?)[\'"]?\]/', '[@\1="\2"]', $selector);
        // [id=foo]
        $selector = preg_replace('/^\[/', '*[', $selector);
        // div#foo
        $selector = preg_replace('/([_\w-]+[_\w\d-]*)\#([_\w-]+[_\w\d-]*)/', '\1[@id="\2"]', $selector);
        // #foo
        $selector = preg_replace('/\#([_\w-]+[_\w\d-]*)/', '*[@id="\1"]', $selector);
        // div.foo
        $selector = preg_replace('/([_\w-]+[_\w\d-]*)\.([_\w-]+[_\w\d-]*)/', '\1[contains(concat(" ",@class," ")," \2 ")]', $selector);
        // .foo
        $selector = preg_replace('/\.([_\w-]+[_\w\d-]*)/', '*[contains(concat(" ",@class," ")," \1 ")]', $selector);
        // div:first-child
        $selector = preg_replace('/([_\w-]+[_\w\d-]*):first-child/', '*/\1[position()=1]', $selector);
        // div:last-child
        $selector = preg_replace('/([_\w-]+[_\w\d-]*):last-child/', '*/\1[position()=last()]', $selector);
        // :first-child
        $selector = str_replace(':first-child', '*/*[position()=1]', $selector);
        // :last-child
        $selector = str_replace(':last-child', '*/*[position()=last()]', $selector);
        // :nth-last-child
        $selector = preg_replace('/:nth-last-child\((\d+)\)/', '[position()=(last() - (\1 - 1))]', $selector);
        // div:nth-child
        $selector = preg_replace('/([_\w-]+[_\w\d-]*):nth-child\((\d+)\)/', '*/*[position()=\2 and self::\1]', $selector);
        // :nth-child
        $selector = preg_replace('/:nth-child\((\d+)\)/', '*/*[position()=\1]', $selector);
        // :contains(Foo)
        $selector = preg_replace('/([_\w-]+[_\w\d-]*):contains\((.*?)\)/', '\1[contains(string(.),"\2")]', $selector);
        // >
        $selector = preg_replace('/>/', '/', $selector);
        // ~
        $selector = preg_replace('/~/', '/following-sibling::', $selector);
        // +
        $selector = preg_replace('/\+([_\w-]+[_\w\d-]*)/', '/following-sibling::\1[position()=1]', $selector);
        $selector = str_replace(']*', ']', $selector);
        $selector = str_replace(']/*', ']', $selector);
    }

    // ' '
    $selector = implode('/descendant::', $selectors);
    $selector = 'descendant-or-self::' . $selector;
    // :scope
    $selector = preg_replace('/(((\|)?descendant-or-self::):scope)/', '.\3', $selector);
    // $element
    $sub_selectors = explode(',', $selector);

    foreach ($sub_selectors as $key => $sub_selector) {
        $parts = explode('$', $sub_selector);
        $sub_selector = array_shift($parts);

        if (count($parts) && preg_match_all('/((?:[^\/]*\/?\/?)|$)/', $parts[0], $matches)) {
            $results = $matches[0];
            $results[] = str_repeat('/..', count($results) - 2);
            $sub_selector .= implode('', $results);
        }

        $sub_selectors[$key] = $sub_selector;
    }

    $selector = implode(',', $sub_selectors);

    return $selector;
}

/**
 * Curl send get request, support HTTPS protocol
 * @param string $url The request url
 * @param string $refer The request refer
 * @param int $timeout The timeout seconds
 * @return mixed
 */
function auto_hads_send_get_request($url, $refer = "", $timeout = 10) {
    $ssl = stripos($url, 'https://') === 0 ? true : false;
    $curlObj = curl_init();
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_AUTOREFERER => 1,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)',
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
        CURLOPT_HTTPHEADER => ['Expect:'],
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    ];
    if ($refer) {
        $options[CURLOPT_REFERER] = $refer;
    }
    if ($ssl) {
        //support https
        $options[CURLOPT_SSL_VERIFYHOST] = false;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
    }
    curl_setopt_array($curlObj, $options);
    $returnData = curl_exec($curlObj);
    if (curl_errno($curlObj)) {
        //error message
        $returnData = curl_error($curlObj);
    }
    curl_close($curlObj);
    return $returnData;
}

/**
 * Curl send post request, support HTTPS protocol
 * @param string $url The request url
 * @param array $data The post data
 * @param string $refer The request refer
 * @param int $timeout The timeout seconds
 * @param array $header The other request header
 * @return mixed
 */
function auto_hads_send_post_request($url, $data, $refer = "", $timeout = 10, $header = []) {
    $curlObj = curl_init();
    $ssl = stripos($url, 'https://') === 0 ? true : false;
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_AUTOREFERER => 1,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)',
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
        CURLOPT_HTTPHEADER => ['Expect:'],
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_REFERER => $refer
    ];
    if (!empty($header)) {
        $options[CURLOPT_HTTPHEADER] = $header;
    }
    if ($refer) {
        $options[CURLOPT_REFERER] = $refer;
    }
    if ($ssl) {
        //support https
        $options[CURLOPT_SSL_VERIFYHOST] = false;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
    }
    curl_setopt_array($curlObj, $options);
    $returnData = curl_exec($curlObj);
    if (curl_errno($curlObj)) {
        //error message
        $returnData = curl_error($curlObj);
    }
    curl_close($curlObj);
    return $returnData;
}
