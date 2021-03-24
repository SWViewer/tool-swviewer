const initLogs = () => {
    const LOGS_SEARCH_INPUT = document.getElementById('logsSearch-input');
    const ACTION_SELECTOR = document.getElementById('actionSelector');
    const NEXT_LOGS = document.getElementById('nextLogs');
    const PREV_LOGS = document.getElementById('prevLogs');
    const LOGS_BOX = document.getElementById('logsBox');
    const BTN_REFRESH = document.getElementById('btnRefresh');
    const FORM_SERACH_LOGS = document.getElementById('form-searchLogs');
    const BTN_SEARCH_LOGS = document.getElementById('btn-searchLogs');

    const ACTION_COLORS = { 'rollback': '#c8b40e', 'undo': '#db24b0', 'delete': '#672dd2', 'edit': '#2dd280', 'warn': '#d92c26', 'report': '#e3791c', 'protect': '#1cb3e3' };
    const ACTION_TRANSLATED = { 'rollback': useLang["logs-action-rollback"], 'undo': useLang["logs-action-undo"], 'delete': useLang["logs-action-delete"], 'edit': useLang["logs-action-edit"], 'warn': useLang["logs-action-warn"], 'report': useLang["logs-action-report"], 'protect': useLang["logs-action-protect"] };
    var logsSearchPhrase = "", action = "", logsLimit = 40, logsOffset = 0;

    ACTION_SELECTOR.onchange = () => action = ACTION_SELECTOR.value;

    BTN_REFRESH.onclick = () => refreshLogs();
    FORM_SERACH_LOGS.onsubmit = (e) => {
        e.preventDefault();
        searchLogs();
    }
    BTN_SEARCH_LOGS.onclick = () => searchLogs();
    NEXT_LOGS.onclick = () => {
        logsOffset += logsLimit;
        getLogs();
    }
    PREV_LOGS.onclick = () => {
        logsOffset -= logsLimit;
        if(logsOffset < 0) logsOffset = 0;
        getLogs();
    }

    const refreshLogs = () => {
        LOGS_SEARCH_INPUT.value = '';
        ACTION_SELECTOR.selectedIndex = 0;
        action = "";
        logsSearchPhrase = "";
        logsOffset = 0;
        getLogs();
    }
    const handleLogsUI = () => {
        if(logsOffset == 0) {
            PREV_LOGS.style.display = 'none';
            PREV_LOGS.parentElement.style.justifyContent = 'flex-end';
        } else {
            PREV_LOGS.style.display = 'unset';
            PREV_LOGS.parentElement.style.justifyContent = 'space-between';
        }
        if(document.getElementById('logsTable').childElementCount <= logsLimit) {
            NEXT_LOGS.style.display = "none";
            LOGS_BOX.append(bakeEl({ type: 'div', child: useLang["logs-no-more"], att: { id: 'noMoreLogs', style: 'padding: 8px 0; text-align: center; color: var(--tc-secondary-low);' } }));
        } else {
            NEXT_LOGS.style.display = "unset";
        }
    }
    const displayLogs = (logs) => {
        const logsCols = ['lt__sno', 'lt__user', 'lt__action', 'lt__wiki', 'lt__title', 'lt__date'];
        const logsColsName = [useLang["logs-cols-n"], useLang["logs-cols-user"], useLang["logs-cols-action"], useLang["logs-cols-wiki"], useLang["logs-cols-title"], useLang["logs-cols-date"]];
        var sno = logsOffset;

        var logsTable = bakeEl({ type: 'div', att: { id: 'logsTable', class: 'logs-table' } });
        
        var headerRow = bakeEl({ type: 'div', att: { class: 'lt-row fs-md' } });
        for (let i = 0; i < logsCols.length; i++) {
            var headerColumn = bakeEl({ type: 'div', child: logsColsName[i], att: { class: logsCols[i] } });
            headerRow.append(headerColumn);
        }
        logsTable.append(headerRow);

        logs.forEach((log) => {
            sno++;
            var columns = {};
            var row = bakeEl({ type: 'div', att: { class: 'lt-row fs-sm' } });
            logsCols.forEach((col) => {
                var column = bakeEl({ type: 'div', att: { class: col } });
                columns[col] = column;
            })
            columns['lt__sno'].textContent = sno.toLocaleString(locale);
            var link = document.createElement('a');
            link.href = log['diff'].substring(0, (log['diff'].indexOf('.org/')) + 5) + "wiki/user:" + log['user'];
            link.textContent = log['user']; link.target = '_blank'; link.rel = "noopener noreferrer";
            columns['lt__user'].append(link);
            columns['lt__action'].textContent = (typeof ACTION_TRANSLATED[log['type']] !== "undefined") ? ACTION_TRANSLATED[log['type']] : ACTION_TRANSLATED[log['type']];
            columns['lt__action'].style.color = ACTION_COLORS[log['type']];
            columns['lt__wiki'].textContent = log['wiki'];
            var link = document.createElement('a');
            link.href = log['diff']; link.textContent = log['title']; link.target = '_blank'; link.rel = "noopener noreferrer";
            columns['lt__title'].append(link);
            var logTime = new Date(Date.parse(moment(log['date'], "YYYY-MM-DD hh:mm:ss").format("YYYY-MM-DD")));
            var today = new Date(Date.parse(moment().utc().format("YYYY-MM-DD")));
            columns['lt__date'].textContent = (today.getTime() > logTime.getTime() || today.getTime() < logTime.getTime()) ? moment(log['date'], "YYYY-MM-DD hh:mm:ss").locale(locale).format("L") : moment(log['date'], "YYYY-MM-DD hh:mm:ss").locale(locale).format("LT");

            for (let column in columns) row.append(columns[column]);
            
            logsTable.append(row);
        });

        if (document.getElementById('noMoreLogs')) document.getElementById('noMoreLogs').remove();
        if (document.getElementById('logsTable')) document.getElementById('logsTable').remove();
        LOGS_BOX.append(logsTable);
        LOGS_BOX.parentElement.scrollTop = 0;
        handleLogsUI();
        LOGS_BOX.parentElement.classList.remove('disabled');
    }
    const getLogs = () => {
        LOGS_BOX.parentElement.classList.add('disabled');

        fetch('https://swviewer.toolforge.org/php/logs.php', {
            method: 'post',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                sp: logsSearchPhrase,
                st: action,
                li: logsLimit,
                of: logsOffset
            })
        }).then((response) => response.json())
        .then((logs) => displayLogs(logs))
        .catch((error) => console.error(error));
    }
    const searchLogs = () => {
        logsSearchPhrase = LOGS_SEARCH_INPUT.value;
        logsOffset = 0;
        getLogs();
    }
    getLogs();
}

