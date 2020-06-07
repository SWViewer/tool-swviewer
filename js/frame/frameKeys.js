document.onkeydown = function(e) {
    if (!e)
        e = window.event;
    var keyCode = e.which || e.keyCode || e.key;
    parent.keyDownFunctOutside(keyCode);
}


var prevScroll = 0;
var dHeight = undefined;
var isDescHidden = false;

const hideDescView = (m) => window.parent.postMessage(m, window.origin);

function setDHeight (e) {
    if (e.origin !== 'https://tools.wmflabs.org') return;
    dHeight = e.data;
    document.getElementById('diffTable').style.paddingTop = (dHeight + 'px');
    document.getElementById('diffTable').style.paddingBottom = (160 + 'px');
}
window.addEventListener('message', setDHeight, false);
hideDescView(undefined);

window.addEventListener('scroll', function scrollDesc() {
    if (document.documentElement.scrollTop > prevScroll &&
        document.documentElement.scrollTop > dHeight) {
        if (!isDescHidden) {
            hideDescView(true);
            isDescHidden = true;
        }
    } else if (isDescHidden) {
        hideDescView(false);
        isDescHidden = false;
    }
    prevScroll = document.documentElement.scrollTop;
});