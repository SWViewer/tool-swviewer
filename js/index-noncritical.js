function copyToClipboard (text) {
    const host = document.getElementById('controlsBase');
    const tempTextArea = document.createElement('input');
    tempTextArea.value = text;
    host.append(tempTextArea);
    tempTextArea.select();
    tempTextArea.setSelectionRange(0, 99999);
    document.execCommand('Copy');
    tempTextArea.remove();
}
/*----presets----*/
function togglePresets() {
    const presetsArrow = document.getElementById('presetsArrow');
    const presetBody = document.getElementById('presetBody');
    if (presetsArrow.classList.contains('presets__arrow-flip')) {
        presetsArrow.classList.remove('presets__arrow-flip');
        presetBody.style.height = '0';
    } else {
        presetsArrow.classList.add('presets__arrow-flip');
        presetBody.style.height = '';
    }
}
/*###################
---PW and PO Module---
#####################*/
function removeTabNotice(tab) {
    document.getElementById(tab).childNodes.forEach(el => {
        if (el.classList === undefined) return;
        if (el.classList.contains('loading-tab')) el.parentNode.removeChild(el);
    })
}
var lastOpenedPW = undefined;
function toggleTab (oldTab, newTab) {
    const close = (tab) => {
        if (tab === undefined) document.getElementById('btn-home').classList.remove('tab__active');
        else if (tab === 'talkForm') document.getElementById('btn-talk').classList.remove('tab__active');
        else if (tab === 'logs') document.getElementById('btn-logs').classList.remove('tab__active');
    }
    const open = (tab) => {
        if (tab === undefined) document.getElementById('btn-home').classList.add('tab__active');
        else if (tab === 'talkForm') document.getElementById('btn-talk').classList.add('tab__active');
        else if (tab === 'logs') document.getElementById('btn-logs').classList.add('tab__active');
    }
    close(oldTab);
    open(newTab);
}
function openPW (pw) {
    function openPWLocal() {
        if (pw !== lastOpenedPW) {
            if (lastOpenedPW !== undefined) closePW(true);
            toggleTab(lastOpenedPW, pw);
            lastOpenedPW = pw;
            document.getElementById(pw).style.display = 'grid';
            closeSidebar();

            if (pw === 'talkForm') onTalkOpen();
        }
    }
    if(document.getElementById(pw) === null) {
        if (pw === "logs") $.getScript('https://swviewer.toolforge.org/js/modules/logs.js', () => removeTabNotice('btn-logs'));
        if (pw === "talkForm") $.getScript('https://swviewer.toolforge.org/js/modules/talk.js', () => removeTabNotice('btn-talk'));
            
        if (document.getElementById(pw) !== null) openPWLocal();
    } else openPWLocal();
}
function closePW (dontToggle) {
    if (lastOpenedPW !== undefined) {
        document.getElementById(lastOpenedPW).style.display = 'none';
        if (!dontToggle) {
            toggleTab(lastOpenedPW, undefined);
            lastOpenedPW = undefined;
            closeSidebar();
        }
    }
}
function openPWDrawer (drawer, overlay) {
    document.getElementById(drawer).classList.add('pw__drawer__active');
    document.getElementById(overlay).classList.add('pw__overlay__active');
}
function closePWDrawer (drawer, overlay) {
    document.getElementById(drawer).classList.remove('pw__drawer__active');
    document.getElementById(overlay).classList.remove('pw__overlay__active');
}