const createLogsPW = (p) => {
    if (document.getElementById('logs') === null) {
        p.append(createPW({
            id: "logs",
            header: {
                title: useLang["logs-title"],
                buttons: [{
                        id: 'btnRefresh', toolTip: useLang["tooltip-logs-refresh"],
                        img: { src: './img/reload-filled.svg', alt: useLang["logs-img-reload"] }
                    }, {
                        class: 'mobile-only', onClick: 'closePW()', toolTip: useLang["tooltip-po-close"],
                        img: { src: './img/cross-filled.svg', alt: 'Cross Image' }
                    }
                ]
        
            },
            content: {
                id: 'logs-content',
                style: 'padding: 0;',
                child: [
                    bakeEl({ type: 'div', att: { id: 'logsBox' } }),
                    bakeEl({ type: 'div', att: { class: 'logBox-control' },
                        child: [
                            bakeEl({ type: 'button', child: useLang["logs-prev"], att: { id: 'prevLogs', class: 'i-btn__secondary-outlined secondary-hover fs-md', style: 'display: none;' } }),
                            bakeEl({ type: 'button', child: useLang["logs-next"], att: { id: 'nextLogs', class: 'i-btn__secondary-outlined secondary-hover fs-md', style: 'display: none;' } })
                        ]
                    })
                ],
                floatbar: {
                    id: 'form-searchLogs',
                    input: { id: 'logsSearch-input', maxLength: '600', placeholder: useLang["logs-search-placeholder"] },
                    buttons: [{
                            style: 'width: unset;',
                            toolTip: useLang["tooltip-logs-filter"],
                            child: bakeEl({
                                type: 'select', att: { id: 'actionSelector', class: 'i-select__secondary fs-md' },
                                child: [
                                    bakeEl({ type: 'option', child: useLang["logs-action-all"], att: { value: '' } }),
                                    bakeEl({ type: 'option', child: useLang["logs-action-rollback"], att: { value: 'rollback' } }),
                                    bakeEl({ type: 'option', child: useLang["logs-action-undo"], att: { value: 'undo' } }),
                                    bakeEl({ type: 'option', child: useLang["logs-action-delete"], att: { value: 'delete' } }),
                                    bakeEl({ type: 'option', child: useLang["logs-action-edit"], att: { value: 'edit' } }),
                                    bakeEl({ type: 'option', child: useLang["logs-action-warn"], att: { value: 'warn' } }),
                                    bakeEl({ type: 'option', child: useLang["logs-action-report"], att: { value: 'report' } }),
                                    bakeEl({ type: 'option', child: useLang["logs-action-protect"], att: { value: 'protect' } })
                                ]
                            })
                        }, {
                            id: 'btn-searchLogs',
                            toolTip: useLang["tooltip-logs-search"],
                            img: { src: './img/search-filled.svg', alt: useLang["logs-img-search"] }
                        }
                    ]
                }
            }
        }));
        initLogs();
    }
};

createLogsPW(document.getElementById('windowContent'));