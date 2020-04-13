$(function(){
    var special_id = getQueryString('special_id');
		if (special_id=='') {
    	window.location.href = WapSiteUrl + '/index.html';
	}else {
		loadSpecial(special_id);
	}
})

function loadSpecial(special_id){
    $.ajax({
        url: ApiUrl + "/index.php/index/special?special_id=" + special_id,
        type: 'get',
        dataType: 'json',
        success: function(result) {
            $('title,h1').html(result.datas.special_desc);
            var data = result.datas.list;
            var html = '';

            $.each(data, function(k, v) {
                $.each(v, function(kk, vv) {
                    switch (kk) {
                        case 'show_list':
                        case 'home3':
                            $.each(vv.item, function(k3, v3) {
                                vv.item[k3].url = buildUrl(v3.type, v3.data);
                            });
                            break;

                        case 'home1':
                            vv.url = buildUrl(vv.type, vv.data);
                            break;

                        case 'home2':
                        case 'home4':
                            vv.square_url = buildUrl(vv.square_type, vv.square_data);
                            vv.rectangle1_url = buildUrl(vv.rectangle1_type, vv.rectangle1_data);
                            vv.rectangle2_url = buildUrl(vv.rectangle2_type, vv.rectangle2_data);
                            break;
                        case 'home5':
                            vv.square_url = buildUrl(vv.square_type, vv.square_data);
                            vv.rectangle1_url = buildUrl(vv.rectangle1_type, vv.rectangle1_data);
                            vv.rectangle2_url = buildUrl(vv.rectangle2_type, vv.rectangle2_data);
                            vv.rectangle3_url = buildUrl(vv.rectangle3_type, vv.rectangle3_data);
                            break;
		    	case 'home6':
                            vv.square_url = buildUrl(vv.square_type, vv.square_data);
                            vv.rectangle1_url = buildUrl(vv.rectangle1_type, vv.rectangle1_data);
                            vv.rectangle2_url = buildUrl(vv.rectangle2_type, vv.rectangle2_data);
                            break;
				case 'home7':
                            vv.square_url = buildUrl(vv.square_type, vv.square_data);
                            vv.rectangle1_url = buildUrl(vv.rectangle1_type, vv.rectangle1_data);
                            vv.rectangle2_url = buildUrl(vv.rectangle2_type, vv.rectangle2_data);
                            vv.rectangle3_url = buildUrl(vv.rectangle3_type, vv.rectangle3_data);
                            vv.rectangle4_url = buildUrl(vv.rectangle4_type, vv.rectangle4_data);
                            vv.rectangle5_url = buildUrl(vv.rectangle5_type, vv.rectangle5_data);
                            vv.rectangle6_url = buildUrl(vv.rectangle6_type, vv.rectangle6_data);
                            vv.rectangle7_url = buildUrl(vv.rectangle7_type, vv.rectangle7_data);
                            vv.rectangle8_url = buildUrl(vv.rectangle8_type, vv.rectangle8_data);
                            vv.rectangle9_url = buildUrl(vv.rectangle9_type, vv.rectangle9_data);
                            break;
		    	case 'home8':
                            vv.square_url = buildUrl(vv.square_type, vv.square_data);
                            vv.rectangle1_url = buildUrl(vv.rectangle1_type, vv.rectangle1_data);
                            vv.rectangle2_url = buildUrl(vv.rectangle2_type, vv.rectangle2_data);
                            vv.rectangle3_url = buildUrl(vv.rectangle3_type, vv.rectangle3_data);
                            break;
                    }

                    if (k == 0) {
                        $("#main-container1").html(template.render(kk, vv));
                    } else {
                      if(kk=='home7'){
                        $("#ico_set").html(template.render(kk, vv));
                      }else{
                        html += template.render(kk, vv);
                      }
                    }
                    return false;
                });
            });

            $("#main-container2").html(html);

            $('.show_list').each(function() {
                if ($(this).find('.item').length < 2) {
                    return;
                }

                Swipe(this, {
                    startSlide: 2,
                    speed: 400,
                    auto: 3000,
                    continuous: true,
                    disableScroll: false,
                    stopPropagation: false,
                    callback: function(index, elem) {},
                    transitionEnd: function(index, elem) {}
                });
            });
			
	    $('.xianshi-list').each(function() {
                if ($(this).find('.item').length < 2) {
                    return;
                }

                Swipe(this, {
                    startSlide: 2,
                    speed: 400,
                    auto: 3000,
                    continuous: true,
                    disableScroll: false,
                    stopPropagation: false,
                    callback: function(index, elem) {},
                    transitionEnd: function(index, elem) {}
                });
            });

        }
    });

}

function takeCount() {
	setTimeout("takeCount()", 1000);
	$(".time-remain").each(function(){
		var obj = $(this);
		var tms = obj.attr("count_down");
		if (tms>0) {
			tms = parseInt(tms)-1;
			var days = Math.floor(tms / (1 * 60 * 60 * 24));
			var hours = Math.floor(tms / (1 * 60 * 60)) % 24;
			var minutes = Math.floor(tms / (1 * 60)) % 60;
			var seconds = Math.floor(tms / 1) % 60;

			if (days < 0) days = 0;
			if (hours < 0) hours = 0;
			if (minutes < 0) minutes = 0;
			if (seconds < 0) seconds = 0;
			if(days>9){
			  obj.find("[time_id='d']").html('9+');
			}else{
			 obj.find("[time_id='d']").html(days);
			}
			obj.find("[time_id='h']").html(hours);
			obj.find("[time_id='m']").html(minutes);
			obj.find("[time_id='s']").html(seconds);
			obj.attr("count_down",tms);
		}
	});
}
$(function() {
	setTimeout("takeCount()", 1000);
	$('.xianshi-list').each(function() {
	if ($(this).find('.item').length < 2) {
				 $(".xianshi").jfocus({
				time: 8E3
			});
		}
	});
});
