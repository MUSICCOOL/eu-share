/* @author:Romey
 * 动态点赞
 * 此效果包含css3，部分浏览器不兼容（如：IE10以下的版本）
 */
$(function () {
    $("#praise").click(function () {
        var praise_img = $("#praise-img");
        var text_box = $("#add-num");
        var praise_txt = $("#praise-txt");
        var num = parseInt(praise_txt.text().replace(/[^0-9]/ig, ""));

        var pro_id = $(this).attr('pro_id');
        $.ajax({
            type: "post",
            url: '/project/doLike',
            data: {pro_id: pro_id},
            dataType: "json",
            success: function (result) {
                console.log(result);
                if ($.trim(result.code) != 0) {
                    $(this).html("<img src='../../../public/pointlike/like.png' id='praise-img' class='animation' />");
                    praise_txt.addClass("hover");
                    text_box.show().html("<em class='add-animation'>" + result.msg + "</em>");
                    $(".add-animation").addClass("hover");
                } else {
                    $(this).html("<img src='../../../public/pointlike/like.png' id='praise-img' class='animation' />");
                    praise_txt.addClass("hover");
                    text_box.show().html("<em class='add-animation'> 点赞人数+1</em>");
                    $(".add-animation").addClass("hover");
                    num += 1;
                    praise_txt.text('已有' + num + '人点赞');
                }

            },
            error: function (result) {
                if ($.trim(result.code) != 0) {
                    $(this).html("<img src='../../../public/pointlike/like.png' id='praise-img' class='animation' />");
                    praise_txt.addClass("hover");
                    text_box.show().html("<em class='add-animation'>" + result.msg + "</em>");
                    $(".add-animation").addClass("hover");
                } else {
                    $(this).html("<img src='../../../public/pointlike/like.png' id='praise-img' class='animation' />");
                    praise_txt.addClass("hover");
                    text_box.show().html("<em class='add-animation'> 点赞人数+1</em>");
                    $(".add-animation").addClass("hover");
                    num += 1;
                    praise_txt.text('已有' + num + '人点赞');
                }

            }
        })
    });
})