var version = new Date().getTime();
var addCss = function (links) {
    if (links instanceof Array) {
        for (var i = 0; i < links.length; i++) {
            document.write('<link type="text/css" rel="stylesheet" href="' + links[i] + '?v=' + version + '">');
        }
    } else {
        document.write('<link type="text/css" rel="stylesheet" href="' + links + '?v=' + version + '">');
    }
}
var addJs = function (links) {
    if (links instanceof Array) {
        for (var i = 0; i < links.length; i++) {
            document.write('<script type="text/javascript" src="' + links[i] + '?v=' + version + '"></script>');
        }
    } else {
        document.write('<script type="text/javascript" src="' + links + '?v=' + version + '"></script>');
    }
}