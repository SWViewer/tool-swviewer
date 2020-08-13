const getPresetIndex = title => presets.findIndex(preset => preset['title'] === title);

// To save preset.
const savePreset = (index) => {
    const invalidAlert = (title, msg) => createDialog({
        parentId: 'angularapp', id: 'invalidPresetTitleDialog',
        title: title, removable: true,
        alert: { message: msg },
        buttons: [{ type: 'accent', title: 'Alright', remove: true }]
    });

    if (document.getElementById("presetTitleInput") !== null) {
        var presetTitle = document.getElementById("presetTitleInput").value;
        let titlePosition = presets.findIndex(item => item['title'] === presetTitle);
        if ((titlePosition !== -1 && titlePosition !== index) || !/^(\s|\w|\d|\-|\(|\)|\[|\]|\{|\})*?$/.test(presetTitle) || presetTitle === "" || presetTitle === null || presetTitle === undefined) {
            invalidAlert('Invalid Title', 'This preset title is not valid. Please try using "A-Z, a-b, 0-9, (), [], {}, -, _".');
            return;
        }
    } else { var presetTitle = presets[index]['title']; }
    const maxEdits = document.getElementById('max-edits').value;
    if (maxEdits !== '' && !isNaN(maxEdits) && maxEdits !== null) preSettings['editscount'] = maxEdits;
    else { invalidAlert('invalid Edits limit', 'Edit limit should be numeric and non-empty'); return; }
    const maxDays = document.getElementById('max-days').value;
    if (maxDays !== '' && !isNaN(maxDays) && maxDays !== null) preSettings['regdays'] = maxDays;
    else { invalidAlert('invalid Days limit', 'Days limit should be numeric and non-empty'); return; }
    preSettings['title'] = presetTitle;
    if (index === undefined) {
        removeDialog("CREATEPresetDialog");
        $.ajax({
            url: 'php/presets.php', type: 'GET', crossDomain: true, 
            data: { 'action': 'create_preset', 'preset_name': preSettings['title'], 'editscount': preSettings['editscount'].toString(), 'anons': preSettings['anons'].toString(), 'regdays': preSettings['regdays'].toString(), 'registered': preSettings['registered'].toString(), 'new': preSettings['new'].toString(), 'onlynew': preSettings['onlynew'].toString(), 'swmt': preSettings['swmt'].toString(), 'users': preSettings['users'].toString(), 'namespaces': preSettings['namespaces'].toString(), 'wlusers': preSettings['wlusers'].toString(), 'wlprojects': preSettings['wlprojects'].toString(), 'blprojects': preSettings['blprojects'].toString() },
            dataType: 'json'
        });
        presets.push({ 'title': preSettings['title'], 'editscount': preSettings['editscount'].toString(), 'anons': preSettings['anons'].toString(), 'regdays': preSettings['regdays'].toString(), 'registered': preSettings['registered'].toString(), 'new': preSettings['new'].toString(), 'onlynew': preSettings['onlynew'].toString(), 'swmt': preSettings['swmt'].toString(), 'users': preSettings['users'].toString(), 'namespaces': preSettings['namespaces'].toString(), 'wlusers': preSettings['wlusers'].toString(), 'wlprojects': preSettings['wlprojects'].toString(), 'blprojects': preSettings['blprojects'].toString() });
        document.getElementById('presetsBase').append(createPresetHolder(presets.length-1));
        }
    else {
        var oldPresetName = presets[index]["title"];
        $.ajax({
            url: 'php/presets.php', type: 'GET', crossDomain: true,
            data: { 'action': 'edit_preset', 'preset_name': oldPresetName, 'preset_name_new': preSettings['title'], 'editscount': preSettings['editscount'].toString(), 'regdays': preSettings['regdays'].toString(), 'anons': preSettings['anons'].toString(), 'registered': preSettings['registered'].toString(), 'new': preSettings['new'].toString(), 'onlynew': preSettings['onlynew'].toString(), 'swmt': preSettings['swmt'].toString(), 'users': preSettings['users'].toString(), 'namespaces': preSettings['namespaces'].toString(), 'wlusers': preSettings['wlusers'].toString(), 'wlprojects': preSettings['wlprojects'].toString(), 'blprojects': preSettings['blprojects'].toString() },
            dataType: 'json',
            success: function() {
                removeDialog(oldPresetName + "PresetDialog");
                presets[index] = { 'title': preSettings['title'], 'editscount': preSettings['editscount'].toString(), 'anons': preSettings['anons'].toString(), 'regdays': preSettings['regdays'].toString(), 'registered': preSettings['registered'].toString(), 'new': preSettings['new'].toString(), 'onlynew': preSettings['onlynew'].toString(), 'swmt': preSettings['swmt'].toString(), 'users': preSettings['users'].toString(), 'namespaces': preSettings['namespaces'].toString(), 'wlusers': preSettings['wlusers'].toString(), 'wlprojects': preSettings['wlprojects'].toString(), 'blprojects': preSettings['blprojects'].toString() };
                if (oldPresetName !== presetTitle) {
                    if (selectedPreset === index)
                        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: { 'action': 'set', query: 'preset', preset: presetTitle }, dataType: 'json',
                        error: function() { alert("Error (save 2)"); }});
                    initPresets(false);
                }
            }, error: function() { alert("Error (save)"); }
        });
    }
}

