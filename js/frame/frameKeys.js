document.onkeydown = function(e) {
    if (!e)
        e = window.event;
    var keyCode = e.which || e.keyCode || e.key;
    parent.keyDownFunctOutside(keyCode);
}

function querySelectorAllLive(element, selector) {
    var result = Array.prototype.slice.call(element.querySelectorAll(selector));
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            [].forEach.call(mutation.addedNodes, function(node) {
                if (node.nodeType === Node.ELEMENT_NODE && node.matches(selector)) {
                    result.push(node);
                }
            });
        });
    });
    observer.observe(element, { childList: true, subtree: true });
    return result;
}

var prevScroll = 0;
var dHeight = undefined;
var isDescHidden = false;

const hideDescView = (m) => window.parent.postMessage(m, window.origin);

function setDHeight (e) {
    if (e.origin !== 'https://swviewer.toolforge.org') return;
    dHeight = e.data;
    document.getElementById('diffTable').style.paddingTop = (dHeight + 'px');
    document.getElementById('diffTable').style.paddingBottom = (160 + 'px');
}
window.addEventListener('message', setDHeight, false);
hideDescView(undefined);

// jumps
if (parent.jumps === 1 && parent.diffTextRaw !== "") {
    var m = parent.diffTextRaw.match(/^.*?(\<td class\=\"diff\-addedline|\<td class\=\"diff\-deletedline)/gm);
    if (m && m.length !== 0) {
        var ftag = "." + m[0].replace('  <td class="', '').replace('"', '');
        var al = querySelectorAllLive(document.getElementById("diffTable").getElementsByTagName("tbody")[0], ftag);
        if (al.length !== 0) {
            var marker = document.createElement("swv"); marker.id = "fDiff";
            var div = al[0].getElementsByTagName("div");
            if (div.length !== 0) {
                diffchange = div[0].getElementsByClassName("diffchange");
                if (diffchange.length !== 0) {
                    diffchange[0].prepend(marker);
                } else {
                    div[0].prepend(marker);
                }
            } else {
                al[0].prepend(marker);
            }
            document.getElementById("fDiff").scrollIntoView({ behavior: 'auto', block: 'center', inline: 'start' });
        }
    }
}

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