$(function () {

    webUrl = window.location.href;
    var ptag = webUrl.split('/')[4];
    if (ptag != '') {
        $('#' + ptag).addClass("active");
    } else {
        $('#home').addClass("active");
    }

    if (ptag) {
        var ptab = (webUrl.split('/')[4]).split('.')[0];
        if (ptag == 'account') {
            $('#' + ptab).addClass("active");
        } else if (ptab == 'update') {
            $('#' + ptag).addClass("active");
            $('#' + ptag + 's').addClass("active");
        }
    }

    $(".del-btn").click(function () {
        if (!confirm('确定要执行该操作？')) {
            return false;
        }
        var url = $(this).attr('url');
        var back_url = $(this).attr('back_url');
        var id = $(this).attr('id');
        $.ajax({
            type: "GET",
            url: url,
            data: {id: id},
            dataType: "json",
            success: function (data) {
                $("#result-show").removeClass('invisible');
                $("#result-show").addClass('text-primary');
                $("#result-show").html(data.msg);
                setTimeout(function () {
                    $("#result-show").addClass('invisible');
                    location.href = back_url;
                }, 2000)
            },
            error: function (data) {
                $("#result-show").removeClass('invisible');
                $("#result-show").addClass('text-danger');
                $("#result-show").html(data.msg);
                setTimeout(function () {
                    $("#result-show").addClass('invisible');
                    location.href = back_url;
                }, 2000)
            }
        });
    });

    $(".menu a").each(function () {
        $this = $(this);
        if ($this[0].href == String(window.location)) {
            $this.parent().addClass("active");
        }
    });
    $(".menu a").on("click", function () {
        $(".menu").find(".active").removeClass("active");
        $(this).parent().addClass("active");
    });

    $(".side-left a").each(function () {
        $this = $(this);
        if ($this[0].href == String(window.location)) {
            $this.parent().addClass("active");
        }
    });
    $(".side-left a").on("click", function () {
        $(".menu").find(".active").removeClass("active");
        $(this).parent().addClass("active");
    });

    $("#reg-btn").click(function () {
        $("#reg-msg").html($.trim(''));

        var url = $("#register-form").attr('action-url');
        var jump_url = $("#register-form").attr('jump-url');
        var username = $("#reg-username").val();
        var nickname = $("#reg-nickname").val();
        var password = $("#reg-pwd").val();
        var password_confirm = $("#reg-pwd-confirm").val();
        var email = $("#reg-email").val();
        var sex = '';
        var radio = document.getElementsByName("sex");
        for (i = 0; i < radio.length; i++) {
            if (radio[i].checked) {
                sex = radio[i].value;
            }
        }

        if (username == '' || nickname == '' || password == '' || password_confirm == '' || sex == '') {
            alert('信息未填写完整，请检查后重试！');
            return false;
        }

        $.ajax({
            type: "post",
            url: url,
            data: {
                username: username,
                nickname: nickname,
                password: password,
                password_confirm: password_confirm,
                email: email,
                sex: sex
            },
            dataType: "json",
            success: function (result) {
                if ($.trim(result.code) != 0) {
                    $("#reg-msg").html($.trim(result.msg));
                } else {
                    location.href = jump_url;
                }
            }
        })
    });

    $("#log-btn").click(function () {
        $("#log-msg").html($.trim(''));

        var url = $(this).attr('action-url');
        var jump_url = $(this).attr('jump-url');
        var username = $("#log-uname").val();
        var password = $("#log-pwd").val();
        $.ajax({
            type: "post",
            url: url,
            data: {username: username, password: password},
            dataType: "json",
            success: function (result) {
                if ($.trim(result.code) != 0) {
                    $("#log-msg").html($.trim(result.msg));
                } else {
                    location.href = jump_url;
                }
            }
        })
    });

    $(".img-up-btn").change(function (e) {
            var imgBox = e.target;
            uploadImg($('.img-pre'), imgBox)
        }
    );

    function uploadImg(element, tag) {
        var file = tag.files[0];
        var imgSrc;
        if (!/image\/\w+/.test(file.type)) {
            alert("看清楚，头像可是需要图片格式的哦！");
            return false;
        }
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function () {
            imgSrc = this.result;
            $(".img-pre-show").attr("src", imgSrc);
            $(".img-pre").removeClass('invisible');
        };
    }

    $(".img-up-btn-zfb").change(function (e) {
            var imgBox = e.target;
            uploadImgZfb($('.img-pre-zfb'), imgBox)
        }
    );

    function uploadImgZfb(element, tag) {
        var file = tag.files[0];
        var imgSrc;
        if (!/image\/\w+/.test(file.type)) {
            alert("看清楚，头像可是需要图片格式的哦！");
            return false;
        }
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function () {
            imgSrc = this.result;
            $(".img-pre-show-zfb").attr("src", imgSrc);
            $(".img-pre-zfb").removeClass('invisible');
        };
    }

    $(".img-up-btn-wx").change(function (e) {
            var imgBox = e.target;
            uploadImgWx($('.img-pre-wx'), imgBox)
        }
    );

    function uploadImgWx(element, tag) {
        var file = tag.files[0];
        var imgSrc;
        if (!/image\/\w+/.test(file.type)) {
            alert("看清楚，头像可是需要图片格式的哦！");
            return false;
        }
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function () {
            imgSrc = this.result;
            $(".img-pre-show-wx").attr("src", imgSrc);
            $(".img-pre-wx").removeClass('invisible');
        };
    }

    $("#pro_create").click(function (e) {
        var name = $('#name').val();
        var intro = $('#intro').val();
        var type = $('#type').val();
        var need_points = $('#need_points').val();
        var desc = $('#desc').val();
        var file_url = $('#file_url').val();

        if (name == '') {
            alert('项目名称不能为空！');
            return false;
        } else if (name.length > 20) {
            alert('项目名称超过最大长度！');
            return false;
        }

        if (intro == '') {
            alert('项目简介不能为空！');
            return false;
        } else if (intro.length > 100) {
            alert('项目简介超过最大长度！');
            return false;
        }

        if (type == '') {
            alert('请选择项目类型！');
            return false;
        }

        if (file_url == '') {
            alert('作品源文件还没有上传(或填写网络地址)！');
            return false;
        } else if (file_url.length > 255) {
            alert('文件地址超过最大长度');
            return false;
        }

        var imgs_urls = $('#imgs_urls').val();

        var url = $("#pro-add-form").attr('action-url');
        var back_url = $("#pro-add-form").attr('jump-back-url');
        $.ajax({
            type: "post",
            url: url,
            data: {
                name: name,
                intro: intro,
                type: type,
                need_points: need_points,
                desc: desc,
                imgs_urls: imgs_urls,
                file_url: file_url
            },
            dataType: "json",
            success: function (result) {
                if ($.trim(result.code) != 0) {
                    alert('创建项目失败:' + result.msg);
                    return false;
                } else {
                    location.href = back_url;
                }
            }
        })
    });

    $("#pro_create_update").click(function (e) {
        var name = $('#name').val();
        var intro = $('#intro').val();
        var type = $('#type').val();
        var need_points = $('#need_points').val();
        var desc = $('#desc').val();
        var file_url = $('#file_url').val();

        if (name == '') {
            alert('项目名称不能为空！');
            return false;
        } else if (name.length > 20) {
            alert('项目名称超过最大长度！');
            return false;
        }

        if (intro == '') {
            alert('项目简介不能为空！');
            return false;
        } else if (intro.length > 100) {
            alert('项目简介超过最大长度！');
            return false;
        }

        if (type == '') {
            alert('请选择项目类型！');
            return false;
        }

        if (file_url == '') {
            alert('作品源文件还没有上传(或填写网络地址)！');
            return false;
        } else if (file_url.length > 255) {
            alert('文件地址超过最大长度');
            return false;
        }

        var imgs_urls = $('#imgs_urls').val();

        var pro_id = $("#pro-add-form").attr('pro_id');
        var url = $("#pro-add-form").attr('action-url');
        var back_url = $("#pro-add-form").attr('jump-back-url');
        $.ajax({
            type: "post",
            url: url,
            data: {
                pro_id: pro_id,
                name: name,
                intro: intro,
                type: type,
                need_points: need_points,
                desc: desc,
                imgs_urls: imgs_urls,
                file_url: file_url
            },
            dataType: "json",
            success: function (result) {
                if ($.trim(result.code) != 0) {
                    alert('修改项目失败:' + result.msg);
                    return false;
                } else {
                    location.href = back_url;
                }
            }
        })
    });

    $("#art_create").click(function (e) {
        var title = $('#title').val();
        var keywords = $('#keywords').val();
        var intro = $('#intro').val();
        var content = $('#content').val();

        if (title == '') {
            alert('亲！文章名称不能为空哦！');
            return false;
        }

        if (keywords == '') {
            alert('亲！文章关键词不能为空哦！');
            return false;
        }

        var url = $("#art-add-form").attr('action-url');
        var back_url = $("#art-add-form").attr('jump-back-url');
        $.ajax({
            type: "post",
            url: url,
            data: {title: title, keywords: keywords, intro: intro, content: content},
            dataType: "json",
            success: function (result) {
                if ($.trim(result.code) != 0) {
                    alert('文章发布失败:' + result.msg);
                    return false;
                } else {
                    location.href = back_url;
                }
            }
        })
    });

    $("#art_create_update").click(function (e) {
        var title = $('#title').val();
        var keywords = $('#keywords').val();
        var intro = $('#intro').val();
        var content = $('#content').val();

        if (title == '') {
            alert('亲！文章名称不能为空哦！');
            return false;
        }

        if (keywords == '') {
            alert('亲！文章关键词不能为空哦！');
            return false;
        }

        var url = $("#art-update-form").attr('action-url');
        var back_url = $("#art-update-form").attr('jump-back-url');
        var art_id = $("#art-update-form").attr('art_id');
        $.ajax({
            type: "post",
            url: url,
            data: {art_id: art_id, title: title, keywords: keywords, intro: intro, content: content},
            dataType: "json",
            success: function (result) {
                if ($.trim(result.code) != 0) {
                    alert('文章更新失败:' + result.msg);
                    return false;
                } else {
                    location.href = back_url;
                }
            }
        })
    });

    $(".down-btn").click(function () {
        var url = $(this).attr('action-url');
        var pro_id = $(this).attr('pro_id');

        $.ajax({
            type: "post",
            url: url,
            data: {pro_id: pro_id},
            dataType: "json",
            success: function (result) {
                console.log(result);
                if ($.trim(result.code) != 0) {
                    $("#message").html("文件下载失败，请稍后重试！");
                } else {
                    $("#message").html('点击链接下载：<a class="text-primary" href="' + result.file + '">下载</a>&nbsp;&nbsp;<small><i>(&nbsp;点击右键另存为&nbsp;)</i></small>');
                }
            }
        })
    });

    $(".pro-img-del").click(function () {
        this.parentNode.parentNode.removeChild(this.parentElement);
        ClearfirtsImg();
    });

    // textarea 限制输入字数
    $("#intro").focus(function () {
        $(".d-last-intro").show();
        $("#intro").css("borderColor", "#B4E6FA");
    }).blur(function () {
        $(".d-last-intro").hide();
        $("#intro").css("borderColor", "#A9A9A9");
    }).keyup(function () {
        var c = $(this).val();
        if (c.length > 100) {
            c = c.substr(0, 100);
            $(this).val(c);
        }
        var last = 100 - c.length;
        $(".d-last-intro").text("还能输入" + last + "字");
    });

    $("#connect").focus(function () {
        $(".d-last-connect").show();
        $("#connect").css("borderColor", "#B4E6FA");
    }).blur(function () {
        $(".d-last-connect").hide();
        $("#connect").css("borderColor", "#A9A9A9");
    }).keyup(function () {
        var c = $(this).val();
        if (c.length > 500) {
            c = c.substr(0, 500);
            $(this).val(c);
        }
        var last = 500 - c.length;
        $(".d-last-connect").text("还能输入" + last + "字");
    });

    $("#send-box").focus(function () {
        $(".d-last-comment").show();
        $("#send-box").css("borderColor", "#B4E6FA");
    }).blur(function () {
        $(".d-last-comment").hide();
        $("#sned-box").css("borderColor", "#A9A9A9");
    }).keyup(function () {
        var c = $(this).val();
        if (c.length > 100) {
            c = c.substr(0, 100);
            $(this).val(c);
        }
        var last = 100 - c.length;
        $(".d-last-comment").text("还能输入" + last + "字");
    });

    // pay相关
    $(".vip-click").click(function (e) {
        $($(this).siblings()).removeClass('vip-bg');
        $(this).addClass('vip-bg');
        $("#doup").attr('vip-type', $(this).attr('vip-type'));
        $("#doup-btn").attr('data-target', '.bs-example-modal-lg' + $(this).attr('vip-type'));
    });

    $(".upvip").click(function () {
        var t10 = $("#order-type-10").val();
        var t20 = $("#order-type-20").val();
        var t50 = $("#order-type-50").val();
        var t100 = $("#order-type-100").val();
        var type = 0;
        var order_num = 0;
        if (t10 != '') {
            type = 0;
            order_num = t10;
        } else if (t20 != '') {
            type = 1;
            order_num = t20;
        } else if (t50 != '') {
            type = 2;
            order_num = t50;
        } else if (t100 != '') {
            type = 3;
            order_num = t100;
        }
        var amount = $("#doup").attr('vip-type');
        var url = $("#doup").attr('url');
        var re = /^\d{6}$/;
        if (!re.test(order_num)) {
            alert("请输入转账单号的后6位！");
            return false;
        } else {
            $.ajax({
                type: "post",
                url: url,
                data: {order_num: order_num, amount: amount, type: type},
                dataType: "json",
                success: function (result) {
                    console.log(result);
                    alert(result.msg);
                }
            })
        }
    });

    $(".rec-click").click(function (e) {
        $($(this).siblings()).removeClass('vip-bg');
        $(this).addClass('vip-bg');
        $("#doup").attr('rec-type', $(this).attr('rec-type'));
        $("#doup-btn").attr('data-target', '.bs-example-modal-lg' + $(this).attr('rec-type'));
    });

    $(".uprec").click(function () {
        var t10 = $("#order-type-10").val();
        var t20 = $("#order-type-20").val();
        var t50 = $("#order-type-50").val();
        var t100 = $("#order-type-100").val();
        var type = 0;
        var order_num = 0;
        if (t10 != '') {
            type = 4;
            order_num = t10;
        } else if (t20 != '') {
            type = 5;
            order_num = t20;
        } else if (t50 != '') {
            type = 6;
            order_num = t50;
        } else if (t100 != '') {
            type = 7;
            order_num = t100;
        }
        var amount = $("#doup").attr('rec-type');
        var url = $("#doup").attr('url');
        var re = /^\d{6}$/;
        if (!re.test(order_num)) {
            alert("请输入转账单号的后6位！");
            return false;
        } else {
            $.ajax({
                type: "post",
                url: url,
                data: {order_num: order_num, amount: amount, type: type},
                dataType: "json",
                success: function (result) {
                    console.log(result);
                    alert(result.msg);
                }
            })
        }
    });

    $(".reward-click").click(function (e) {
        $($(this).siblings()).find("input").removeAttr("checked");
        $($(this).siblings()).removeClass('reward-bg');

        $(this).find("input").attr("checked", true);
        $(this).addClass('reward-bg');

        $("#reward").attr('e_points', $(this).find("input").attr("value"));
    });

    $("#reward").click(function () {
        if (!confirm('确定打赏该项目？')) {
            return false;
        }
        var url = $(this).attr('action-url');
        var pro_id = $(this).attr('pro_id');
        var u_id = $(this).attr('u_id');
        var e_points = $(this).attr('e_points');

        $.ajax({
            type: "post",
            url: url,
            data: {pro_id: pro_id, e_points: e_points, u_id: u_id},
            dataType: "json",
            success: function (result) {
                console.log(result);
                alert(result.msg);
            }
        })
    });


});