var lastOpenedPO = undefined;
function openPO (po) {
    function openPOLocal () {
        document.getElementById(po).style.display = 'grid';
        setTimeout(() => {
            document.getElementById(po).classList.add('po__active');
            document.getElementById('POOverlay').classList.add('po__overlay__active');
        }, 100);
        lastOpenedPO = po;

        const notifyIndicator = document.getElementById('notify-indicator');
        if (po === 'notificationPanel' && !notifyIndicator.classList.contains('tab-notice-indicator__inactive')) notifyIndicator.classList.add('tab-notice-indicator__inactive');
    }

    if (document.getElementById(po) === null) {
        const poParent = document.getElementById('angularapp');
        if (po === "about") $.getScript('https://swviewer.toolforge.org/js/modules/about.js', () => removeTabNotice('btn-about'));
        if (document.getElementById(po) !== null) openPOLocal();
    } else openPOLocal();
}
function closePO () {
    closeSettingsSend();
    if (lastOpenedPO !== undefined) {
        document.getElementById(lastOpenedPO).classList.remove('po__active');
        document.getElementById('POOverlay').classList.remove('po__overlay__active');
        setTimeout(() => {
            document.getElementById(lastOpenedPO).style.display = 'none';
        }, 200);
    }
}
function clickHome() {
    if (lastOpenedPW === undefined && firstClickEdit !== false)
        homeBtn(true);
}
function homeBtn(mod) {
    if (document.getElementById('page-welcome').style.display === "none" && mod !== false) {
        document.getElementById('description-container').style.display = "none";
        document.getElementById('moreControlOverlay').style.display = "none";
        document.getElementById('controlsBase').style.display = "none";
        document.getElementById('page-welcome').style.display = "block";
        document.getElementById('page').style.display = "none";
        document.getElementById('moreOptionBtnMobile').classList.add('disabled');
    } else {
        document.getElementById('description-container').style.display = "grid";
        document.getElementById('moreControlOverlay').style.display = "unset";
        document.getElementById('controlsBase').style.display = "block";
        document.getElementById('page-welcome').style.display = "none";
        document.getElementById('page').style.display = "block";
        document.getElementById('moreOptionBtnMobile').classList.remove('disabled');
    }
}

/*#########################
--------- talk -------
#########################*/

function onTalkOpen () {
    scrollToBottom("talk-content");
    if (document.getElementById('badge-talk').style.background !== "var(--bc-negative)") {
        document.getElementById('badge-talk').style.display = "none";
        document.getElementById('badge-talk').textContent = "0";
    }
}

$(window).resize(function() {
    scrollToBottom("talk-content");
});

/*######################
------- Settings -------
######################*/

/*-----Global settings----*/


function closeSettingsSend() {
    var checkSendSettings = false;
    if (document.getElementById("settingsOverlay").classList.contains('po__active')) {

        if ( (typeof document.getElementById('max-queue').value == "undefined") ||
            document.getElementById('max-queue').value == null ||
            document.getElementById('max-queue').value == "") {
                if (Number(countqueue) !== 0) {
                    countqueue = 0;
                    checkSendSettings = true;
                }
        }
        if (document.getElementById('max-queue').value.match(/^\d+$/)) {
            if (Number(countqueue) !== parseInt(document.getElementById('max-queue').value)) {
                countqueue = parseInt(document.getElementById('max-queue').value);
                checkSendSettings = true;
                if (Number(countqueue) !== 0)
                    angular.element(document.getElementById("angularapp")).scope().removeLast();
            }
        }

        if (Number(countqueue) == 0)
            document.getElementById('max-queue').value = "";
        else
            document.getElementById('max-queue').value = countqueue;

        $.ajax({url: 'https://swviewer.toolforge.org/php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'numbers',
            countqueue: countqueue 
        }, dataType: 'json'});
    }
};

document.getElementById('themeSelector').onchange = function() {
    window.themeIndex = document.getElementById("themeSelector").selectedIndex;
    if (window.themeIndex === 4) {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) changeTheme(2);
        else changeTheme(0);
    } else changeTheme(window.themeIndex);

    $.ajax({url: 'https://swviewer.toolforge.org/php/settings.php', type: 'POST', crossDomain: true, data: {
        action: 'set',
        query: 'theme',
        limit: Object.keys(THEME),
        theme: document.getElementById("themeSelector").selectedIndex
    }, dataType: 'json'});
};

