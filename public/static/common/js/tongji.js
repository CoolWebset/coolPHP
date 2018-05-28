var zhiwenma = {},
  detectOS, init, myBrowser, ajaxpost;
detectOS = function() {
  var sUserAgent = navigator.userAgent;
  var isWin = (navigator.platform == "Win32") || (navigator.platform == "Windows");
  var isMac = (navigator.platform == "Mac68K") || (navigator.platform == "MacPPC") || (navigator.platform == "Macintosh") || (navigator.platform == "MacIntel");
  if (isMac) return "Mac";
  var isUnix = (navigator.platform == "X11") && !isWin && !isMac;
  if (isUnix) return "Unix";
  var isLinux = (String(navigator.platform).indexOf("Linux") > -1);
  if (isLinux) return "Linux";
  if (isWin) {
    var isWin2K = sUserAgent.indexOf("Windows NT 5.0") > -1 || sUserAgent.indexOf("Windows 2000") > -1;
    if (isWin2K) return "Win2000";
    var isWinXP = sUserAgent.indexOf("Windows NT 5.1") > -1 || sUserAgent.indexOf("Windows XP") > -1;
    if (isWinXP) return "WinXP";
    var isWin2003 = sUserAgent.indexOf("Windows NT 5.2") > -1 || sUserAgent.indexOf("Windows 2003") > -1;
    if (isWin2003) return "Win2003";
    var isWinVista = sUserAgent.indexOf("Windows NT 6.0") > -1 || sUserAgent.indexOf("Windows Vista") > -1;
    if (isWinVista) return "WinVista";
    var isWin7 = sUserAgent.indexOf("Windows NT 6.1") > -1 || sUserAgent.indexOf("Windows 7") > -1;
    if (isWin7) return "Win7";
    var isWin10 = sUserAgent.indexOf("Windows NT 10") > -1 || sUserAgent.indexOf("Windows 10") > -1;
    if (isWin10) return "Win10"
  }
  return "other"
}
myBrowser = function() {
  var Sys = {};
  var ua = navigator.userAgent.toLowerCase();
  var s;
  (s = ua.match(/rv:([\d.]+)\) like gecko/)) ? Sys.ie = s[1]: (s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] : (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] : (s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] : (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] : (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;
  if (Sys.ie) return 'ie' + Sys.ie;
  if (Sys.firefox) return 'firefox' + Sys.firefox;
  if (Sys.chrome) return 'chrome' + Sys.chrome;
  if (Sys.opera) return 'opera' + Sys.opera;
  if (Sys.safari) return 'safari' + Sys.safari
}
ajaxpost = function(url, url2) {
  var xmlhttp, xmlhttp1;
  if (window.XMLHttpRequest) {
    xmlhttp = new XMLHttpRequest();
    xmlhttp1 = new XMLHttpRequest()
  } else {
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    xmlhttp1 = new ActiveXObject("Microsoft.XMLHTTP")
  }
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      var obj = eval('(' + xmlhttp.responseText + ')');
      xmlhttp1.open("post", url2, true);
      xmlhttp1.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xmlhttp1.send('antepage=' + obj.referrer + '&visitpage=' + obj.url)
    }
  }
  xmlhttp.open("post", url, true);
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlhttp.send('screen_height=' + zhiwenma.screen_height + '&screen_width=' + zhiwenma.screen_width + '&os=' + zhiwenma.os + '&browser=' + zhiwenma.browser + '&referrer=' + document.referrer + '&url=' + window.location.href)
}
init = (function() {
  zhiwenma.screen_height = window.screen.height;
  zhiwenma.screen_width = window.screen.width;
  zhiwenma.os = detectOS();
  zhiwenma.browser = myBrowser();
  ajaxpost('/datastatistics', '/datastatistics/dostatistics')
})();