// To delete preset.
const deletePreset = (index) => {
    createDialog({
        parentId: 'angularapp', id: 'removePresetDiaog',
        removable: true,
        alert: { emoji: '🗑️', message: `Delete  "${presets[index].title}" preset?` },
        buttons: [{
            type: 'negative', title: 'Delete',
            onClick: () => {
                var presetHolder = document.getElementById(presets[index].title + "PresetHolder");
                var presetTitle = presets[index].title;
            
                if (presetTitle === "Default") {
                    createDialog({ parentId: 'angularapp', id: 'removeDefaultPresetAlert',
                        title: 'Warning!', removable: true,
                        alert: {emoji: '⚠️', message: "We don't recommend you to delete default preset! Alright?"},
                        buttons: [{ type: 'accent', title: 'Alright', remove: true }]
                    } );
                    return;
                }
                presetHolder.parentElement.removeChild(presetHolder);
            
                presets.splice(index, 1);
                
                $.ajax({url: 'php/presets.php', type: 'GET', crossDomain: true, data: { 'action': 'delete_preset', 'preset_name': presetTitle }, dataType: 'json'});
            
                if (index === selectedPreset) {
                    selectedPreset = undefined;
                    selectPreset(presets.findIndex((item) => item['title'] === 'Default'));
                }
            }, remove: true
        }, { title: 'Cancel', remove: true } ]
    });
}

// To selected preset.
const selectPreset = (index, whildEntry = false, req = true) => {
    if (index !== selectedPreset || whildEntry) {
        if (!whildEntry && selectedPreset !== undefined) {
            document.getElementById(presets[selectedPreset].title + "PresetHolder").classList.remove('preset__selected');
        }
        document.getElementById(presets[index].title + "PresetHolder").classList.add('preset__selected');
        selectedPreset = index;

        document.getElementById('drawerPresetTitle').textContent = presets[index].title;
        const ecp = document.getElementById('editCurrentPreset');
        const ecpNew = ecp.cloneNode(true);
        ecpNew.addEventListener('click', () => editPreset(index) );
        ecp.parentElement.replaceChild(ecpNew, ecp);

        if (req)
            $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: { 'action': 'set', query: 'preset', preset: presets[index].title }, dataType: 'json'});
    }
}

