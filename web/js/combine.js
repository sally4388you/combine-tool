'use strict';
var xxx;
var combine = (function($) {
    var
    eleDrag = null,
    target = null,
    divOnclick = null,
    tags = {
        tgs : Array(),
        //tags' physical action
        dragEnter : function(ev) {
            target = ev.target;
            if (target != eleDrag) {
                target.className = target.className + " dragover";
            }
        },
        dragLeave : function(ev) {
            ev.target.className = ev.target.className.replace(/ dragover/, "");
        },
        drop : function(thisdiv) {//this is target;
            thisdiv.className = thisdiv.className.replace(/ dragover/, "");
            var eleDrag_id = eleDrag.id.match(/\d+/);
            var target_id = thisdiv.id.replace("group", "");           

            if (eleDrag == null) return;
            if (target_id == eleDrag_id) return false;
            if (eleDrag) {
                this.change(target_id, eleDrag_id);
                this.clear($("#group" + eleDrag_id)[0]);
                $.post("/combine/merge", "el=" + eleDrag_id + "&target=" + target_id, function(data) {
                    if (typeof(data) == 'object') {
                        $('#tips').html(tags.tgs[eleDrag_id][1] + "<font color='#000'>被合并到</font>" + tags.tgs[target_id][1]);
                        detailBox.showDetail(data);
                    }
                    else alert('数据合并失败!请刷新恢复数据.');
                });
            }
        },
        change : function(target_id, eleDrag_id) {
            //changes in className,fadeOut,cursor
            $('#group' + target_id).attr('class', 'btn btn-sm btn-success mybutton');
            this.tgs[target_id][0] = "success";
            searchBox.change(target_id, eleDrag_id);
            dealing.change(eleDrag_id);
        },
        clear : function(div) {
            $(div).fadeOut(function() {
                $(this).css('display', '');
                $(this).css('opacity', '0');
                $(this).css('cursor','default');
            });
            div.onclick = function(ev) {}
            div.ondblclick = function(ev) {}
            div.ondragstart = function(ev) {return false;}
        },
        delt : function() {
            var isDelt = false;
            var id = divOnclick.id.replace(/group/, '');
            if (this.tgs[id][0] == 'default') {
                divOnclick.className = "btn btn-sm btn-primary mybutton";
                this.tgs[id][0] = "primary";
                $.post("/combine/delt", "id=" + id, function(data) {
                    if (data != "success") {
                        alert('数据处理失败!');
                    }
                });
            }
            else {
                alert('已处理过');
            }
            divOnclick = null;
        },
        rename : {
            isFocus : false,
            thisdiv : null,
            textInit : null,
            rename_d : false,
            act : function() {
                this.thisdiv = divOnclick;
                this.textInit = this.thisdiv.innerHTML;
                var id = this.thisdiv.id.replace('group', '');
                
                $('#rename').attr('readonly', false);
                $('#rename').val(this.textInit);
                $('#rename').bind("propertychange input",function(){
                    tags.rename.thisdiv.innerHTML = $(this).val();
                    $('#searchbox').val('');
                    $('#search').html('');
                });
                $('#renameForm')[0].onsubmit = function() {
                    var name = $('#rename').val();
                    if (name != "") {
                        if (tags.tgs[id][0] == "default") {
                            tags.tgs[id][0] = "primary";
                        }
                        tags.rename.thisdiv.className = "btn btn-sm btn-" + tags.tgs[id][0] + " mybutton";
                        var string = "id=" + id + "&name=" + name.replace(/&/g, '%26') + "&rename_d=" + tags.rename.rename_d;
                        $.post("/combine/rename", string, function(data) {
                            if (data == "") {
                                tags.tgs[id][1] = name;
                                tags.rename.init();
                            } else if (/remainId/.test(data)) {
                                var obj = eval ("(" + data + ")");
                                tags.clear($('#group' + obj.initId)[0]);
                                $('#tips').html(tags.tgs[obj.initId][1] + "<font color='#000'>被合并到</font>" + tags.tgs[obj.remainId][1]);
                            } else if(/有重名/.test(data)) {
                                if (confirm(data)) {
                                    tags.rename.rename_d = true;
                                    $('#renameForm')[0].onsubmit();
                                }
                            } else {
                                alert(data);
                            }
                        });
                    } else {
                        alert('修改名称不能为空');
                        $('#rename').val(tags.rename.textInit);
                        tags.rename.thisdiv.innerHTML = tags.rename.textInit;
                    }
                    return false;
                }
            },
            init : function() {
                tags.rename.rename_d = false;
                $('#rename').val('');
                $('#rename').attr('readonly', true);
                if (this.thisdiv != null) {
                    this.thisdiv.innerHTML = tags.tgs[this.thisdiv.id.replace('group', '')][1];
                }
                $('#renameForm')[0].onsubmit = function() {
                    return false;
                }
            }
        },
        add : function() {
            var name = $('#searchbox').val();
            if (name == "") {
                alert('添加内容不为空.');
            } else if (divOnclick == null) {
                alert('请选择一个插入索引.');
            } else {
                $.post("/combine/addtag", "name=" + name, function(data) {
                    if (data.match(/^\d+$/)) {
                        $(divOnclick).before(addElmt("group" + data, "btn btn-sm btn-default mybutton", name));
                        tags.tgs[data] = ['default', name];
                        tags.init($('#group' + data)[0], data);
                    }
                    else alert('添加失败，可能有重名');
                });
            }
        },
        init : function(div, index) {
            div.onclick = function() {
                divOnclick = this;
                detailBox.intoDetail(index);
            }
            div.ondblclick = function(ev){dealing.into(this);}
            div.ondragstart = function(ev) {eleDrag = ev.target;}
            div.ondragleave = function(ev) {tags.dragLeave(ev);}
            div.ondragover = function(ev) {ev.preventDefault();}
            div.ondragenter = function(ev) {tags.dragEnter(ev);}
            div.ondrop = function() {tags.drop(this);}
        }
    },
    searchBox = {
        ids : Array(),
        isFocus : false,
        search : function(val, index) {
            $("#search").html('');
            //recover matched elements last time
            if (this.ids.length > 0) {
                for (var j in this.ids) {
                    $('#group' + this.ids[j]).attr('class', 'btn btn-sm btn-' + tags.tgs[this.ids[j]][0] + ' mybutton');
                }
                this.ids.length = 0;
            }

            var i = 0;
            //filter some unuseful words
            var keyword = val;
            $('#searchbox').val(keyword);
            if (keyword != "中国" && keyword != "银行" && keyword != "中国银行") {
                keyword = keyword.replace("中国", "").replace(/(有限)*(公司|集团|银行)+/, "").toLowerCase();
            }
            if (keyword == "") return;

            //main part
            $('#labels div').each(function() {
                if (this.style.opacity != "" && this.style.opacity == "0") {
                    return true;
                }
                if (new RegExp(keyword).test(this.innerHTML.toLowerCase()))  {
                    var id = this.id.replace(/group/, "");
                    $('#search').append(addElmt("search" + id, this.className, this.innerHTML));
                    searchBox.ids[i ++] = id;
                    searchBox.init($('#search' + id)[0]);
                }
            });

            //change matched elements
            for (var j in this.ids) {
                $('#group' + this.ids[j]).attr('class', 'btn btn-sm btn-warning mybutton')
            }
            if (index != '') {
                searchBox.ids[i ++] = index;
                $('#group' + index).attr('class', 'btn btn-sm btn-danger mybutton');
            }
        },
        change : function(target_id, eleDrag_id) {
            var fadeId = '#search' + eleDrag_id;
            var classId = '#search' + target_id;
            if($(fadeId).length > 0) {
                $(fadeId).fadeOut();
            }
            if($(classId).length > 0) {
                $(classId).removeClass().addClass("btn btn-sm btn-success mybutton");
            }
        },
        init : function(div) {
            div.ondragstart = function(ev) {eleDrag = ev.target;}
        }
    },
    detailBox = {
        count : 0,
        intoDetail : function(index) {
            $('#combined').html('');
            tags.rename.init();
            tags.rename.act();
            searchBox.search(tags.tgs[index][1], index);
            if (tags.tgs[index][0] == "success") {
                $.get("/combine/search?id=" + index, function(data) {
                    if (typeof(data) != "object") {
                        alert("error");
                    }
                    else {
                        detailBox.showDetail(data);
                    }
                });
            }
            this.count = 0;
        },
        showDetail : function(data) {
            var intoCombined = "";
            for (var i = 0; i < data.group.length; i ++) {
                intoCombined += "<h4 id='detail" + data.group[i].id + "'>" +
                        "<span class='label label-info'>" + 
                        data.group[i].name +
                        " &nbsp;<i class='fa fa-times'></i>" +
                        "</span>" +
                    "</h4>";
                this.count += 1;
            }
            $('#combined').html(intoCombined);
            $('#combined h4').each(function() {
                $(this).click(function() {
                    detailBox.returnBack(this.id);
                })
            });
        },
        returnBack : function(id) {
            this.count -= 1;
            $('#' + id).fadeOut();
            $.post("/combine/return", "id=" + id.replace(/detail/, ""), function(data) {
                if (/[^0-9]+/.test(data)) {
                    alert('数据还原失败!');
                }
                else {
                    id = id.match(/\d+/);
                    var content = $('#detail' + id + ' span').text().replace(/\s+/, '');
                    var addText = addElmt("group" + id, "btn btn-sm btn-primary mybutton", content);
                    if (divOnclick != null) {
                        $(divOnclick).before(addText);
                    } else {
                        $('#labels').append(addText);
                    }
                    tags.tgs[id] = ["primary", content];
                    tags.init($('#group' + id)[0], id);
                    if (detailBox.count <= 0) {
                        $('#group' + data).attr('class', 'btn btn-sm btn-primary mybutton');
                        tags.tgs[data][0] = "primary";
                    }
                }
            });
        }
    },
    dealing = {
        into : function(div) {
            var id = div.id.replace(/group/, "");
            $('#dealing').append(addElmt("dealing" + id, div.className, div.innerHTML));
            tags.clear(div);
            this.init($('#dealing' + id)[0]);
        },
        out : function(div) {
            var id = div.id.match(/\d+/);
            $(div).fadeOut();
            $(div).remove();
            $('#group' + id).css('opacity', '100');
            tags.init($('#group' + id)[0], id);
        },
        change : function(eleDrag_id) {
            var fadeId = '#dealing' + eleDrag_id;
            if($(fadeId).length > 0) {
                $(fadeId).fadeOut();
            }
        },
        init : function(div) {
            div.ondblclick=function(){dealing.out(this);}
            div.ondragstart = function(ev) {eleDrag = ev.target;}
        }
    },
    trash = {
        lastClick : null,
        act : function(type) {
            var id = divOnclick.id.replace("group", "");
            if (tags.tgs[id][0] == "success") {
                alert('不能删除有关联的标签.');
                return ;
            }
            tags.clear(divOnclick);
            $('#searchbox').val('');
            $('search').html('');
            var isDelete = (type == "out") ? 0 : 1;
            var string = "id=" + id + "&isDelete=" + isDelete;
            $.post("/combine/intotrash", string, function(data) {
                if (data != "success") {
                    alert('数据删除失败!');
                }
            });
            divOnclick = null;
        },
        initList : function() {
            $('#labels div').each(function() {
                var id = this.id.replace(/group/, '');
                tags.tgs[id] = [this.className.match(/btn-([a-z]*) mybutton/)[1],this.innerHTML];
                $(this).click(function() {
                    if (trash.lastClick != null) {
                        $(trash.lastClick).attr('class', 'btn btn-sm btn-primary mybutton');
                        trash.lastClick = null;
                    }
                    divOnclick = this;
                    trash.lastClick = this;
                    $(this).attr('class', 'btn btn-sm btn-danger mybutton');
                });
                $(this).dblclick(function() {
                    if (confirm('确定还原 ' + this.innerHTML + ' ?')) {
                        trash.act('out');
                    }
                });
                document.onkeydown = function(e){
                    e = window.event || e;
                    if(e.keyCode == 46){//del:delete
                        if (clickNull() != true) return;
                        if (confirm("删除后不可恢复，确定删除 " + divOnclick.innerHTML + " ?")) {
                            trash.REALdelete();
                        }
                        return false;
                    }
                }
            });
        },
        REALdelete : function() {
            /******* TODO ******/
            alert(1);
        }
    },
    list = {
        init : function() {
            $('#list').click(function() {
                if (!clickNull()) return ;
                $.post("/combine/getmj", "id=" + divOnclick.id.replace('group', ''), function(data) {
                    if (typeof(data) == "object") {
                        xxx = data;
                        var html_mj = "";
                        var html_exam = "";
                        for(var i = 0; i < data.mjs.length; i ++) {
                            html_mj += "<a href='/article/update?id=" + data.mjs[i].id + "&groupby=1' target='_blank'>" + data.mjs[i].title + "</a>";
                        }
                        for(var i = 0; i < data.exams.length; i ++) {
                            html_exam += "<a href='/exams#/view/" + data.exams[i].id + "' target='_blank'>" + data.exams[i].work_name + "</a>";
                        }
                        $('#mj').html(html_mj);
                        $('#exam').html(html_exam);
                        $('#myModal').modal('show');
                    }
                })
            });
        }
    },
    clickNull = function() {
        if (divOnclick == null) {
            alert("没有选中任何标签!");
            return false;
        }
        else return true;
    },
    addElmt = function (id, classname, text) {
        return "<div " +
                    "id='" + id + "'" + 
                    "draggable='true' " +
                    "class='" + classname + "'>" + 
                    text +
                "</div>&nbsp;"
    },
    init = function(){
        if (typeof(pageName) != "undefined") {
            trash.initList();
            return ;
        }
        
        $('.js-affixed-element-bottom').affix(
            {offset: {}}
        );
        $('#operation').affix(
            {offset: {}}
        );
        $('.js-popover').popover();
        $('#add-btn').popover();
        list.init();

        $('#searchbox').keypress(function(){
            searchBox.search($(this).val(), '');
        });
        $('#searchbox').blur(function() {
            searchBox.isFocus = false;
        });
        $('#searchbox').focus(function() {
            searchBox.isFocus = true;
        });
        $('#rename').blur(function() {
            tags.rename.isFocus = false;
        });
        $('#rename').focus(function() {
            tags.rename.isFocus = true;
        });

        $('#labels div').each(function() {
            var id = this.id.replace(/group/, '');
            tags.tgs[id] = [this.className.match(/btn-([a-z]*) mybutton/)[1], this.innerHTML];
            tags.init(this, id);
        });
        $('#add').click(function() {
            tags.add();
        });
        document.onkeydown = function(e){
            e = window.event || e;
            if(e.keyCode == 46){//del:delete
                if (!clickNull()) return;
                if (confirm("确定删除" + divOnclick.innerHTML + "?")) {
                    trash.act('in');
                }
                return false;
            }
            if(e.keyCode == 83){//s:delt
                if (searchBox.isFocus || tags.rename.isFocus) return ;
                if (!clickNull()) return;
                tags.delt();
                return false;
            }
        }
    };
    return {
        init : init
    };
})(jQuery);

(function() {
    combine.init();
})();