function bottomUp (button) {
        var sendDirection;
        if (button.classList.contains('t-btn__active')) {
            document.getElementById("queue").setAttribute("style", "display:flex; flex-direction:column-reverse");
            sendDirection = 1;
        }
        else {
            document.getElementById("queue").removeAttribute("style");
            sendDirection = 0;
        }
        $.ajax({url: 'https://swviewer.toolforge.org/php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'direction',
            direction: sendDirection
        }, dataType: 'json'});
};

document.getElementById("soundSelector").onchange = function() {
    sound = Number(document.getElementById("soundSelector").value);
    $.ajax({url: 'https://swviewer.toolforge.org/php/settings.php', type: 'POST', crossDomain: true, data: {
        action: 'set',
        query: 'sound',
        sound: sound
    }, dataType: 'json'});
};

document.getElementById("checkSelector").onchange = function() {
    checkMode = Number(document.getElementById("checkSelector").value);
    $.ajax({url: 'https://swviewer.toolforge.org/php/settings.php', type: 'POST', crossDomain: true, data: {
        action: 'set',
        query: 'checkmode',
        checkmode: checkMode
    }, dataType: 'json'});
};

function RHModeBtn (button, start) {
    var rhmode;
    var sidebarOptions = document.getElementsByClassName('sidebar__options')[0];
    var tooltipSide = "";
    if (button.classList.contains('t-btn__active')) {
        document.getElementById('baseGrid').classList.add('base-grid__RH-mode');
        rhmode = 1; tooltipSide = 'left';
    } else {
        document.getElementById('baseGrid').classList.remove('base-grid__RH-mode');
        rhmode = 0; tooltipSide = 'right';
    }
    for (let i = 1; i < sidebarOptions.childNodes.length; i += 2) {
        sidebarOptions.childNodes[i].setAttribute('i-tooltip', tooltipSide);
    }
    if (start === false)
        $.ajax({url: 'https://swviewer.toolforge.org/php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'rhand',
            rhand: rhmode
        }, dataType: 'json'});
}
RHModeBtn(document.getElementById('RH-mode-btn'), true);

function hotkeysState(button, start) {
    hotkeys = (button.classList.contains('t-btn__active')) ? 1 : 0;

    if (start === false)
        $.ajax({url: 'https://swviewer.toolforge.org/php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'hotkeys',
            hotkeys: hotkeys
        }, dataType: 'json'});
}

function jumpsState(button, start) {
    jumps = (button.classList.contains('t-btn__active')) ? 1 : 0;

    if (start === false)
        $.ajax({url: 'https://swviewer.toolforge.org/php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'jumps',
            jumps: jumps
        }, dataType: 'json'});
}

function terminateStreamBtn (button, start) {
    if (button.classList.contains('t-btn__active')) terminateStream = 1;
    else terminateStream = 0;

    if (start === false)
        $.ajax({url: 'https://swviewer.toolforge.org/php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'terminateStream',
            terminateStream: terminateStream
        }, dataType: 'json'});
}
terminateStreamBtn(document.getElementById('terminate-stream-btn'), true);

function playSound (ps, ignoreIsSound) {
    audiopromise = ps.play();
    if (audiopromise !== undefined)
        audiopromise.then( function() { return null; }).catch( function() { return null; });
};


/*-----preset settings----*/
function createChipCross(listId, inputId, key, chipVal) {
    var chipCross = document.createElement('span');
    chipCross.textContent = '×';
    chipCross.addEventListener('click', () => removeFilter(listId, inputId, key, chipVal, true));
    return chipCross;
}
function removeChip(parent, chipVal) {
    document.getElementById(parent).childNodes.forEach((child) => {
        if (child.textContent === '×' + chipVal) {
            child.parentElement.removeChild(child);
            return;
        }
    })
}