// To edit specific preset
// if used without parameter create new preset
const editPreset = (index) => {
    if (presets[index] !== undefined) {
        var PDialogId = presets[index].title + 'PresetDialog';
        var PTitle = presets[index].title;
    }
    const editBody = document.createElement('div');
    editBody.className = "edit-preset__container"
    var editPTitleTemp = document.getElementById('editPTitleTemplate');
    var editPTemp = document.getElementById('editPresetTemplate');
    if (PTitle !== 'Default') editBody.append(editPTitleTemp.content.cloneNode(true));
    editBody.append(editPTemp.content.cloneNode(true));
    createDialog({
        parentId: 'angularapp',
        id: PDialogId || 'CREATEPresetDialog',
        title: PTitle || 'Create Preset',
        removable: true,
        custom: { insertElement: editBody },
        buttons: [
            { type: 'accent', title: 'Save', onClick: () => savePreset(index), remove: false },
            { title: 'Cancel', remove: true }
        ]
    });
    if (index === undefined) preSettings = { title: "", regdays: "5", editscount: "100", anons: "1", registered: "1", new: "1", onlynew: "0", swmt: "0", users: "0", namespaces: "", wlusers: "", wlprojects: "", blprojects: ""};
    else preSettings = {...presets[index]};

    if (true) {
        if (typeof document.getElementById("small-wikis-btn") !== 'undefined' && document.getElementById("small-wikis-btn") !== null)
            if (typeof preSettings['swmt'] !== 'undefined') {
                if ((preSettings['swmt'] === '1' || preSettings['swmt'] === '2') && isGlobal === true)
                    toggleTButton(document.getElementById('small-wikis-btn'));
                if (preSettings['swmt'] === '2' && isGlobalModeAccess === true)
                    toggleTButton(document.getElementById('small-wikis-btn'));
            }

        if (typeof document.getElementById("lt-300-btn") !== 'undefined' && document.getElementById("lt-300-btn") !== null)
            if (typeof preSettings['users'] !== 'undefined') {
                if ((preSettings['users'] === '1' || preSettings['users'] === '2') && isGlobal === true)
                    toggleTButton(document.getElementById('lt-300-btn'));
                if (preSettings['users'] == '2' && isGlobalModeAccess === true)
                    toggleTButton(document.getElementById('lt-300-btn'));
            }

        
        function initIValue (value, input) {
            if (typeof value !== "undefined" && value !== "") document.getElementById(input).value = value;
        }
        initIValue(preSettings['editscount'], 'max-edits');
        initIValue(preSettings['regdays'], 'max-days');

        function initTBtn (value, btn) {
            if (typeof value !== "undefined" && value === "1") toggleTButton(document.getElementById(btn));
        }
        initTBtn(preSettings['registered'], 'registered-btn');
        initTBtn(preSettings['new'], 'new-pages-btn');
        initTBtn(preSettings['onlynew'], 'onlynew-pages-btn');
        initTBtn(preSettings['anons'], 'onlyanons-btn');
        
        function initFilters (listId, inputId, key) {
            if (typeof preSettings[key] !== "undefined") {
                if (typeof preSettings[key] === 'string') preSettings[key] = preSettings[key].split(',');
                preSettings[key].forEach((val) => {
                    if (val === "") return;
                    if (key === 'namespaces') {
                        if (typeof nsList[val] !== "undefined") val = nsList[val];
                        else val = "Other (" + val + ")";
                    }
                    var ul = document.getElementById(listId);
                    var li = document.createElement('li');
                    li.appendChild(createChipCross(listId, inputId, key, val));
                    li.appendChild(document.createTextNode(val));
                    ul.appendChild(li);
                });
            }
        }
        initFilters('wlareap', 'wladdp', 'wlprojects');
        initFilters('wlareau', 'wladdu', 'wlusers');
        if (document.getElementById("blareap") !== null) initFilters('blareap', 'bl-p', 'blprojects');
        initFilters('nsList', 'ns-input', 'namespaces');

        if (PTitle !== "Default")
            document.getElementById("presetTitleInput").value = preSettings['title'];
        else {
            // document.querySelector("#DefaultPresetDialog").querySelector(".dialog__body").classList.add("disabled");
            // document.querySelector("#DefaultPresetDialog").querySelector(".dialog__base").querySelector(".dialog__buttons").querySelector(".i-btn__accent").classList.add("disabled");
        }
    }
}

//Create a preset holder.
const createPresetHolder = (index) => {
    const PRESET_TITLE = presets[index].title;

    var PContainer = document.createElement('div');
    PContainer.className = "preset-container";
    PContainer.id = PRESET_TITLE + "PresetHolder";

    var PSelector = document.createElement('button');
    PSelector.className = "i-btn__primary primary-hover fs-sm";
    PSelector.textContent = PRESET_TITLE;
    PSelector.id = PRESET_TITLE + "PresetSelector";
    PSelector.addEventListener('click',  () => selectPreset(getPresetIndex(PRESET_TITLE)));

    var PDelete = document.createElement('div');
    PDelete.className = "i-btn__primary o-btn__circle primary-hover fs-sm";
    var PDeleteImg = document.createElement('img');
    PDeleteImg.className = 'touch-ic primary-icon';
    PDeleteImg.src = "./img/delete-filled.svg";
    PDeleteImg.alt="Delete image";
    PDelete.append(PDeleteImg);
    if (PRESET_TITLE === 'Default') PDelete.style.visibility = "hidden";
    else PDelete.addEventListener('click',  () => deletePreset(getPresetIndex(PRESET_TITLE)));

    var PEdit = document.createElement('div');
    PEdit.className = "i-btn__primary o-btn__circle primary-hover fs-sm";
    var PEditImg = document.createElement('img');
    PEditImg.className = 'touch-ic primary-icon';
    PEditImg.src = "./img/pencil-filled.svg";
    PEditImg.alt="Pencil image";
    PEdit.append(PEditImg);
    PEdit.addEventListener('click',  () => editPreset(getPresetIndex(PRESET_TITLE)));
    
    PContainer.append(PSelector ,PDelete , PEdit);

    return PContainer;
}

// initialize the presets
const initPresets = (req = true) => {
    var PBase = document.getElementById('presetsBase');

    var containers = document.getElementsByClassName("preset-container");
    while(containers[0]) {
        PBase.removeChild(containers[0]);
    }
    if (req) getPresets(settingslist);

    for (let index in presets) PBase.append(createPresetHolder(index));
    selectPreset(selectedPreset, true, false)
    return PBase;
}

initPresets();
