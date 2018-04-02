'use strict';
var app = (function($) {
	var 
	//var of the jobs that for article
	//tigger the function change to regenerate all when jobs change
	//
	//!!!!remember jobs means work category
	jobs = {
		jbs: Array(),
		getJobs: function(){
			return this.jbs;
		},
		splice: function(index,length){
			this.jbs.splice(index,length);
			this.change();
		},
		push:function(id, name){
			this.jbs.push({'id': id,'name': name});
			this.change();
		},
		change: function(){
			var selectedBox = $('.dropdown-menu .selected-box.job')
		 	var html ='';
		 	var names = '';
		 	var ids = Array();
		 	var jbs = this.jbs;
		 	$('.item-box a.job').removeClass('selected');
		 	for(var i in jbs){
		 		html += '<span class="btn job" data-id="'+jbs[i].id
		 		+'" data-name="'+jbs[i].name+'">'+jbs[i].name+
		 		'<a href="javascript:void(0);"><i class="fa fa-times"></i></a></span>';
		 		$('.item-box a.job[data-id="'+jbs[i].id+'"]').addClass('selected');

		 		names += '<span class="label label-info">'+jbs[i].name + "</span>";
		 		ids.push(jbs[i].id);
		 	}
		 	selectedBox.html(html);

		 	$("input[name='work_categories']").val( JSON.stringify(ids) );
		 	$('#span_jobs').html(names);

		 	//click item for delete it,and then regenerate the item
		 	$('.dropdown-menu .selected-box.job span.job').click(function(){
		 		// console.log(jobs);
		 		$(this).remove();
		 		var id = $(this).data('id');
	 			var jbs = jobs.getJobs();
				for(var i in jbs){
					if(jbs[i].id == id){
						jobs.splice(i,1);
						break;
					}
				}
		 		return false;
		 	});
			if_show_msg_box()
		}
	},
	//var of the categories that for article
	//tigger the function change to regenerate all when cates change
	//!!!!attention: now the categories means subcategory
	//!
	//!now the categories means trade sub type
	categories = {
		cates :  Array(),
		getCates : function(){
			return this.cates;
		},
		push : function(id,name){
			//if selected id is 0 ,that is 通用资料
			//if so we will delete all other item
			if(id == 0)
				this.cates.length =0;
			//what ever other item click ,delete id=0
			else
				for(var i in this.cates){
					if( this.cates[i].id == 0 ){
						this.splice(i,1);
						break;
					}	
				}
			this.cates.push({'id':id,'name':name})
			this.change();
		},
		splice : function(index,length){
			this.cates.splice(index,length);
			this.change();
		},
		change: function(){
			var selectedBox = $('.dropdown-menu .selected-box.category')
		 	var html ='';
		 	var names = '';
		 	var ids = Array();
		 	var cates = this.cates;
		 	$('.item-box a.category').removeClass('selected');
		 	for(var i in cates){
		 		html += '<span class="btn category" data-id="'+cates[i].id
		 		+'" data-name="'+cates[i].name+'">'+cates[i].name+
		 		'<a href="javascript:void(0);"><i class="fa fa-times"></i></a></span>';
		 		$('.item-box a.category[data-id="'+cates[i].id+'"]').addClass('selected');
		 		// console.log("cates of "+cates[i].id+"==="+cates[i].name);
		 		names += '<span class="label label-info">'+cates[i].name + "</span>";
		 		ids.push(cates[i].id);
		 	}
		 	selectedBox.html(html);

		 	$("input[name='trade_sub_types']").val( JSON.stringify(ids) );
		 	$('#span_categories').html(names);

		 	//click item for delete it,and then regenerate the item
		 	$('.dropdown-menu .selected-box.category span.category').click(function(){
		 		$(this).remove();
		 		var id = $(this).data('id');
	 			var cates = categories.getCates();
				for(var i in cates){
					if(cates[i].id == id){
						categories.splice(i,1);
						break;
					}
				}
		 		return false;
		 	});
		 	//where no item enable all
		 	if(this.cates.length == 0){
		 		$('a.category').removeClass('disabled')
		 	}
		 	//where exists id =0 [通用资料]
		 	//disable other item
		 	else if(this.cates[0].id == 0){
		 		$('a.category').addClass('disabled');
		 		$('a.category[data-id="0"]').removeClass('disabled')
		 	}
		 	//otherwise
		 	//disable [通用资料]
		 	else{
		 		$('a.category').removeClass('disabled')
		 		$('a.category[data-id="0"]').addClass('disabled');
		 	}
		 	
			if_show_msg_box();


		 }
	},
	//var of the comptags for article
	//tigger the function change to regenerate all when comptags change
	//
	//now the comptags means company_tag
	comptags = {
		tags: Array(),
		//a backup of the origin html
		html:'',
		title:'',
		getComptags: function(){
			return this.tags;
		},
		push: function(name){
			//trim
			name = name.replace(/(^\s*)|(\s*$)/g, "");
			for(var i in this.tags){
				if( this.tags[i] == name ){
					return;
				}	
			}
			this.tags.push(name);
			this.change();
		},
		splice: function(index, length){
			var index = typeof(index) === 'undefined' ? this.tags.length-1: index
			var length = typeof(length) ==='undefined' ? 1: length
			this.tags.splice(index,length);
			this.change();
		},
		delete: function(name){
			for(var i in this.tags){
				if( this.tags[i] == name ){
					this.splice(i);
					break;
				}	
			}
			this.change();
		},
		clear_all: function(){
			this.tags = new Array();
			this.change()
		},
		//tigger when tags change
		change: function(){
			var spanTpl = '<span class="label label-info" data-name="@name">@name <i class="fa fa-times"></i></span>'
			var spanHtml = '';
			for(var i in this.tags)
				spanHtml += spanTpl.replace(/@name/g,this.tags[i]);
			$('.comptags input[name="company_tags"]').val( JSON.stringify(this.tags) );
			$('.comptags .taglist').html(spanHtml);
			//bind click event
			$('.comptags .taglist span.label').click(function(){
				var name = $(this).data('name');
				comptags.delete(name);
			})

		 	//highlight the text that appear in the tags
		 	//this is 4 when the length lg 1
		 	if(this.tags.length >0 ){

		 		if(this.html === '')
		 			this.html = $("#article_info").html();
		 		var html = this.html;
		 		var tpl ="<strong class='highlight'>@tag</strong>";
		 		for(var i in this.tags){
		 			var str = tpl.replace('@tag',this.tags[i]);
		 			html =html.replace(new RegExp(this.tags[i],"gmi"),str);// this.tags.
		 			// console.log(html);
		 		}
		 		$("#article_info").html(html);

		 		if(this.title === '')
		 			this.title = $('#article_title').html();
		 		var title = this.title;
		 		for(var i in this.tags){
		 			var str = tpl.replace('@tag',this.tags[i]);
		 			title =title.replace(new RegExp(this.tags[i],"gmi"),str);// this.tags.
		 			// console.log(html);
		 		}
		 		$("#article_title").html(title);

		 	}else{
		 		//when the length is 0 ,and the data change happen ,
		 		//we just set the html as the copy we left here
		 		$("#article_info").html(this.html);
		 		$("#article_title").html(this.title);
		 	}
		 	
		 	if_show_msg_box();

		}

	},
	if_show_msg_box = function(){
 		var category_length = categories.getCates().length
 		var job_length = jobs.getJobs().length 
 		var comptag_length = comptags.getComptags().length
	 	//msg box show/hide
	 	if(category_length === 0 || job_length === 0 ){
	 		$('.msgbox').removeClass('hide');
	 		$('button[type="submit"]').addClass('disabled')
	 	}
	 	else{
 		 		$('button[type="submit"]').removeClass('disabled')
 		 		$('.msgbox').addClass('hide');
	 		 }
	},
	next_action_ipt = function($value){
		var nextaction = $('input[name="nextaction"]');
		if( nextaction )
			nextaction.val($value);

	},
	create_init = function(){
		$("#preview").click(function() {
			next_action_ipt('preview');
	 	});
		$("#next").click(function(){
			next_action_ipt('next');
		})
	},
	year_init = function(){
		var init_year =  $('input[name="Article[year]"]').val();
		$('.year[data-year='+init_year+']').addClass('btn-primary')
		$('.year').click(function(){
			var year = $(this).text();
			$('.year').removeClass('btn-primary');
			$(this).addClass('btn-primary');
			$('input[name="Article[year]"]').val(year);
		});
	},
	update_init = function(){
	 	$("#preview").click(function() {
			next_action_ipt('preview');
	 	});
	 	$("#next").click(function(){
			next_action_ipt('next');
	 	})
	 	$("#groupby").click(function() {
			next_action_ipt('groupby');
	 	});
	 	$("#delete").click(function() {
			if(window.confirm('你确定要删除么？')){
				var id = $(this).data('id');
        		var form = $("<form></form>")
		        form.attr('action','/article/delete?id='+id)
		        form.attr('method','post')
		        var input_nextaction = $("<input type='hidden' name='nextaction' value='next' />")
		       	var input_csrf = $("<input type='hidden' name='_csrf' value='"+$("meta[name='csrf-token']").attr('content')+"' />")
		        form.append(input_nextaction)
		        form.append(input_groupby)
		        form.append(input_csrf)
		        form.appendTo("body")
		        form.css('display','none')
		        form.submit()
			}else{
				return false;
			}
		})
	},
	groupby_init = function(){
	 	//disable click action in this popup div 
	 	$('.dropdown-menu-multiselect').click(function(){return false;})
	 	var offsetTop = $('.pinned').offset().top
	 	$(window).scroll(function(event) {
		 	//pinned the element
		    if( $(this).scrollTop() > offsetTop )
		    	$('.pinned').addClass('pinned-top');
		    else 
		    	$('.pinned').removeClass('pinned-top');
	 	});
	 	$("#preview").click(function() {
			next_action_ipt('preview');
	 	});
	 	$("#next").click(function(){
			next_action_ipt('next');
	 	})
	 	$("#update").click(function() {
			next_action_ipt('update');
	 	});
	 	$("#delete").click(function() {
			if(window.confirm('你确定要删除么？')){
				var id = $(this).data('id');
        		var form = $("<form></form>")
		        form.attr('action','/article/delete?id='+id)
		        form.attr('method','post')
		        var input_nextaction = $("<input type='hidden' name='nextaction' value='next' />")
		        var input_groupby = $("<input type='hidden' name='groupby' value='1' />")
		        var input_csrf = $("<input type='hidden' name='_csrf' value='"+$("meta[name='csrf-token']").attr('content')+"' />")
		        form.append(input_nextaction)
		        form.append(input_groupby)
		        form.append(input_csrf)
		        form.appendTo("body")
		        form.css('display','none')
		        form.submit()
			}else{
				return false;
			}
	 	});
	 	$('button[type="submit"]').click(function(){
	 		var category_length = categories.getCates().length
	 		var job_length = jobs.getJobs().length 
	 		if(category_length === 0 || job_length == 0 ){
	 			$('.msgbox').removeClass('hide');
	 			return false;
	 		}
	 	})

	 // 	//Ueditor init 
		// var ue = UE.getEditor('article-info',
		// 	{"serverUrl":false,"toolbars":[["bold","underline","forecolor","fontsize"]],
		// 	"retainOnlyLabelPasted":true,"pasteplain":true,"enableContextMenu":false});
	 // 	//first bind the focus event
	 // 	ue.addListener('focus', function(){
	 // 		//close all open dropdown div
	 // 		$('.form-group.open').removeClass('open');
	 // 	})
	 // 	//bind the content blur event
	 // 	ue.addListener('blur',function(){
	 // 		var content = ue.getContentTxt();
	 // 		var dists = comptags.dists
	 // 		for( var i in dists ){
	 // 			if( content.indexOf(dists[i]) >0)
	 // 				comptags.push(dists[i]);
	 // 		}
	 // 		// $('#ue_preview').html(ue.getContent());
	 		
	 // 	})
	 	// $.getJSON('/comptag',function(data){
	 	// 	comptags.dists = data;
	 	// 	if()
	 	// 	var content = $('#article_info').html();

	 	// });

	 	//bind event to ue_preview's text selected
	 	//TODO: 
	},
	job_init = function(){
	 	//seek if there is already item selected ? just 4 update action
	 	$('.dropdown-menu .selected-box.job span.job').each(function(index, el) {
	 		var id = $(el).data('id');
	 		var name = $(el).data('name');
	 		jobs.push(id,name)
	 	});

	 	//bind click event to the category ,not the selected item ,just run once for init; 
	 	$('.btn-fluid-list a.job').click(function(){
	 		var exist = false;
	 		var id = $(this).data('id');
	 		var name = $(this).data('name');
	 		var jbs = jobs.getJobs();
	 		for(var i in jbs){
	 			if( jbs[i].id == id){
	 				exist =true;
	 				jobs.splice(i,1);
	 				break;
	 			}
	 		}
	 		if(!exist)
	 			jobs.push(id,name);
	 		return false;
	 	});
	},
	category_init = function(){
	 	//seek if there is already item selected ? just 4 update action
	 	$('.dropdown-menu .selected-box.category span.category').each(function(index, el) {
	 		var id = $(el).data('id');
	 		var name = $(el).data('name');
	 		categories.push(id,name)
	 	});

	 	//bind click event to the category ,not the selected item ,just run once for init; 
	 	$('.btn-fluid-list a.category').click(function(){
	 		var exist = false;
	 		var id = $(this).data('id');
	 		var name = $(this).data('name');
	 		var cates = categories.getCates();
	 		for(var i in cates){
	 			if( cates[i].id == id){
	 				exist =true;
	 				categories.splice(i,1);
	 				break;
	 			}
	 		}
	 		if(!exist)
	 			categories.push(id,name);
	 		return false;
	 	});
	},
	//init the comptags
	comptags_init = function(){
		//seek if there is already item selected ? just 4 update action
	 	$('.comptags .taglist span.label').each(function(index, el) {
	 		var name = $(el).data('name');
	 		comptags.push(name)
	 	});
	 	
	 	if(comptags.getComptags().length == 0){
		 	$.getJSON('/company-tag',function(data){

		 		var content = $('#article_info').html();
		 		var title = $('#article_title').html();
		 		for( var i in data ){
		 			var preg = new RegExp(data[i],"gmi");
	 				if( preg.test(content)|| preg.test(title) )
	 					comptags.push(data[i])
		 		}

		 	});
	 	}
	 	//bind blur event
	 	$(".comptags input.newtag").blur(function() {
 			var newTag = $(this).val();
			if( newTag != '' )
				comptags.push(newTag);
			$(this).val('')
	 	});

		//bind keydown event
		$(".comptags input.newtag").keydown(function(e){
			//enter pressed
			if(e.keyCode === 13 ){
				var newTag = $(this).val();
				if( newTag != '' )
					comptags.push(newTag);
				$(this).val('')
				return false;
			}
			else if(e.keyCode == 8 && $(this).val()==''){
				comptags.splice();
				return false;
			}
		})
		//focus the input element when click to this form-control
		$(".comptags .form-control").click(function(){
			$("input[class='newtag']").focus()
		});
		//select text to add tags
		$("#article_info,#article_title").mouseup(function(e){
			//get select txt
			var txt = "";
			if(document.selection) {
				txt = document.selection.createRange().text;	// IE
			} else {
				txt = document.getSelection();
			}
			txt = txt.toString();
			//
			var button = $("#btn_tag");
			//calc
			var sh = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
			var left = (e.clientX - 40 < 0) ? e.clientX + 20 : e.clientX - 40, 
			top = (e.clientY - 40 < 0) ? e.clientY + sh + 20 : e.clientY + sh - 40;
			if(txt){
				button.removeClass('hide').css({"left":left+"px","top":top+"px"});
				button.unbind("click").click(function(){
					comptags.push(txt);
				})
			}
			else{
				button.addClass('hide');
			}
		})

		$('#clear_company_tag').click(function(){
			comptags.clear_all()
		})
	},
	//init action
	 init = function(){
	 	$('[data-toggle="tooltip"]').tooltip()
	 	year_init();
	 	if( window.location.pathname.indexOf("update") > 0 ){
		 	if(window.location.search.indexOf('groupby')>0){
		 	groupby_init();
		 	job_init();
		 	category_init();
		 	comptags_init();
		 	if_show_msg_box();
		 }else{
		 	update_init();
		 }
	 	}else if(window.location.pathname.indexOf("create") > 0){
	 		create_init();
	 	}
	 };
	 return {
        init: init
     };
})(jQuery);

(function() {
    app.init();
})();