function addFilter(listId, inputId, key) {
    var val = document.getElementById(inputId).value;
    if (val !== "" && val !== null && typeof val !== 'undefined') {
        if (val.indexOf(",") == -1) {
            if (key === 'namespaces') { nsChange(val, "add"); return; }
            let ul = document.getElementById(listId);
            let li = document.createElement('li');
            li.appendChild(createChipCross(listId, inputId, key, val));
            li.appendChild(document.createTextNode(val));
            ul.appendChild(li);
            document.getElementById(inputId).value = "";
            preSettings[key].push(val);
        } else alert("Parameter is incorrect");
    }
}
function removeFilter(listId, inputId, key, chipVal, crossClick) {
    if (crossClick === true) var val = chipVal;
    else var val = document.getElementById(inputId).value;
    if (val !== "" && val !== null && typeof val !== 'undefined' || crossClick === true) {
        if (key === 'namespaces'){ nsChange(val, "delete"); return}
        removeChip(listId, val);
        document.getElementById(inputId).value = "";

        var index = preSettings[key].indexOf(val);
        if (index !== -1) preSettings[key].splice(index, 1);
    }
}

/*---user whitelist---*/
function wluAddFunct() { addFilter('wlareau', 'wladdu', 'wlusers'); };
function wluDeleteFunct() { removeFilter('wlareau', 'wladdu', 'wlusers'); };
/*---wikis whitelist---*/
function wlpAddFunct() { addFilter('wlareap', 'wladdp', 'wlprojects'); };
function wlpDeleteFunct() { removeFilter('wlareap', 'wladdp', 'wlprojects'); };
/*---custom wikis---*/
if (isGlobal == true || isGlobalModeAccess === true) {
    function blpAddFunct() { addFilter('blareap', 'bl-p', 'blprojects'); }
    function blpDeleteFunct() { removeFilter('blareap', 'bl-p', 'blprojects'); }
}
/*---langset wikis---*/
if (isGlobal == true || isGlobalModeAccess === true) {
    function lAddFunct() { addFilter('lareap', 'l-p', 'wikilangs'); }
    function lDeleteFunct() { removeFilter('lareap', 'l-p', 'wikilangs'); }
}
/*---namespaces---*/
function nsAddFunct() { addFilter('nsList', 'ns-input', 'namespaces');}
function nsDeleteFunct() { removeFilter('nsList', 'ns-input', 'namespaces'); }

function nsChange(val, action) {
    var checkChange = false;
    if (isNaN(val)) {
        var match = /^Other\s\((\d+)\)$/g.exec(val);
        if (match && typeof match[1] !== "undefined") {
            if (preSettings["namespaces"].indexOf(match[1]) !== -1) {
                preSettings["namespaces"].splice(preSettings["namespaces"].indexOf(match[1]), 1);
                checkChange = val;
            }
        }
        else {
           var checkNsVal = findKey(val.toLowerCase(), nsList);
           if (checkNsVal !== false) {
               if (action == "add")
                   if (preSettings["namespaces"].indexOf(checkNsVal) == -1) {
                       preSettings["namespaces"].push(checkNsVal);
                       checkChange = val;
                   }
               if (action == "delete") {
                   if (preSettings["namespaces"].indexOf(checkNsVal) !== -1) {
                       preSettings["namespaces"].splice(preSettings["namespaces"].indexOf(checkNsVal), 1);
                       checkChange = val;
                   }
               }
            }
        }
    }
    else {
        if (action == "add")
            if (preSettings["namespaces"].indexOf(val) == -1) {
                preSettings["namespaces"].push(val);
                if (typeof nsList[val] !== "undefined")
                    checkChange = nsList[val];
                else
                    checkChange = "Other (" + val + ")";
            }
        if (action == "delete")
            if (preSettings["namespaces"].indexOf(val) !== -1) {
                preSettings["namespaces"].splice(preSettings["namespaces"].indexOf(val), 1);
                if (typeof nsList[val] !== "undefined")
                    checkChange = nsList[val];
                else
                    checkChange = "Other (" + val + ")";
            }
    }
    
    if (checkChange !== false) {
        if (action == "add") {
            var ul = document.getElementById("nsList");
            var li = document.createElement('li');
            li.appendChild(createChipCross('nsList', 'ns-input', 'namespaces', checkChange));
            li.appendChild(document.createTextNode(checkChange));
            ul.appendChild(li);
        }
        else {
            removeChip("nsList", checkChange);
        }
    }
    document.getElementById("ns-input").value = "";
}

