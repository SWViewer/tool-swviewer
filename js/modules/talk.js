var userColor = new Map();
const getUserColor = (user) => {
    if (!userColor.has(user)) userColor.set(user, `hsl(${Math.floor(Math.random() * 361)}, ${(Math.floor(Math.random() * 50) + 40)}%, 50%`);
    return userColor.get(user);
}

const parseDate = (date) => {
    const parsed = Date.parse(date);
    if (!isNaN(parsed)) {
        return parsed;
    }

    return Date.parse(date.replace(/-/g, '/').replace(/[a-z]+/gi, ' '));
}
var lastMsg = {user: undefined, time: {hours: undefined, minuts: undefined}};
const addToTalk = (timestamp, nickname, text) => {
    var hours, minuts, seconds;

    let timetexttmp = (timestamp == null) ? moment(moment().utc().format("YYYY-MM-DD hh:mm:ss")) : moment(timestamp, "YYYY-MM-DD hh:mm:ss");
    hours = timetexttmp.hours().toString();
    minuts = timetexttmp.minutes().toString();
    seconds = timetexttmp.seconds().toString();

    var textTime = timetexttmp.locale(locale).format("LT");
    var textUser = nickname;
    var textMessage = text;

    var blockCap = document.createElement('div');
    blockCap.className = 'phrase-cap ng-non-bindable';
    var blockTime = document.createElement('div');
    blockTime.className = 'phrase-line1 fs-xs ng-non-bindable';
    var blockUser = document.createElement('div');
    blockUser.className = 'phrase-line2 fs-md ng-non-bindable';
    blockUser.setAttribute('onclick', 'selectTalkUsers(this)');
    var blockMessage = document.createElement('div');
    blockMessage.className = 'phrase-line3 fs-sm ng-non-bindable';

    blockCap.textContent = textUser.substring(0, 2);
    blockTime.textContent = textTime;
    blockUser.textContent = textUser;

    /* Find and attach links in user message. */
    const LINK_PATTERN = /\b(http|https):\/\/\S+/g;
    if (LINK_PATTERN.test(textMessage)) {
        var links = textMessage.match(LINK_PATTERN);
        var subMessStart = 0;
        var subMessEnd = textMessage.indexOf(links[0]);
        for (let index in links) {
            if (links.hasOwnProperty(index)) {
                blockMessage.appendChild(document.createTextNode(textMessage.substring(subMessStart, subMessEnd)));

                var link = document.createElement('a');
                link.href = links[index];
                link.target = "_blank";
                link.rel = "noopener noreferrer"
                link.style.wordBreak = "break-all";
                link.textContent = links[index];
                blockMessage.appendChild(link);

                subMessStart = (subMessEnd + links[index].length);
                subMessEnd = subMessStart + (textMessage.substring(subMessStart, textMessage.length)).search(LINK_PATTERN);
            }
        }
        blockMessage.appendChild(document.createTextNode(textMessage.substring(subMessStart, textMessage.length)));
    } else {
        blockMessage.textContent = textMessage;
    }

    var blockPhrase = document.createElement('div');
    blockPhrase.className = 'phrase-talk';

    if (lastMsg.user === nickname && lastMsg.time.hours === hours && lastMsg.time.minuts === minuts && !document.getElementById('form-talk').lastChild.classList.contains('days-ago-talk')) {
        blockCap.style.height = '0px';
        blockPhrase.appendChild(blockCap);
        blockPhrase.appendChild(blockMessage);
        document.getElementById('form-talk').lastChild.style.paddingBottom = "0";
        blockPhrase.style.marginTop = "-16px";
    } else {
        const userColor = getUserColor(nickname);

        blockCap.style.background = userColor;
        blockUser.style.color = userColor;

        blockPhrase.appendChild(blockCap);
        blockPhrase.appendChild(blockTime);
        blockPhrase.appendChild(blockUser);
        blockPhrase.appendChild(blockMessage);
        lastMsg.user = nickname;
        lastMsg.time.hours = hours;
        lastMsg.time.minuts = minuts;
    }
    document.getElementById('form-talk').appendChild(blockPhrase);
    scrollToBottom("talk-content");
}

const addToTalkSection = (datatext) => {
    var blockMessage = document.createElement('div');
    blockMessage.className = "days-ago-talk fs-xs";
    blockMessage.textContent = datatext;

    document.getElementById('form-talk').appendChild(blockMessage);
    scrollToBottom("talk-content");
}

