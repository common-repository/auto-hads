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
        $('body').on('click', '.auto-hads-close,.auto-hads-panel', function (e) {
            $(this).closest('.auto-hads-panel').fadeOut();
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
            var data_params = {
                    action: 'auto_hads_ajax_get_content_test',
                    url: url
            };
            data_params = get_data_params_hads('.auto-hads-bot-detail',data_params);
            
            $('.loading-url').fadeIn();
            $.ajax({
                url:ajaxurl,
                type:"POST",
                data:data_params,
                dataType:"json",
                success: function(response){
                   if (response && response.success)
                    {
                       $('.auto-hads-test-data-content').html(response.data.post);
                       $('.auto-hads-test-data').fadeIn();
                    }
                    $('.loading-url').fadeOut();
                }
            });
        });
        $('body').on('click', 'input.auto-hads-get-posts-from-url', function (e) {
            e.preventDefault();
            list_posts = null;
            $('.loading-url').fadeIn();
            $('.postbox-container-auto-hads-insert').fadeOut();
            $('#auto-hads-content-body-url').html('');
            $('.message-url').html('Begin get url');
            
            var data_params = {
                    action: 'auto_hads_ajax_get_content_data'
            };
            
            data_params = get_data_params_hads('.auto-hads-bot',data_params);
            
            
            $.ajax({
                url:ajaxurl,
                type:"POST",
                data:data_params,
                dataType:"json",
                success: function(response){
//                    $('.auto-hads-content-url').html(response.data);
                   if (response && response.success)
                    {
                        $('.message-url').html('Get url Successfull');
                        
                        $('#auto-hads-content-body-url').html(response.data.html);
                        list_posts = response.data.list;
                        listcout = response.data.count;
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
		set_posts_projessbar(-1 , listcout);
                list_posts = null;
                $('#auto-hads-content-body-url').html('');
                $('.postbox-container-auto-hads-insert').fadeOut();
                $('.message-url').html('');
                $('.auto-hads-message-data-content').html('You have successfully added ' + listcout + ' posts to the database');
                $('.auto-hads-message-data').fadeIn();
                $("html, body").animate({ 'scrollTop' : 0 }, 2000);
                $('.loading-url').fadeOut();
                return;
            }
            
            $('.loading-url').fadeIn();
            var postdata = list_posts[posision];
            var dataimport = {
                    action: 'auto_hads_ajax_set_import_post_data',
                    cats: categories
            };
            if(brands.length)
            {
                dataimport['brands'] = brands;
            }
            var dataKeys = Object.keys(postdata);
            $.each(dataKeys,function (){
                dataimport[this] = postdata[this];
            });
            
            dataimport = get_data_params_hads('.auto-hads-bot-detail',dataimport);    
                
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
                    else{
                        $('.loading-url').fadeOut();
                        $('.auto-hads-message-data-content').html('<b style="color:red;">Has some error. Please select the category before performing this action</b>');
                        $('.auto-hads-message-data').fadeIn();
                    }
                });
            }
        }
    });
    
    function get_data_params_hads(c,data_params){
        $(c).each(function (){
            var control = $(this).attr('type');
            switch(control){
                case 'checkbox':
                    if($(this).is(':checked'))
                    {
                        data_params[$(this).attr('name')] = 1;
                    }
                    else{
                        data_params[$(this).attr('name')] = 0;
                    }
                    break;
                case 'textarea':
                    
                    data_params[$(this).attr('name')] = $(this).val().split('\n');
                    //console.log(data_params);
                    break;
                default :
                    data_params[$(this).attr('name')] = $(this).val();
                    break;
            }
        });
        return data_params;
    }
    
    function set_posts_projessbar(posision , listcout)
    {
        var opt = posision + 1;
        var opercent = ((opt / listcout) * 100).toFixed(2);
        $('#auto_hads_projess_bar_in').width(opercent + '%');
        $('#auto_hads_results_post_format').html('<div><label>Current is </label><label >' + opercent + ' %</label><label> of ' + opt + '/' + listcout + ' items </label></div>');
    }
    

}(jQuery));