function bindEntry() {
    if (event.keyCode == 13) {
        $("#log-btn").click();
    }
}

// ajax 文件上传，带进度条
var uploadUrl = $("#oFile").attr('action-url');

//文件选择完毕时
function FileChangeFn(event) {
    $('.send_btn').show();
    var event = event || window.event,
        dom = '',
        ofile = $("#oFile").get(0).files[0],
        otype = ofile.type,
        ourl = window.URL.createObjectURL(ofile); //文件临时地址

    //osize = ofile.size / 1054000,
    //$('#file_type').text("选择上传文件类型：" + otype);
    //$('#file_size').text("选择上传文件大小，共" + osize.toFixed(2) + "MB。");

    //console.log(ofile);
    //console.log("文件类型：" + otype); //文件类型
    //console.log("文件大小：" + osize); //文件大小
};

//侦查附件上传情况 ,这个方法大概0.05-0.1秒执行一次
function OnProgRess(event) {
    var event = event || window.event;
    //console.log(event);  //事件对象
    //console.log("已经上传：" + event.loaded); //已经上传大小情况(已上传大小，上传完毕后就 等于 附件总大小)
    //console.log(event.total);  //附件总大小(固定不变)
    var loaded = Math.floor(100 * (event.loaded / event.total)); //已经上传的百分比
    $("#speed").html(loaded + "%").css("width", loaded + "%");
};

