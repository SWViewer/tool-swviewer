const notifyStack = [];
var notifyCount = 0;

function notifyOpen() {
    notifyCount = 0;
    const notifyIndicator = document.getElementById('notify-indicator');
    const notifyFabIndicator = document.getElementById('notify-fab-indicator');
    notifyIndicator.classList.add('tab-notice-indicator__inactive');
    notifyFabIndicator.parentElement.parentElement.classList.add('notification-fab-base__inactive');
    // document.getElementById('clearAllNotify-base').style.transform = 'translateX(100%)';
}

const removeNotify = (notiID) => {
    const notifyIndex = notifyStack.indexOf(notiID);
    if (notifyIndex === -1) return console.error(`${notiID} is not valid`);
    notifyStack.splice(notifyIndex, 1);
    const noti = document.getElementById(notiID);
    noti.parentElement.removeChild(noti);
}

const removeAllNotify = () => [...notifyStack].forEach(id => removeNotify(id));

const createNotify = (notify) => {
    const date = new Date();
    const notiID = `Notification-${date.getTime()}`
    const noti = bakeEl({
        type: 'div', att: { id: notiID, class: "noti-base" },
        child: (() => {
            var notiChilds = [];
            if (typeof notify.img === 'string') notiChilds.push(bakeEl({
                type: 'div', att: { class: 'noti-img' },
                child: bakeEl({ type: 'img', att: { class: 'secondary-icon', src: notify.img, alt: useLang["notifications-img"] } })
            }));
            if (typeof notify.removable === 'boolean' && notify.removable === true) notiChilds.push(bakeEl({
                type: 'img', att: { class: "noti-cross secondary-hover secondary-icon", onClick: `removeNotify('${notiID}')`, src: './img/cross-filled.svg', alt: useLang["po-img-cross"] }
            }));
            if (typeof notify.title === 'string') notiChilds.push(bakeEl({
                type: 'div', child: notify.title, att: { class: 'noti-title fs-lg' }
            }));
            if (typeof notify.content === 'string') {
                let msg = notify.content;
                const content = [];

                while (msg !== "") {
                    if (msg.match(/\[\[[^\|\|]+\|\|[^\]\]]+\]\]/g) === null) {
                        content.push(msg);
                        break;
                    }
                    content.push(msg.slice(0, msg.indexOf("[[")));
                    msg = msg.slice(msg.indexOf("[[") + 2);
                    const linkName = msg.slice(0, msg.indexOf("||"));
                    msg = msg.slice(msg.indexOf('||') + 2)
                    const linkUrl = msg.slice(0, msg.indexOf("]]"));
                    msg = msg.slice(msg.indexOf(']]') + 2)
                    content.push(bakeEl({
                        type: 'a', att: { href: linkUrl, target: "_blank", rel: "noopener noreferrer" },
                        child: linkName
                    }));
                }

                notiChilds.push(bakeEl({
                    type: 'div', child: content, att: { class: 'noti-content fs-sm' }
                }));
            }
            if (notify.buttons) notiChilds.push(bakeEl({
                type: 'div', att: { class: 'noti-buttons' },
                child: ((buttons) => {
                    var buttonsChild = []
                    buttons.forEach(btn => {
                        const btnObj = { type: 'button', child: btn.title, att: { class: 'fs-md'} }
                        switch (btn.type) {
                            case "accent": btnObj.att.class = 'i-btn__accent accent-hover fs-md'; break;
                            case "positive": btnObj.att.class = 'i-btn__positive fs-md'; break;
                            case "negative": btnObj.att.class = 'i-btn__negative fs-md'; break;
                            default: btnObj.att.class = 'i-btn__secondary-outlined secondary-hover fs-md';
                        }
                        const myBtn = bakeEl(btnObj);

                        if (btn.onClick !== undefined) myBtn.addEventListener('click', btn.onClick);
                        if (btn.remove === true) myBtn.addEventListener('click', (e) => { e.stopPropagation(); removeNotify(notiID); });

                        buttonsChild.push(myBtn);
                    })
                    return buttonsChild;
                })(notify.buttons)
            }));

            return notiChilds;
        })()
    });

    document.getElementById('notify-box').prepend(noti);

    const notifyIndicator = document.getElementById('notify-indicator');
    const notifyFabIndicator = document.getElementById('notify-fab-indicator');
    if (notifyIndicator.classList.contains('tab-notice-indicator__inactive')) {
        notifyIndicator.classList.remove('tab-notice-indicator__inactive');
        notifyFabIndicator.parentElement.parentElement.classList.remove('notification-fab-base__inactive');
    }
    notifyStack.push(notiID);
    notifyCount++;
    notifyIndicator.textContent = notifyCount.toLocaleString(locale);
    notifyFabIndicator.textContent = notifyCount.toLocaleString(locale);
    document.getElementById('clearAllNotify-base').style.transform = 'translateX(0)';
    return notiID;
}
const createNotificationPanel = p => {
    if (document.getElementById('notificationPanel') !== null) return;

    p.append(createPO({
        id: 'notificationPanel',
        header: { title: useLang["notifications-title"] },
        content: {
            child: bakeEl({ 
                type: 'div', att: { id: 'notify-box', class: 'fs-md' }, 
                child: bakeEl({
                    type: 'div', att: { class: "talk-svg fs-md" },
                    child: [bakeEl({
                        type: 'img', att: { class: "secondary-icon", style: "margin-bottom: 48px", src: "./img/bell-filled.svg", alt: useLang["notifications-img"], width: "100px" }
                    }), bakeEl({
                        type: 'span', child: useLang["notifications-empty"]
                    })]
                })
            })
        }
    }));

    document.getElementById('notify-box').parentElement.parentNode.append(bakeEl({
        type: 'div', att: { id: 'clearAllNotify-base', style: 'display: flex; align-items: center; padding-left: var(--side-padding); box-shadow: var(--floatbar-shadow); transform: translateX(100%);' },
        child: [
            bakeEl({ type: 'span', child: useLang['notifications-img-clear'], att: {class: 'fs-sm', style: 'flex:1 ;' } }),
            bakeEl({ type: 'div', att: { onClick: 'removeAllNotify(); closePO();', class: 'secondary-hover', style: 'height: 48px; width: 48px; cursor: pointer; display: flex; justify-content: center; align-items: center;'},
                child: bakeEl({ type: 'img', att: { class: 'touch-ic secondary-icon', src: '/img/clear-bars-filled.svg', alt: useLang["notifications-img-clear"]} })
            })
        ]
    }));
}

createNotificationPanel(document.getElementById('angularapp') || document.body);