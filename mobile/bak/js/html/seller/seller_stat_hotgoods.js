$(function() {
	if (getQueryString('seller_key') != '') {
		var key = getQueryString('seller_key');
		var seller_name = getQueryString('seller_name');
		addCookie('seller_key', key);
		addCookie('seller_name', seller_name);
	} else {
		var key = getCookie('seller_key');
		var seller_name = getCookie('seller_name');
	}
	//滚动header固定到顶部
	//$.scrollTransparent();	

	//请求数据

	if (key && seller_name) {
		$.ajax({
			type: 'post',
			url: ApiUrl + "/index.php/seller_stat_industry/hot",
			data: {
				key: key
			},
			dataType: 'json',
			success: function(result) {
				var data = result.datas;
                if(data.error){
                	alert('请重新登录！');
		            window.location.href=WapSiteUrl+'/html/seller/login.html';
                }
				var html = template.render('goodslist_tpl', data);
				$("#goodslist_info").html(html);
				var stat_arr=data.stat_arr;
				if(stat_arr.search_arr.search_type=='month'){
					$("#search_type").val("month");
				}else{
					$("#search_type").val("week");
				}
				var year_arr_html='';
				if(stat_arr.year_arr.length>0){
                  	for(var i=0;i<stat_arr.year_arr.length;i++){ 
						var is_chekc='';
						if(stat_arr.search_arr.week.current_year== stat_arr.year_arr[i])
						{
							is_chekc='selected';
						}
                  		year_arr_html +='<option value=\"'+stat_arr.year_arr[i]+'\" '+is_chekc+'>'+stat_arr.year_arr[i]+'</option>';
					}
				}
				$("#searchweek_year").html(year_arr_html);
				$("#searchmonth_year").html(year_arr_html);
				
				var month_arr_html='';
				if(stat_arr.month_arr.length>0){
                  	for(var i=0;i<stat_arr.month_arr.length;i++){ 
						var is_chekc='';
						if(stat_arr.search_arr.week.current_month== stat_arr.month_arr[i])
						{
							is_chekc='selected';
						}
                  		month_arr_html +='<option value=\"'+stat_arr.month_arr[i]+'\" '+is_chekc+'>'+stat_arr.month_arr[i]+'</option>';
					}
				}
				$("#searchweek_month").html(month_arr_html);
				$("#searchmonth_month").html(month_arr_html);
				
				var week_arr_html='';
				if(stat_arr.week_arr.length>0){
                  	for(var i=0;i<stat_arr.week_arr.length;i++){ 
						var is_chekc='';
						if(stat_arr.search_arr.week.current_week== stat_arr.week_arr[i].key)
						{
							is_chekc='selected';
						}
                  		week_arr_html +='<option value=\"'+stat_arr.week_arr[i].key+'\" '+is_chekc+'>'+stat_arr.week_arr[i].val+'</option>';
					}
				}
				$("#searchweek_week").html(week_arr_html);
				
			/* 	var choose_gcid_arr_html='';
				if(stat_arr.gc_json.length>0){
                  	for(var i=0;i<stat_arr.gc_json.length;i++){ 
						var is_chekc='';
						if(stat_arr.gc_choose_json== stat_arr.gc_json[i])
						{
							is_chekc='selected';
						}
                  		choose_gcid_arr_html +='<option value=\"'+stat_arr.gc_json[i]+'\" '+is_chekc+'>'+stat_arr.gc_json[i]+'</option>';
					}
				}
				$("#choose_gcid").html(choose_gcid_arr_html); */
				//商品分类
				init_gcselect(stat_arr.gc_choose_json,stat_arr.gc_json);
                var bjson = data.stat_json;
                var xAxis_categories = bjson.xAxis.categories;
                var legend_enabled = bjson.legend.enabled;
                var series_name = bjson.series[0].name;
                var series_data = bjson.series[0].data;
                var title_text = bjson.title.text;
                var title_x = bjson.title.x;
                var chart_type = bjson.chart.type;
                var colors = bjson.colors;
                var credits_enabled = bjson.credits.enabled;
                var exporting_enabled = bjson.exporting.enabled;
                var yAxis_title_text = bjson.yAxis.title.text;

               $(document).ready(function() {
				/**
				 * Highcharts 在 4.2.0 开始已经不依赖 jQuery 了，直接用其构造函数既可创建图表
				 **/
				var chart = new Highcharts.Chart('container', {
					title: {
						text: title_text,
						x: title_x
					},
					xAxis: {
						categories: xAxis_categories
					},
					yAxis: {
						title: {
							text: yAxis_title_text
						},
						plotLines: [{
							value: 0,
							width: 1,
							color: '#808080'
						}]
					},
					tooltip: {
						valueSuffix: ''
					},
					legend: {
						enabled: legend_enabled,
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'middle',
						borderWidth: 0
					},
					series: [{
						name: series_name,
						data: series_data,
						type:chart_type
					}]
				});});
			}
		});
	}else{
        alert('请重新登录！');
		window.location.href=WapSiteUrl+'/html/seller/login.html';
	}
	$('.search-btn').on('click',function(){
		if (key && seller_name) {
			var search_type=$('#search_type').val();
			var searchweek_year=$('#searchweek_year').val();
			var searchweek_month=$('#searchweek_month').val();
			var searchweek_week=$('#searchweek_week').val();
			var searchmonth_year=$('#searchmonth_year').val();
			var searchmonth_month=$('#searchmonth_month').val();
			var choose_gcid=$('#choose_gcid').val();
		$.ajax({
			type: 'get',
			url: ApiUrl + "/index.php/seller_stat_industry/hot",
			data: {
				key: key,
				search_type:search_type,
				searchweek_year:searchweek_year,
				searchweek_month:searchweek_month,
				searchweek_week:searchweek_week,
				searchmonth_year:searchmonth_year,
				searchmonth_month:searchmonth_month,
				choose_gcid:choose_gcid
			},
			dataType: 'json',
			success: function(result) {
				var data = result.datas;
                if(data.error){
                	alert('请重新登录！');
		            window.location.href=WapSiteUrl+'/html/seller/login.html';
                }
				var html = template.render('goodslist_tpl', data);
				$("#goodslist_info").html(html);
				
				var stat_arr=data.stat_arr;
				//$("#search_type").val(stat_arr.search_arr.search_type);
				if(stat_arr.search_arr.search_type=='month'){
					$("#search_type").val("month");
				}else{
					$("#search_type").val("week");
				}
				var year_arr_html='';
				if(stat_arr.year_arr.length>0){
                  	for(var i=0;i<stat_arr.year_arr.length;i++){ 
						var is_chekc='';
						if(stat_arr.search_arr.week.current_year== stat_arr.year_arr[i])
						{
							is_chekc='selected';
						}
                  		year_arr_html +='<option value=\"'+stat_arr.year_arr[i]+'\" '+is_chekc+'>'+stat_arr.year_arr[i]+'</option>';
					}
				}
				$("#searchweek_year").html(year_arr_html);
				$("#searchmonth_year").html(year_arr_html);
				
				var month_arr_html='';
				if(stat_arr.month_arr.length>0){
                  	for(var i=0;i<stat_arr.month_arr.length;i++){ 
						var is_chekc='';
						if(stat_arr.search_arr.week.current_month== stat_arr.month_arr[i])
						{
							is_chekc='selected';
						}
                  		month_arr_html +='<option value=\"'+stat_arr.month_arr[i]+'\" '+is_chekc+'>'+stat_arr.month_arr[i]+'</option>';
					}
				}
				$("#searchweek_month").html(month_arr_html);
				$("#searchmonth_month").html(month_arr_html);
				
				var week_arr_html='';
				if(stat_arr.week_arr.length>0){
                  	for(var i=0;i<stat_arr.week_arr.length;i++){ 
						var is_chekc='';
						if(stat_arr.search_arr.week.current_week== stat_arr.week_arr[i].key)
						{
							is_chekc='selected';
						}
                  		week_arr_html +='<option value=\"'+stat_arr.week_arr[i].key+'\" '+is_chekc+'>'+stat_arr.week_arr[i].val+'</option>';
					}
				}
				$("#searchweek_week").html(week_arr_html);
				
				
				
                var bjson = data.stat_json;
                var xAxis_categories = bjson.xAxis.categories;
                var legend_enabled = bjson.legend.enabled;
                var series_name = bjson.series[0].name;
                var series_data = bjson.series[0].data;
                var title_text = bjson.title.text;
                var title_x = bjson.title.x;
                var chart_type = bjson.chart.type;
                var colors = bjson.colors;
                var credits_enabled = bjson.credits.enabled;
                var exporting_enabled = bjson.exporting.enabled;
                var yAxis_title_text = bjson.yAxis.title.text;

               $(document).ready(function() {
				/**
				 * Highcharts 在 4.2.0 开始已经不依赖 jQuery 了，直接用其构造函数既可创建图表
				 **/
				var chart = new Highcharts.Chart('container', {
					title: {
						text: title_text,
						x: title_x
					},
					xAxis: {
						categories: xAxis_categories
					},
					yAxis: {
						title: {
							text: yAxis_title_text
						},
						plotLines: [{
							value: 0,
							width: 1,
							color: '#808080'
						}]
					},
					tooltip: {
						valueSuffix: ''
					},
					legend: {
						enabled: legend_enabled,
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'middle',
						borderWidth: 0
					},
					series: [{
						name: series_name,
						data: series_data,
						type:chart_type
					}]
				});});
			}
		});
	}else{
        alert('请重新登录！');
		window.location.href=WapSiteUrl+'/html/seller/login.html';
	}
	});
})