(function ($) {
    var running = false;
    var listcout = 0;
    var list_posts = [];
    var categories = [];
    var brands = [];
    $(document).ready(function ()
    {
        $('body').on('click', 'input.auto-hads-set-posts-fortmat', function (e) {
            e.preventDefault();
            running = true;
            categories = [];
            $('.hads-categories-box input:checked').each(function () {
                categories.push(this.value);
            });
            brands = [];
            $('.hads-brands-box input:checked').each(function () {
                brands.push(this.value);
            });
            auto_hads_set_posts_data($(this), 0);
        });
        $('body').on('click', '.auto-hads-test-data', function (e) {
            $(this).fadeOut();
        });
        
        $('body').on('change', '.edits-box', function (e) {
            var $index = parseInt($(this).data('id'));
            var $key = $(this).data('key');
            if (typeof list_posts[$index] !== 'undefined')
            {
                var postdata = list_posts[$index];
                switch ($key) {
                    case 'title':
                        postdata.title = $(this).val();
                        break;
                    case 'price':
                        postdata.price = $(this).val();
                        break;
                    case 'skip':
                        if($(this).is(':checked'))
                        {
                            postdata.skip = 1;
                        }
                        else{
                            postdata.skip = 0;
                        }
                        break;
                        
                    default:
                        
                        break;
                }
                list_posts[$index] = postdata;
            }
        });
        
        $('body').on('click', 'button.get-my-info', function (e) {
            e.preventDefault();
            var url = $(this).data('id');
            var description = $('.tag-description').val();
            var descriptionreplace = $('.tag-description-replace').val();
            $('.auto-hads-test-data').html('')
            $.ajax({
                url:ajaxurl,
                type:"POST",
                data:{
                    action: 'auto_hads_ajax_get_content_test',
                    url: url,
                    fnc: 'woocommerce',
                    tagdesc: description,
                    descreplace: descriptionreplace
                },
                dataType:"json",
                success: function(response){
                   if (response && response.success)
                    {
                       $('.auto-hads-test-data').html(response.data).fadeIn();
                    }
                }
            });
        });
        $('body').on('click', 'input.auto-hads-get-posts-from-url', function (e) {
            e.preventDefault();
            list_posts = null;
            var url = $('.url-title').val();
            $('.loading-url').fadeIn();
            $('.postbox-container-auto-hads-insert').fadeOut();
            $('#auto-hads-content-body-url').html('');
            $('.message-url').html('Begin get url');
            var warper = $('.tag-warper').val();
            var link = $('.tag-link').val();
            var image = $('.tag-image').val();
            var title = $('.tag-title').val();
            var price = $('.tag-price').val();
            var pricereplace = $('.tag-price-replace').val();
            var salechk = $('.tag-sale-chk:checked').length;
            
            var sale = $('.tag-sale').val();
            var description = $('.tag-description').val();
            
            $.ajax({
                url:ajaxurl,
                type:"POST",
                data:{
                    action: 'auto_hads_ajax_get_content_data',
                    url: url,
                    fnc: 'woocommerce',
                    warper: warper,
                    link: link,
                    image: image,
                    title: title,
                    price: price,
                    salechk: salechk,
                    pricereplace: pricereplace,
                    sale: sale,
                    description: description
                },
                dataType:"json",
                success: function(response){
                   if (response && response.success)
                    {
                        $('.message-url').html('Get url Successfull');
                        
                        $('#auto-hads-content-body-url').html(response.data.html);
                        list_posts = response.data.list;
                        listcout = list_posts.length;
						
			set_posts_projessbar(-1 , listcout);
                        $('.postbox-container-auto-hads-insert').fadeIn();
                    }
                    else{
                        $('.message-url').html('Has some error get url');
                    }
                    $('.loading-url').fadeOut();
                }
            });
        });
        
        function auto_hads_set_posts_data(bt, posision) {
            if (typeof list_posts[posision] === 'undefined')
            {
                running = false;
                $(bt).val('Run Data');
				set_posts_projessbar(-1 , listcout);
                list_posts = null;
                $('#auto-hads-content-body-url').html('');
                $('.postbox-container-auto-hads-insert').fadeOut();
                $("html, body").animate({ 'scrollTop' : 0 }, 2000);
                return;
            }
            var postdata = list_posts[posision];
            var description = $('.tag-description').val();
            var descriptionreplace = $('.tag-description-replace').val();
            var dataimport = {
                    action: 'auto_hads_ajax_set_import_post_data',
                    cats: categories,
                    fnc: 'woocommerce',
                    brands: brands,
                    title: postdata.title,
                    price: postdata.price,
                    src: postdata.src,
                    url: postdata.url,
                    pricesale: postdata.pricesale,
                    tagdesc: description,
                    descreplace: descriptionreplace
                };
            if(postdata.skip === 1)
            {
                set_posts_projessbar(posision , listcout);
                auto_hads_set_posts_data(bt, posision + 1);
            }
            else{
                $.post(ajaxurl,dataimport , function (response) {
                    if (response && response.success && running)
                    {
                        set_posts_projessbar(posision , listcout);
                        auto_hads_set_posts_data(bt, posision + 1);
                    }
                });
            }
        }
    });
    
    function set_posts_projessbar(posision , listcout)
    {
        var opt = posision + 1;
        var opercent = ((opt / listcout) * 100).toFixed(2);
        $('#auto_hads_projess_bar_in').width(opercent + '%');
        $('#auto_hads_results_post_format').html('<div><label>Current is </label><label >' + opercent + ' %</label><label> of ' + opt + '/' + listcout + ' items </label></div>');
    }
    

}(jQuery));