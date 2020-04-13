var SiteUrl = "http://"+window.location.host;
var ApiUrl = "http://"+window.location.host+"/api/mobile";
var pagesize = 10;
var WapSiteUrl = "http://"+window.location.host+"/mobile";
var IOSSiteUrl = "http://"+window.location.host+"/app.ipa";
var AndroidSiteUrl = "http://"+window.location.host+"/app.apk";

// auto url detection
(function() {
    var m = /^(https?:\/\/.+)\/mobile/i.exec(location.href);
    if (m && m.length > 1) {
        SiteUrl = m[1];
        ApiUrl = m[1] + '/api/mobile';
        WapSiteUrl = m[1] + '/mobile';
    }
})();
