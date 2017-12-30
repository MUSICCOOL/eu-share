$(function () {
    $(".del-btn").click(function () {
        if (!confirm('确定要执行该操作？')) {
            return false;
        }
        var url = $(this).attr('url');
        var back_url = $(this).attr('back_url');
        var id = $(this).attr('id');
        $.ajax({
            type: "post",
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

    $(".setread-all").click(function () {
        var ids = "";
        $("[name = list-checked]:checked").each(function () {
            ids += ',' + $(this).val();
        });

        if (ids == "") {
            alert('请选择要设置已读的记录!');
            return false;
        }

        if (!confirm('确定要执行该操作？')) {
            return false;
        }
        var url = $(this).attr('url');
        var back_url = $(this).attr('back_url');


        $.ajax({
            type: "post",
            url: url,
            data: {id: ids},
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

    $(".select-all").click(function () {
        $("[name = list-checked]:checkbox").attr("checked", true);
    });
    $(".cancel-all").click(function () {
        $("[name = list-checked]:checkbox").attr("checked", false);
    });
    $(".del-all").click(function () {
        var ids = "";
        $("[name = list-checked]:checked").each(function () {
            ids += ',' + $(this).val();
        });

        if (ids == "") {
            alert('请选择要删除的记录!');
            return false;
        }

        if (!confirm('确定要执行该操作？')) {
            return false;
        }
        var url = $(this).attr('url');
        var back_url = $(this).attr('back_url');


        $.ajax({
            type: "GET",
            url: url,
            data: {id: ids},
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
});