var daysAgoToday = false;
var historyCount = 0;
const downloadHistoryTalk = () => {
    let talkSVG = document.getElementById('form-talk').childNodes[0];
    if (getComputedStyle(talkSVG).display === 'none') {
        let formTalk = document.getElementById('form-talk');
        formTalk.textContent = '';
        formTalk.append(talkSVG);
    }
    var formData = new FormData();
    formData.append("action", "get");
    $.ajax({
        url: 'php/talkHistory.php', type: 'POST', data: {action: 'get'}, crossDomain: true, dataType: 'json',
        success: function (talkHistory) {
            var options = {year: 'numeric', month: 'long', day: 'numeric', weekday: 'long', timezone: 'UTC'};

            historyCount = 0;
            for (let i = 4; i !== -1; i--) {
                var daysAgo = null;
                if (talkHistory.hasOwnProperty(i)) {
                    if (talkHistory[i] !== null && talkHistory[i].length > 0) {
                        if (i === 0) {
                            daysAgo = useLang["talk-today"];
                            daysAgoToday = true;
                        } else {
                            if (i === 1)
                                daysAgo = useLang["talk-yesterday"];
                            else {
                                var dateHistory = new Date(Date.now() - (i * 1000 * 60 * 60 * 24));
                                daysAgo =  moment(Date.parse(dateHistory)).locale(locale).format("LL");
                            }
                        }

                        historyCount++;
                        addToTalkSection(daysAgo);

                        talkHistory[i].forEach(function (el) {
                            addToTalk(el['msgtime'], el['name'], el['text']);
                        });
                    }
                }
            }
        }
    });
}

const createTalkPW = (p) => {
    if (document.getElementById('talkForm') === null) {
        p.append(createPW({
            id: 'talkForm',
            header: {
                title: useLang["talk-title"],
                buttons: [{
                    class: 'mobile-only', onClick: 'closePW()', toolTip: useLang["tooltip-po-close"],
                    img: {src: './img/cross-filled.svg', alt: useLang["talk-img-cross"]}
                }, {
                    class: 'mobile-only',
                    onClick: "openPWDrawer('talkPWDrawer', 'talkPWOverlay')",
                    toolTip: useLang["tooltip-talk-people"],
                    img: {class: 'touch-ic__w-free', src: './img/people-filled.svg', alt: useLang["talk-img-people"]}
                }
                ]
            },
            content: {
                id: 'talk-content',
                child: bakeEl({
                    type: 'div', att: {id: 'form-talk'},
                    child: bakeEl({
                        type: 'div', att: {class: 'talk-svg fs-md'},
                        child: [
                            bakeEl({
                                type: 'img',
                                att: {
                                    class: "secondary-icon",
                                    style: "margin-bottom: 48px;",
                                    src: "./img/message-filled.svg",
                                    alt: useLang["talk-img-app"],
                                    width: "100px"
                                }
                            }),
                            bakeEl({type: 'span', child: useLang["talk-no-messages"]})
                        ]
                    })
                }),
                floatbar: {
                    onSubmit: "event.preventDefault(); document.getElementById('btn-send-talk').onclick();",
                    input: {
                        id: 'phrase-send-talk',
                        onFocus: "scrollToBottom('talk-content')",
                        maxLength: '600',
                        placeholder: useLang["talk-send-placeholder"]
                    },
                    buttons: [{
                        id: 'btn-send-talk',
                        onClick: "angular.element(document.getElementById('angularapp')).scope().sendTalkMsg()",
                        toolTip: useLang["tooltip-talk-send"],
                        img: {
                            src: './img/send-filled.svg',
                            alt: useLang["talk-img-send"]
                        }
                    }
                    ]
                }
            },
            drawer: {
                id: 'talkPWDrawer',
                child: [
                    bakeEl({
                        type: 'div',
                        att: {class: 'action-header__sticky'},
                        child: bakeEl({
                            type: 'span',
                            child: useLang["talk-people"],
                            att: {class: 'action-header__title fs-lg'}
                        })
                    }),
                    bakeEl({type: 'div', att: {id: 'talkPeopleContent', class: 'pw__drawer__content'}})
                ]
            },
            overlay: {
                id: 'talkPWOverlay',
                onClick: "closePWDrawer('talkPWDrawer', 'talkPWOverlay')"
            }
        }));
        downloadHistoryTalk();
        angular.element(document.getElementById('angularapp')).scope().displayTalkPeople();
    }
};

createTalkPW(document.getElementById('windowContent'));