$(document).ready(function () {
    let url = window.location.href, token = null;
    if (url.search("api") !== -1) {
        token = getApiToken();
    } else {
        token = getToken();
    }
    $.ajaxSetup({
        async: false,
        beforeSend: function (request) {
            request.setRequestHeader("access-token", token);
        },
    });
});

// 获取时间
function time() {
    let tmp = Date.parse(new Date()).toString();
    tmp = tmp.substr(0, 10);
    return tmp;
}

// php的empty()函数
function empty(str) {
    return typeof (str) === "undefined" || str == null || str === "" || str === "NaN";
}

function arrayDuplicate(a, b) {
    let c = [];
    a.forEach(v => {
        if (b.indexOf(v) === -1) {
            c.push(v);
        }
    });
    return c;
}

// 获取url后面的参数
function getParams() {
    var url = location.search;
    url = decodeURI(url);
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        // if (url.indexOf("''") != -1) {
        var str = url.substring(1);
        strs = str.split("&");
        for (var i = 0; i < strs.length; i++) {
            theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
        }
    }
    // url后传递的参数
    // var id = theRequest.id;
    return theRequest;
}

// 获取时间戳
function timeToTimeStamp($time) {
    if (empty($time)) {
        return null;
    }
    let date = new Date($time);
    return Date.parse(date) / 1000;
}

// 时间戳转换成日期
function timestampToTime(timestamp) {
    if (empty(timestamp)) {
        return "缺失时间";
    }
    let date = new Date(timestamp * 1000);
    // 年月日
    let Y = date.getFullYear() + '-';
    let M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
    let D = date.getDate() < 10 ? '0' + date.getDate() + ' ' : date.getDate() + ' ';
    // 时分秒
    let H = date.getHours() + ':';
    let m = (date.getMinutes() < 10 ? '0' + (date.getMinutes()) : date.getMinutes()) + ':';
    let s = (date.getSeconds() < 10 ? '0' + (date.getSeconds()) : date.getSeconds());
    return Y + M + D + H + m + s;
}

// 读取配置
function config(status) {

    $.ajaxSetup({ async: false });
    let res = null;
    $.getJSON("/static/common/js/code.json", function (data) {
        res = data[status];
    });
    return res;
}

// 获取api的token
function getApiToken() {
    return $.cookie('api_login_token');
}

// 获取管理员的token
function getToken() {
    return $.cookie('admin_login_token');
}

// 是否已登录
function isAdminLogin(secret) {
    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded",
        url: '/' + secret + '/Admin/isLogin',
        beforeSend: function (request) {
            request.setRequestHeader("access-token", getToken());
        },
        success: function (res) {
            if (res.code === config('goto')) {
                layer.msg('登录凭证失效！', {}, function () {
                    $.remvoeCookie('admin_login_token', { path: '/' });
                    // $(window).attr('location', '/' + secret + 'loginView');
                });
            }
        }
    });
}

// 用户是否已登录
function isApiLogin() {
    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded",
        url: '/api/User/isLogin',
        beforeSend: function (request) {
            request.setRequestHeader("access-token", getApiToken());
        },
        success: function (res) {
            if (res.code === config('goto')) {
                layer.msg('登录凭证失效！', {}, function () {
                    $.removeCookie('api_login_token', { path: '/' });
                    // $.removeCookie('api_login_token', {domain: document.domain, path: '/'});
                    $(window).attr('location', '/api/View/user/login');
                });
            }
        }
    });
}

// 管理员是否已登录
function isAdminLogin() {
    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded",
        url: '/admin/is_login',
        beforeSend: function (request) {
            request.setRequestHeader("access-token", getToken());
        },
        success: function (res) {
            if (res.code === config('goto')) {
                layer.msg('登录凭证失效！', {}, function () {
                    $.removeCookie('admin_login_token', { path: '/' });
                    // $.removeCookie('api_login_token', {domain: document.domain, path: '/'});
                    $(window).attr('location', '/view/login');
                });
            }
        }
    });
}

// 获取用户
function getUserById(uid) {
    let user = null;
    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded",
        url: '/api/User/getUserById',
        data: { id: uid },
        beforeSend: function (request) {
            request.setRequestHeader("access-token", getApiToken());
        },
        success: function (res) {
            if (res.code === config('goto')) {
                layer.msg('登录凭证失效！', {}, function () {
                    $.removeCookie('api_login_token', { path: '/' });
                    $(window).attr('location', '/api/View/user/login');
                });
            }

            if (res.code === config('failed')) {
                layer.msg(res.msg);
                return false;
            }
            // console.log(res.data);
            user = res.data;
        }
    });
    // 返回用户信息，必须要在ajax外面返回值
    return user;
}

