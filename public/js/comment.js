$(function () {
    $("#send-btn").click(function () {
        $("#send-msg").html($.trim(''));

        var url = $("#send-comment").attr('action-url');
        var pro_id = $("#send-comment").attr('pro_id');
        var comment = $("#send-box").val();
        if (comment == '') {
            alert("亲！评论内容不能为空哦！");
            return false;
        }

        if (comment.length > 100) {
            alert("亲！评论的字符太多了哦！");
            return false;
        }

        $.ajax({
            type: "post",
            url: url,
            data: {pro_id: pro_id, comment: comment},
            dataType: "json",
            success: function (result) {
                if ($.trim(result.code) != 0) {
                    $("#send-msg").html($.trim(result.msg));
                } else {
                    $("#send-box").val('');
                    $("#com-list-show").html('');
                    $("#com-list").attr('page', 1);
                    getComments();
                }
            }
        })
    });

    getComments();

    $("#get-btn").click(function () {
        getComments();
    });


    function getComments() {
        $("#get-msg").html($.trim(''));
        $("#com-list").attr('page');

        var url = $("#com-list").attr('action-url');
        var pro_id = $("#com-list").attr('pro_id');
        var page = $("#com-list").attr('page');

        $.ajax({
            type: "get",
            url: url,
            data: {pro_id: pro_id, page: page},
            dataType: "json",
            success: function (result) {
                if ($.trim(result.code) != 0) {
                    $("#get-msg").html($.trim(result.msg));
                } else {
                    var html = '';
                    if (result.comments.length > 0) {
                        for (var i = 0; i < result.comments.length; i++) {
                            html += '<div class="each-comment">';
                            html += '<div class="each-comment-left pull-left text-center">';
                            html += '<img class="img-circle" src="' + result.comments[i].user.avatar + '" width="40px" height="40px" />';
                            html += '</div>';
                            html += '<div class="each-comment-right pull-left">';
                            html += '<p class="text-primary">' + result.comments[i].user.nickname + '<small>发表了评论 ( ' + result.comments[i].created_at + ' ) </small></p>';
                            html += '<p class="comment-cont">' + result.comments[i].comment + '</p>';
                            html += '</div>';
                            html += '</div>';
                            html += '<div class="clearfix"></div>';
                            html += '<hr />';
                        }
                        $("#com-list").attr('page', parseInt(page) + 1);
                        $("#get-btn").removeClass('invisible');
                    } else {
                        var html = '<p class="text-center">没有更多</p>';
                        $("#get-btn").addClass('invisible');
                    }
                    $("#com-list-show").append(html);
                }
            }
        })
    }
});

