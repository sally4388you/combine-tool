var 
/*
* This is trade's selecting part
* author: Yang Xujing
*/
categories = {
  cates : Array(),
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
  },
  init: function() {
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
    $('#return').click(function() {
      location.href = "/filltrade";
    });
    $('#next').click(function() {
      $('#isnext').val('1');
      $('#tags').submit();
    });
   }
},
/*
* This is tags part
* Author: Qi Buer
*/
tags = {
  init : function() {
    $('#labels h4').each(function() {
      $(this).click(function() {
        var new_id = $(this).attr('id').replace('label', '');
        var old_id = $('#pic-id').val();
        $.post("/filltrade/regroup", "new=" + new_id + "&old=" + old_id, function(data) {
          if (data == "suc") {
            location.href = "/filltrade/update?id=" + new_id;
          }
        });
      });
    });
  }
};



/*
* This is pictureshot part
* from cropper's example and revise a few things
*/
(function (factory) {
  categories.init();
  tags.init();
  if (typeof define === 'function' && define.amd) {
    define(['jquery'], factory);
  } else if (typeof exports === 'object') {
    // Node / CommonJS
    factory(require('jquery'));
  } else {
    factory(jQuery);
  }
})(function ($) {

  'use strict';

  var console = window.console || { log: function () {} };

  function CropAvatar($element) {
    this.$container = $element;

    this.$avatarView = this.$container.find('.avatar-view');
    this.$avatarModal = this.$container.find('#avatar-modal');

    this.$avatarForm = this.$avatarModal.find('.avatar-form');
    this.$avatarData = this.$avatarForm.find('.avatar-data');
    this.$avatarSave = this.$avatarForm.find('.avatar-save');

    this.$avatarWrapper = this.$avatarModal.find('.avatar-wrapper');
    this.$avatarPreview = this.$avatarModal.find('.avatar-preview');

    this.init();
  }

  CropAvatar.prototype = {
    constructor: CropAvatar,
    divOnclick : null,

    init: function () {
      this.$avatarView.on('click', $.proxy(this.click, this, $(this)));
      this.$avatarForm.on('submit', $.proxy(this.submit, this));
      this.$avatarSave.on('click', function() {
      	$('.avatar-form').submit();
      });
    },

    click: function (one, div) {
      var picture = div.toElement.src;
      $.post("/filltrade/getimage", "url=" + picture, $.proxy(this.show, this));
    },

    show: function () {
      this.$avatarModal.modal('show');
      this.url = '../images/temp.png?t=' + Math.random();
      this.startCropper();
    },

    submit: function () {
        this.ajaxUpload();
        return false;
    },

    startCropper: function () {
      var _this = this;

      if (this.active) {
        this.$img.cropper('replace', this.url);
      } else {
        this.$img = $('<img src="' + this.url + '">');
        this.$avatarWrapper.empty().html(this.$img);
        this.$img.cropper({
          aspectRatio: 1 / 1,
          autoCropArea: 1,
          strict: false,
          guides: false,
          dragCrop: false,
          preview: this.$avatarPreview.selector,
          checkImageOrigin: false,
          crop: function (data) {
            var json = [
                  '{"x":' + data.x,
                  '"y":' + data.y,
                  '"height":' + data.height,
                  '"width":' + data.width + '}'
                ].join();

            _this.$avatarData.val(json);
          }
        });

        this.active = true;
      }

      this.$avatarModal.one('hidden.bs.modal', function () {
        _this.$avatarPreview.empty();
        _this.stopCropper();
      });
    },

    stopCropper: function () {
      if (this.active) {
        this.$img.cropper('destroy');
        this.$img.remove();
        this.active = false;
      }
    },

    ajaxUpload: function () {
      var url = this.$avatarForm.attr('action'),
          data = new FormData(this.$avatarForm[0]),
          _this = this;

      $.ajax(url, {
        type: 'post',
        data: data,
        dataType: 'json',
        processData: false,
        contentType: false,

        success: function (data) {
          _this.submitDone(data);
        },

        error: function (XMLHttpRequest, textStatus, errorThrown) {
          _this.submitFail(textStatus || errorThrown);
        },
      });
    },

    submitDone: function (data) {
      if ($.isPlainObject(data) && data.state === 200) {
        if (data.result) {
          this.url = data.result;
          this.uploaded = false;
          this.cropDone();

        } else if (data.message) {
          this.alert(data.message);
        }
      } else {
        this.alert('Failed to response');
      }
    },

    submitFail: function (msg) {
      this.alert(msg);
    },

    cropDone: function () {
      this.$avatarForm.get(0).reset();
      $('#logo').attr('src', "/images/logo/" + $('#pic-id').val() + ".png?t=" + Math.random());
      this.stopCropper();
      this.$avatarModal.modal('hide');
    },

    alert: function (msg) {
      var $alert = [
            '<div class="alert alert-danger avatar-alert alert-dismissable">',
              '<button type="button" class="close" data-dismiss="alert">&times;</button>',
              msg,
            '</div>'
          ].join('');

      // this.$avatarUpload.after($alert);
    }
  };

  $(function () {
    return new CropAvatar($('#crop-avatar'));
  });

});
