var dialogStack = [];

const removeDialog = (id) => {
    if (Object.keys(dialogStack).length !== 0) {
        if (id === undefined) {
            id = Object.keys(dialogStack)[Object.keys(dialogStack).length - 1];
            if (dialogStack[id].removable !== true) return;
        } else if (dialogStack[id] === undefined) {
            console.log('Dialog with id: "' + id + '" not found!');
            return;
        }
        var DParent = document.getElementById(dialogStack[id].parentId);
        var dialog = document.getElementById(id);
        DParent.removeChild(dialog);
        delete dialogStack[id];
        return
    }
    console.error("No dialog to remove!");
}

// To create the new dialog.
const createDialog = (dialog) => {

    if (dialog.parentId === undefined || dialog.id === undefined) {
        console.error("Dialog id and its parentId are required!");
        return -1;
    }
    if (dialogStack[dialog.id] !== undefined) {
        console.error('Dialog with id: "' + dialog.id + '" already exist!');
        return -1;
    }

    var DBackground = document.createElement('div');
    DBackground.className = "dialog__background secondary-cont hidden-scroll";
    DBackground.id = dialog.id;
    var DFlex = document.createElement('div');
    DFlex.className = "dialog__flex";

    var DBase = document.createElement('div');
    DBase.className = "dialog__base";
    DBase.addEventListener('click', e => e.stopPropagation());

    /*Create Dialog header*/
    if (dialog.title !== undefined || dialog.removable) {
        var DHeader = document.createElement('div');
        DHeader.className = "dialog__header";
        if (dialog.title !== undefined) {
            var DTitle = document.createElement('span');
            DTitle.className = "d-title fs-xl";
            DTitle.textContent = dialog.title;
            DHeader.append(DTitle);
        }
        if (dialog.removable === true) {
            var DClose = document.createElement('div');
            DClose.className = "d-close";
            var DCloseIC = document.createElement('img');
            DCloseIC.className = "touch-ic secondary-icon";
            DCloseIC.src = "./img/cross-filled.svg"
            DCloseIC.alt = useLang["dialog-img-close"];
            DBackground.addEventListener('click', (e) => { e.stopPropagation(); removeDialog(dialog.id); });
            DClose.addEventListener('click', (e) => { e.stopPropagation(); removeDialog(dialog.id); });
            DClose.append(DCloseIC);
            DHeader.append(DClose);
        }
        DBase.append(DHeader);
    }
    
    /*Create Dialog body*/
    if (dialog.alert !== undefined || dialog.custom !== undefined) {
        var DBody = document.createElement('div');
        DBody.className = "dialog__body";
        if (dialog.alert !== undefined) {
            if (dialog.alert.emoji !== undefined) {
                var DBodyEmoji = document.createElement('p');
                DBodyEmoji.className = 'dialog__emoji';
                DBodyEmoji.textContent = dialog.alert.emoji;
                DBody.append(DBodyEmoji);
            }
            if (dialog.alert.message !== undefined) {
                var DBodyMessage = document.createElement('p');
                DBodyMessage.className = 'dialog__message';
                DBodyMessage.textContent = dialog.alert.message;
                DBody.append(DBodyMessage);
            }
        }
        if (dialog.custom !== undefined) {
            var DBodyCustom = document.createElement('div');
            DBodyCustom.className = "dialog__custom";
            if (dialog.custom.insertElement !== undefined) {
                DBodyCustom.append(dialog.custom.insertElement);
            }
            DBody.append(DBodyCustom);
        }
        DBase.append(DBody);
    }

    /*Create Dialog buttons*/
    if (dialog.buttons !== undefined && dialog.buttons.length !== 0) {
        var DButtons = document.createElement('div');
        DButtons.className = 'dialog__buttons';
        dialog.buttons.forEach((button) => {
            var btn = document.createElement('button');
            if (button.title !== undefined) btn.textContent = button.title;
            switch (button.type) {
                case "accent": btn.className = 'i-btn__accent accent-hover fs-md'; break;
                case "positive": btn.className = 'i-btn__positive fs-md'; break;
                case "negative": btn.className = 'i-btn__negative secondary-hover fs-md'; break;
                default: btn.className = 'i-btn__secondary-outlined secondary-hover fs-md';
            }
            if (button.onClick !== undefined) btn.addEventListener('click', button.onClick);
            if (button.remove === true) btn.addEventListener('click', (e) => { e.stopPropagation(); removeDialog(dialog.id); });
            DButtons.append(btn);
        });
        DBase.append(DButtons);
    }
    DFlex.append(DBase);
    DBackground.append(DFlex);
    var DParent = document.getElementById(dialog.parentId);
    if (DParent)  {
        DParent.append(DBackground);
        dialogStack[dialog.id] = {
            parentId: dialog.parentId,
            removable: dialog.removable
        }
        setTimeout(() => {
            document.getElementById(dialog.id).classList.add('dialog__animation');
            document.getElementById(dialog.id).childNodes[0].childNodes[0].classList.add('dialog__animation');
        }, 100);
        return true;
    } else {
        console.error('Parent with id: "' + dialog.parentId + '" for dialog not found!');
        return -1;
    }
}