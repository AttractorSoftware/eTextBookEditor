function findString(str, backwards) {
    backwards = backwards || false;
    var strFound;
    if (window.find) {
        strFound = self.find(str, 0, backwards);
        if (!strFound) {
            strFound = self.find(str, 0, !backwards);
            while (self.find(str, 0, !backwards)) continue;
        }
    }
    return;
}
var text = '';
$('#search-button').click(function () {
    text = prompt('Введите текст для поиска');
    if (text != '') findString(text);
    return true;
});


$('#search-next').click(function () {
    if (text != '') findString(text);
    else text = prompt('Введите текст для поиска');
    return true;
});

$('#search-previous').click(function () {
    if (text != '') findString(text, true);
    else text = prompt('Введите текст для поиска');
    return true;
});