// 获取用户
function getUser() {
    let user = null;
    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded",
        url: '/api/User/getUserByToken',
        beforeSend: function (request) {
            request.setRequestHeader("access-token", getApiToken());
        },
        success: function (res) {
            if (res.code === config('goto')) {
                layer.msg('登录凭证失效！', {}, function () {
                    $.removeCookie('api_login_token', { path: '/' });
                    $(window).attr('location', '/api/View/user/login');
                });
            }

            if (res.code === config('failed')) {
                layer.msg(res.msg);
                return false;
            }
            // console.log(res.data);
            user = res.data;
        }
    });
    // 返回用户信息，必须要在ajax外面返回值
    return user;
}

// 滚动到指定位置 上、中、下
function scrollToEnd(val) {
    var h = null;
    switch (val) {
        case 1:
            h = 0;
            break;
        case 2:
            h = $(document).height() - $(window).height() / 2;
            break;
        case 3:
            h = $(document).height() - $(window).height();
            break;
    }
    $(document).scrollTop(h);
}


/**
 * 改变数据状态，只能是0和1
 *
 * @param  this
 * @return $
 */
function update_field_value(obj) {
    // 数据
    var id = $(obj).attr("data-id"); // id
    var value = $(obj).attr("data-value") == 1 ? 0 : 1; // 要修改的值
    var field = $(obj).attr("data-field"); // 字段名称
    var db = $(obj).attr("data-db"); // 表名

    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded",
        url: '/ajax/update_field_value',
        data: {
            id: id,
            field: field,
            value: value,
            db: db,
        },
        success: function (res) {

            if (res.code == config('failed')) {
                layer.msg(res.msg, { icon: 2 });
                return false;
            }

            if (res.data.value == 1) {
                $(obj).removeClass("layui-btn-danger").attr("data-value", 1).text("开启");
            } else {
                $(obj).addClass("layui-btn-danger").attr("data-value", 0).text("关闭");
            }
        }
    });
}

/**
 * 修改数据字段的值
 *
 * @param  id 数据id
 * @param  field 字段
 * @param  value 要修改的值
 * @param  db 表名
 * @return json
 */
function ajax_field_value(id, field, value, db) {

    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded",
        url: '/ajax/update_field_value',
        data: {
            id: id,
            field: field,
            value: value,
            db: db,
        },
        success: function (res) {

            if (res.code == config('failed')) {
                layer.msg(res.msg, { icon: 2 });
                return false;
            }

            layer.msg('修改成功');
        }
    });
}

/**
 * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
 * @author: 神织知更
 * @time: 2022/04/06 15:57
 *
 * 数据保存
 *
 * @param  obj      from    layui表单实例
 * @param  string 	url		提交的url
 * @param  obj      layer   layer实例
 */
function layui_ajax_save(form, url, layer) {
    form.on('submit(form)', function(data) {
        console.log(data);
        //发异步，把数据提交给php
        $.ajax({
            type: "POST",
            contentType: "application/x-www-form-urlencoded",
            url: url,
            data: data.field,
            success: function (res) {
                if (res.code === config('failed')) {
                    layer.msg(res.msg);
                } else if (res.code === config('success')) {
                    layer.msg("保存成功", { time: 500 }, function() {
                        // 关闭当前frame
                        xadmin.close();
                        // 可以对父窗口进行刷新 
                        xadmin.father_reload();
                    });
                }
            }
        });

        return false;
    });
}


/**
 * @description:  オラ!オラ!オラ!オラ!⎛⎝≥⏝⏝≤⎛⎝
 * @author: 神织知更
 * @time: 2022/04/06 15:57
 *
 * 数据更新
 *
 * @param  obj      from    layui表单实例
 * @param  string 	url		提交的url
 * @param  obj      layer   layer实例
 */
function layui_ajax_update(form, url, layer) {
    form.on('submit(form)', function(data) {
        // console.log(data);
        //发异步，把数据提交给php
        $.ajax({
            type: "PUT",
            contentType: "application/x-www-form-urlencoded",
            url: url,
            data: data.field,
            success: function (res) {
                if (res.code === config('failed')) {
                    layer.msg(res.msg);
                } else if (res.code === config('success')) {
                    layer.msg("保存成功", { time: 500 }, function() {
                        // 关闭当前frame
                        xadmin.close();
                        // 可以对父窗口进行刷新 
                        // xadmin.father_reload();
                    });
                }
            }
        });

        return false;
    });
}