var file_up_retry_num = 3;

//开始上传文件
function UploadFileFn() {
    var uploadUrl = $("#oFile").attr('action-url');
    $('.speed_box').show();
    var oFile = $("#oFile").get(0).files[0], //input file标签
        formData = new FormData(); //创建FormData对象
    xhr = $.ajaxSettings.xhr(); //创建并返回XMLHttpRequest对象的回调函数(jQuery中$.ajax中的方法)
    formData.append("pro_file", oFile); //将上传name属性名(注意：一定要和 file元素中的name名相同)，和file元素追加到FormData对象中去
    $.ajax({
        type: "POST",
        url: uploadUrl, // 后端服务器上传地址
        data: formData, // formData数据
        cache: true, // 是否缓存
        async: true, // 是否异步执行
        processData: false, // 是否处理发送的数据  (必须false才会避开jQuery对 formdata 的默认处理)
        contentType: false, // 是否设置Content-Type请求头
        xhr: function () {
            if (OnProgRess && xhr.upload) {
                xhr.upload.addEventListener("progress", OnProgRess, false);
                return xhr;
            }
        },
        success: function (returndata) {
            if (returndata.code != 0) {
                if (file_up_retry_num > 0) {
                    file_up_retry_num = file_up_retry_num - 1;
                    xhr.upload.addEventListener("progress", OnProgRess, false);
                    return xhr;
                } else {
                    $("#speed").html("上传失败:" + returndata.msg);
                }
            } else {
                $("#speed").html("上传成功");
                $("#file_url").val(returndata.url);
            }
            // console.log(returndata);
            //alert(returndata);
        },
        error: function (returndata) {
            if (file_up_retry_num > 0) {
                file_up_retry_num = file_up_retry_num - 1;
                xhr.upload.addEventListener("progress", OnProgRess, false);
                return xhr;
            } else {
                $("#speed").html("上传失败:" + returndata.msg);
            }
            //console.log(returndata)
            //alert('上传失败！');
        }
    });
};