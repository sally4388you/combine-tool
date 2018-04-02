window.onload = function(){

var app = angular.module('examinApp',['ngRoute','ngResource','ngSanitize','ui.bootstrap']);

//config
app.config(['$routeProvider', '$httpProvider', function($routeProvider, $httpProvider){
	$routeProvider
		.when('/',{
            controller: 'ListController',
            templateUrl: '/partials/list.html',
		})
        .when('/new', {
            controller: 'AddController',
            templateUrl: '/partials/new.html',

        })
        .when('/edit/:id', {
            controller: 'AddController',
            templateUrl: '/partials/new.html',

        })
        .when('/view/:id',{
        	controller:'ViewController',
        	templateUrl:'/partials/view.html',
        })
        .otherwise('/');

 	$httpProvider.defaults.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

	//config layer skin
	layer.config({
	    extend: ['skin/moon/style.css'], 
	    skin: 'layer-ext-moon' 
	});
}]);
//service to get work_category
//the format of the json should be
// {id:name, id:name, id:name ...}
app.service('workCategoryFactory',['$http','$q',function($http,$q){
	var _url = '/work_category.json';
	this.get_work_category_json = function(){
		var defer = $q.defer();
		$http({
			cache:true,
			method:'GET',
			url:_url
		})
		.success(function(data){
			defer.resolve(data)
		})
		.error(function(){
			defer.reject('Error')
		})
		return defer.promise;
	}
}]);

//service for restful 
app.factory('examService', ['$resource', function($resource) {
	return $resource('/exams/:id', {
		  id: '@id'
		} , 
		{ 
		    'query':  {method:'GET', isArray:false},
			'update': { method:'PUT' }
  		}
  	);
}]);


//add controller 4 add action and bind to url #/new
app.controller('AddController', ['$scope','$routeParams','$location','workCategoryFactory','examService', 
	function($scope,$routeParams,$location,workCategoryFactory,examService){
	
	//load needed data
	workCategoryFactory.get_work_category_json()
		.then(function(data){
			$scope.work_categories = data
			// console.log($scope.trade);
			
		},function(){
			layer.msg('加载职位分类失败,请联系管理员',{icon:5,shade: true,time:1000})
		})

	//data 4 form which used to the form post to backend server
	
	// var csrf = $('meta[name="csrf-token"]').attr('content');

	$scope.formdata = {
		company_tag: "",
		work_name: "",
		year: 2015,
		work_category: new Array(),
		question_answer: new Array(),
		// _csrf : csrf
	}
	//question answer 4 ueditor
	$scope.question_answer = {
		question:"",
		answer:"",
		ue_question:{},
		ue_answer:{},
		index:null,
		action:null,
		data:new Array()
	}

	//common data of work category 
	// $scope.work_categories = workCategoryFactory.get_work_category_json()

	//safeApply 2 solute the error: $apply already in progress
	$scope.safeApply = function(fn) {
	  var phase = this.$root.$$phase;
	  if(phase == '$apply' || phase == '$digest') {
	    if(fn && (typeof(fn) === 'function')) {
	      fn();
	    }
	  } else {
	    this.$apply(fn);
	  }
	};

	//click event
	
	//save
	$scope.save_question_answer = function(){
		if(!this.question_answer.ue_question.hasContents() ){
			layer.msg('内容未录入完整',{icon:2,time:1000});
			return false;
		}
		//get data and data without format
		var question = this.question_answer.ue_question.getContent();
		var question_txt = this.question_answer.ue_question.getContentTxt();
		var answer = this.question_answer.ue_answer.getContent();
		var answer_txt = this.question_answer.ue_answer.getContentTxt();

		if(this.question_answer.action === 'insert'){
			var index = this.question_answer.index;

			//update
			this.question_answer.data.splice(index ,0 , {
				question: question,			answer: answer,
				question_txt: question_txt, answer_txt: answer_txt
			})
			layer.msg('第'+(index+1)+'题 已插入',{time:1000,icon:1});

		}
		else if(this.question_answer.action === 'update'){
			var index = this.question_answer.index;
			//update
			this.question_answer.data[index] = {
				question: question,			answer: answer,
				question_txt: question_txt, answer_txt: answer_txt
			}
			layer.msg('第'+(index+1)+'题 已更新',{time:1000,icon:1});
			//reset ueditor
			this.ueditor_reset();
		}else{
			
			//push
			this.question_answer.data.push({
				question: question,			answer: answer,
				question_txt: question_txt, answer_txt: answer_txt
			})
			layer.msg('已添加',{time:1000,icon:6});
		}
		//reset ueditor
		this.ueditor_reset();
		return true;
	}

	//click event for preview question and answer
	$scope.question_answer_preview = function(index){

		if( index!==null && isNaN(index) && index !== 'all' )
			return;
		var title = ($scope.formdata.company_tag+" "||"") + ($scope.formdata.year+" "|| "") 
				+ ($scope.formdata.work_name +" "|| "" );

		var queation_answer_tpl = "<h2>题目</h2><div>@question</div><hr/><h2>答案</h2><div>@answer</div><hr/><br/>";
		var content_tpl ="<div class='container'>@inner</div>"
		if( index === 'all' ){
			var qa_num = "<h1>第@index题</h1><hr/>";
			var data = $scope.question_answer.data
			var content = '';
			for(var i in data)
				content += qa_num.replace('@index',(Number(i)+1))
					+ queation_answer_tpl.replace('@question',data[i].question).replace('@answer',data[i].answer);
			content = content_tpl.replace('@inner',content);
			layer.open({
			    type: 1,
			    title: title,
			    shadeClose: true,
			    area: ['800px', '600px'],
			    maxmin: true,//show the maxmin button
			    content: content
			});
		}
		else{
			title +="第 "+ (index+1)+ " 题";
			var question = $scope.question_answer.data[index].question;
			var answer   = $scope.question_answer.data[index].answer;
			var content =  content_tpl.replace('@inner',
				queation_answer_tpl.replace('@question',question).replace('@answer',answer)
				);

			layer.open({
			    type: 1,
			    title: title,
			    shadeClose: true,
			    area: ['800px', '600px'],
			    maxmin: true,//show the maxmin button
			    content: content
			});
		}
	}

	//click event for modify exists record
	$scope.question_answer_edit =function(index){
		// console.log(index);
		if( index===null || isNaN(index) || index<0 ||index > this.question_answer.data.length-1 )
			return;
		var fn=function(){
			$scope.question_answer.action='update'
			$scope.question_answer.index = index;
			$scope.question_answer.ue_question.setContent($scope.question_answer.data[index].question);
			$scope.question_answer.ue_answer.setContent($scope.question_answer.data[index].answer);
		}
		this.question_answer_confirm_save(fn);
	}
	//click event for insert a row 
	$scope.question_answer_insert=function(index){
		console.log(index);
		if( index===null || isNaN(index) || index<0 ||index > this.question_answer.data.length-1 )
			return;
		var fn = function(){
			$scope.question_answer.action='insert'
			$scope.question_answer.index = index;
			$scope.question_answer.ue_question.setContent('');
			$scope.question_answer.ue_answer.setContent('');
		}
		this.question_answer_confirm_save(fn);
	}

	//delete a exists item 
	$scope.question_answer_delete = function(index){
		if( index===null || isNaN(index) || index<0 ||index > this.question_answer.data.length-1 )
			return;
		layer.confirm('是否真的要删除', {
		    btn: ['删除','放弃'], 
		    shade: false,
		    shift:6,
		    icon:2,
		    closeBtn:false
		}, 
		//delete it
		function(){
			$scope.question_answer.data.splice(index,1);
			//force to refresh
			//according to 
			//http://stackoverflow.com/questions/15475601/ng-repeat-list-in-angular-is-not-updated-when-a-model-element-is-spliced-from-th
			$scope.$apply();
		    layer.msg('已删除', {icon:1, time:1000});
		},
		//forget it
		function(){
		    layer.msg('没有删除', {icon:6, time:1000});
		}
		);
	}

	//confirm whethe to save content or not ,when we want to setContent
	$scope.question_answer_confirm_save = function(fn){
		if( !this.question_answer.ue_question.hasContents() 
				|| !this.question_answer.ue_answer.hasContents() ){
			fn()
			return true;
		}
		
		//if there has contents ,we confirm first
		//unsaved content exists , need to confirm
		layer.confirm('有未保存的数据，是否保存？', {
		    btn: ['保存','不保存','放弃'], 
		    shade: false,
		    shift:6,
		    icon:0,
		    closeBtn:false
		}, 
		//save it
		function(){
			$scope.save_question_answer()
			fn()
		},
		//unsave it
		function(){
		    layer.msg('好的，没保存', {icon:6, time:1000});
		    fn();
		},
		//forget it
		function(){
		    layer.msg('好的，已放弃', {icon:6, time:1000});
		}
		);
	}


	//toggle work_category in the formdata
	$scope.work_category_toggle =function(work_category_id, $event){
		// console.log(work_category_id);
		var index = this.formdata.work_category.indexOf(work_category_id);
		if(index >= 0)
			this.formdata.work_category.splice(index,1);
		else this.formdata.work_category.push(work_category_id);
		$event.stopPropagation()
	}
	//stop propagation
	$scope.dropdown_click = function($event){
		$event.stopPropagation()
	}

	//load datamodel for edit action
	$scope.load_data = function(id){
		examService.get( {id: id },function(data){
			$scope.formdata.id = data.id
			$scope.formdata.company_tag = data.company_tag
			$scope.formdata.work_name = data.work_name
			$scope.formdata.year = data.year
			//work_cateory is strange ,because if we not do this ,it can not be located
			//if change the work_category use int as index ,we can not order them right
			for(var i in data.work_category){
				$scope.formdata.work_category.push('\''+data.work_category[i]+'\'');
			}

			//bind data
			for(var i in data.question_answer){
				var question = data.question_answer[i].question
				var answer = data.question_answer[i].answer
				var question_txt = question
				var answer_txt = answer
				$scope.question_answer.data.push({
					question:question,answer:answer,
					question_txt:question_txt,answer_txt:answer_txt
				})
			}


		},function(data){
			//load falied
			layer.msg('读取数据失败！',{icon:5,time:2000,shade:true});
			return $location.path('/')
		})
	}


	//inti of ueditor and the question_answer 
	$scope.ueditor_init = function(){
		//before init delete old one
		var hasProp =false;
		for (var prop in UE.instants){
			hasProp = true;
			break;
		}
		if (hasProp){
			UE.delEditor('question')
			UE.delEditor('answer');
		}
		
		//init the ueditor
		this.question_answer.ue_question = UE.getEditor('question');
		this.question_answer.ue_answer = UE.getEditor('answer');
		console.log('ueditor init')
		//bind event when the ueditor content change
		//only change in ueditor will be bind to the ng-model
		//no ng-model change will be set in ueditor..
		//
		//if b-bind , it will be very strange
 		this.question_answer.ue_question.addListener('contentchange',function(){
			//watch data change and update view
			$scope.safeApply(function() {
				$scope.question_answer.question = $scope.question_answer.ue_question.getContent();
			})
		});
		//same as before
		this.question_answer.ue_answer.addListener('contentchange',function() {
			$scope.safeApply(function() {
				// console.log(UE)
				$scope.question_answer.answer = $scope.question_answer.ue_answer.getContent();
			})
		});
	}

	$scope.question_answer_init = function(){
		//init the ng-model data watch
		//
		//use the 3rd argument to be true , 
		//so that we can deeply watch the array data change
		//
		//for change b-bind to the formdata
		this.$watch('question_answer.data' ,function(new_value,old_value){
			//not effect when data has not change
			if( new_value === old_value ) return false;
			//new array and push data
			var q_a_new = new Array();
			for(var i in new_value)
				q_a_new.push({'question': new_value[i].question,'answer': new_value[i].answer});
			//bind data
			$scope.formdata.question_answer = q_a_new;
		},true)
	}

	// //trade type and trade sub type init
	// $scope.work_category_init = function(){
	// 	workCategoryFactory.get_work_category_json()
	// 		.then(function(data){
	// 			$scope.work_categories = data
	// 			// console.log($scope.trade);
				
	// 		},function(){
	// 			layer.msg('加载职位分类失败,请联系管理员',{icon:5,shade: true,time:1000})
	// 		})
	// }
	$scope.formdata_reset = function(){
		//reset formdata
		this.formdata.company_tag = ""
		this.formdata.work_name = ""
		this.formdata.work_category = new Array()
		this.formdata.question_answer = new Array()
		this.formdata.id = null;
	}

	$scope.ueditor_reset = function(){
		// console.log($scope.question_answer.ue_question)
		this.question_answer.ue_question.setContent('');
		this.question_answer.ue_answer.setContent('');
		//reset the the index
		this.question_answer.index = null;
		this.question_answer.action = null;
	}

	//reset question_answer data and it's index;
	$scope.question_answer_reset =function(){
		//reset Q & A , just rest the question and answer not the ue_*,
		//so that the ue instance will not be null
		// this.question_answer.question = '';
		// this.question_answer.answer = '';
		this.question_answer.data = new Array();
		this.question_answer.index = null;
		this.question_answer.action = null;
	}

	//reset all
	$scope.reset = function(){
		this.formdata_reset();
		this.ueditor_reset();
		this.question_answer_reset()
	}

	//submit action 
	$scope.submit = function(go_to_next){
		//default is false means view
		$scope.go_to_next = go_to_next || false;
		//bind event
		var fn = function(go_to_next){
		    var index = layer.msg('好的，正在提交', {icon: 6});
			var success = function(data){
				layer.close(index);
				layer.msg('保存成功！，正在跳转', {icon: 6,time:1000});
				if($scope.go_to_next){
					$scope.reset();
					$location.path('/new')
				}else{
					console.log(data);
					$location.path('/view/'+data.id)
				}
				//todo: else 
			}
			var fail = function(){
				layer.close(index);
				layer.msg('保存失败！请重试', {icon: 5,time:1000});
			}
			if(typeof($scope.formdata.id)!=='undefined' && $scope.formdata.id !==null && $scope.formdata.id !=='')
				examService.update({id: $scope.formdata.id},$scope.formdata,success,fail)

			else examService.save($scope.formdata,success,fail)
		}
	//if there has content ,first confirm whether to save 
	if(this.question_answer.ue_question.hasContents() )
		layer.confirm('编辑器中有未保存的数据，是否保存', {
		    btn: ['保存并提交','不保存并提交','取消'], 
		    shade: false,
		    closeBtn:false,
		    icon:0
		}, function(){
			$scope.save_question_answer()
			fn()

		}, function(){
			if($scope.formdata.company_tag==='' || $scope.formdata.work_name==='' ||
			 isNaN($scope.formdata.year) || $scope.formdata.work_category.length <=0 || 
			 $scope.formdata.question_answer.length <=0 ){
				layer.msg('数据未输入完整',{icon:2,time:1000,shade:true})
			return false;
			}
			fn()
		},
		function(){
			return false;
		});
	else fn();
	}
	//init event
	$scope.init = function(){
		var index = layer.load(2, {
		    shade: [0.5,'#000'] 
		});
		// this.work_category_init();
		this.ueditor_init();
		this.question_answer_init()

		if( typeof($routeParams.id)!== 'undefined' && !isNaN($routeParams.id) ){
			this.load_data($routeParams.id);
			$scope.title = '修改真题'
		}else{
			$scope.title = '添加真题'
		}

		layer.close(index);  
	}
	//exec init when load
	$scope.init();
}]);


//controller for list
app.controller('ListController', ['$scope','examService', function($scope,examService){
	//default params
	$scope.params = {
		sort_by: 'id',
		sort_order: 'desc',
		start:1,
		length:50,
	}
	//toggle sort ,change sort_by or sort_order
	$scope.sort_toggle = function(column){
		console.log(column)
		if(this.params.sort_by === column){
			this.params.sort_order = this.params.sort_order === 'asc' ?'desc':'asc';
		}
		else{
			this.params.sort_by = column;
			this.params.sort_order = 'desc'
		}
		this.search();
	}
	//delete action
	$scope.delete= function(id){
		layer.confirm('是否确认删除', {
		    btn: ['删除','取消'], 
		}, function(){
		    var index = layer.msg('正在删除', {icon: 0});
		    examService.delete({id:id},function(){
		    	layer.close(index)
		    	layer.msg('删除成功',{icon:6,time:1000})
		    	$('#list_'+id).fadeOut();
		    },function(){
		    	layer.close(index)
		    	layer.msg('删除失败，请重试',{icon:2,time:1000})
		    })
		}, function(){
			return true;
		});

	}
	//search action
	$scope.search = function(){
		var index = layer.msg('努力加载中...',{icon:6})
		examService.query({
			isajax:true,
			start:($scope.params.start -1)*$scope.params.length,
			length:$scope.params.length,
			sort_by:$scope.params.sort_by,
			sort_order:$scope.params.sort_order
		},function(data){
			$scope.count = data.count;
			$scope.data = data.data;
			layer.close(index);
		},function(){
			layer.close(index);
			layer.msg('加载失败',{icon:2,time:1000})
		})
	}
	$scope.search();
}]);

//controller for view
app.controller('ViewController',['$scope','$routeParams','$location','examService','workCategoryFactory',
	function($scope,$routeParams,$location,examService,workCategoryFactory){
	$scope.data= {};
	//load work category
	workCategoryFactory.get_work_category_json()
	.then(function(data){
		$scope.work_categories = data
		// console.log($scope.trade);
		
	},function(){
		layer.msg('加载职位分类失败,请联系管理员',{icon:5,shade: true,time:1000})
	})

	//delete action
	$scope.delete= function(id){
		layer.confirm('是否确认删除', {
		    btn: ['删除','取消'], //按钮
		}, function(){
		    var index = layer.msg('正在删除', {icon: 0});
		    examService.delete({id:id},function(){
		    	layer.close(index)
		    	layer.msg('删除成功',{icon:6,time:1000})
		    	$location.path('/')
		    },function(){
		    	layer.close(index)
		    	layer.msg('删除失败，请重试',{icon:2,time:1000})
		    })
		}, function(){
			return true;
		});

	}

	//init action
	$scope.init = function(){
		var index = layer.msg('加载中...',{icon:0});
		//get model
		examService.get( {id: $routeParams.id },function(data){
			layer.close(index);
			$scope.data.id = data.id
			$scope.data.company_tag = data.company_tag
			$scope.data.work_name = data.work_name
			$scope.data.year = data.year
			$scope.data.work_category= new Array();
			for(var i in data.work_category){
				$scope.data.work_category.push('\''+data.work_category[i]+'\'');
			}

			$scope.data.question_answer = data.question_answer

		},function(data){
			layer.close(index);

			layer.msg('读取数据失败！',{icon:5,time:2000,shade:true});
			// return $location.path('/')
		})
	}
	//exec init action
	$scope.init();
}])

angular.bootstrap(document, ["examinApp"]);

}