function findKey(val, arr) {
    for (var key in arr) {
        if (arr[key].toLowerCase() == val)
            return key;
    }
    return false;
}
function registeredBtn (button) {
    var sqlreg = 0;
    if (button.classList.contains('t-btn__active'))
        sqlreg = 1;
    else {
        if (!document.getElementById('onlyanons-btn').classList.contains('t-btn__active'))
            document.getElementById('onlyanons-btn').click();
    }
    preSettings["registered"] = sqlreg;
};

function newPagesBtn (button) {
    var sqlnew = 0;
    if (button.classList.contains('t-btn__active'))
        sqlnew = 1;
    else {
        if (document.getElementById('onlynew-pages-btn').classList.contains('t-btn__active'))
            document.getElementById('onlynew-pages-btn').click();
    }
    preSettings["new"] = sqlnew;
};

function onlyNewPagesBtn(button) {
    var sqlonlynew = 0;
    if (button.classList.contains('t-btn__active')) {
        sqlonlynew = 1;
        if (!document.getElementById('new-pages-btn').classList.contains('t-btn__active'))
            document.getElementById('new-pages-btn').click();
    }
    preSettings["onlynew"] = sqlonlynew;
};

function onlyAnonsBtn(button) {
    var onlyanons = 0;
    if (button.classList.contains('t-btn__active'))
        onlyanons = 1;
    else {
        if (!document.getElementById('registered-btn').classList.contains('t-btn__active'))
            document.getElementById('registered-btn').click();
    }
    preSettings["anons"] = onlyanons;
};

if (isGlobal == true || isGlobalModeAccess === true) {
    function smallWikisBtn (button) {
            var sqlswmt = 0;
            if (button.classList.contains('t-btn__active')) {
                sqlswmt = 1;
                if (isGlobalModeAccess === true)
                    sqlswmt = 2;
            }
            preSettings['swmt'] = sqlswmt;
    };

    function lt300Btn (button) {
            var sqlusers = 0;
            if (button.classList.contains('t-btn__active')) {
                sqlusers = 1;
                if (isGlobalModeAccess === true)
                    sqlusers = 2;
            }
            preSettings['users'] = sqlusers;
    }
}
/*---logout---*/
function logout() {
    createDialog({ parentId: "angularapp", id: "unloginDialog", title: useLang["nc-unlogin-title"], removable: true,
        alert: { emoji: "👋", message: useLang["nc-unlogin-q"] },
        buttons: [{
            type: 'negative',
            title:  useLang["nc-unlogin-title"],
            onClick: () => {
                removeDialog("unloginDialog");
                createDialog({ parentId: "angularapp", id: "unloginProgressDialog", alert: { emoji: "🤖", message: useLang["nc-unlogin-progress"] } });
                $.ajax({url: 'php/oauth.php?action=unlogin', crossDomain: true, dataType: 'text',
                    success: function(unloginResp) {
                        if (unloginResp === "Unlogin is done")
                            window.open("https://swviewer.toolforge.org/", "_self");
                        else {
                            removeDialog("unloginProgressDialog");
                            createDialog({ parentId: "angularapp", id: "unloginFailedDialog",
                                alert: { emoji: "⚠️", message: useLang["nc-unlogin-error"] },
                                button: [{ type: 'positive',  title: useLang["alright"], remove: true }]
                            });
                        }
                    }
                });
            }
        }, { title: useLang["cancel"], remove: true }]
    });
};