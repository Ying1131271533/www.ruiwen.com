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
    let D = date.getDate() + ' ';
    // 时分秒
    let H = date.getHours() + ':';
    let m = (date.getMinutes() < 10 ? '0' + (date.getMinutes()) : date.getMinutes()) + ':';
    let s = (date.getSeconds() < 10 ? '0' + (date.getSeconds()) : date.getSeconds());
}

// 读取配置
function config(status) {

    $.ajaxSetup({ async: false });
    let res = null;
    $.getJSON("/static/api/js/status.json", function (data) {
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

// 管理员是否已登录
function isLogin(secret) {
    $.ajax({
        type: "POST",
        contentType: "application/x-www-form-urlencoded",
        url: '/' +secret+'/Admin/isLogin',
        data: "data",
        beforeSend: function (request) {
            request.setRequestHeader("access-token", getToken());
        },
        success: function (res) {
            if (res.status === config('goto')) {
                layer.msg('登录失败！', function () {
                    $.remvoeCookie('admin_login_tokne', {path: '/'});
                    $(window).attr('location', '/'+secret+'loginView');
                });
            }
        }
    });
}