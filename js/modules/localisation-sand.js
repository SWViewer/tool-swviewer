function sandwichLocalisation(baseContent, dirLocal, localMessage, targetEl, patternType, parsedLen, styleEl, uniqId, linkLocalisation, baseAdd = false) {
    var parsedMessage; var baseContent = baseContent;
    if (patternType === 'link')
        parsedMessage = (dirLocal === 'ltr') ? localMessage.match(/^(.*?)\[\$link\|(.*?)\](.*)$/) : localMessage.match(/^(.*?)\[\$\s?link\s?\|\s?(.*?)\](.*)$/);
    else {
        if (patternType === 'name')
            parsedMessage = (dirLocal === 'ltr') ? localMessage.match(/^(.*?)\$1(.*)/) : localMessage.match(/^(.*?)\$\s?1(.*)/);
        else
            parsedMessage = (dirLocal === 'ltr') ? localMessage.match(/^(.*?)\[\$1\|(.*?)\](.*)$/) : localMessage.match(/^(.*?)\[\$\s?1\s?\|\s?(.*?)\](.*)$/);
    }
    if (parsedMessage !== null && parsedMessage.length === parsedLen) {
        targetEl.textContent = '';
        var preLocalisedEl1 = baseContent.createElement('div');
        var preLocalisedEl2 = (linkLocalisation === false) ? baseContent.createElement('div') : baseContent.createElement('a');
        var preLocalisedEl3 = baseContent.createElement('div');
        preLocalisedEl1.id = 'localisedEl' + uniqId + '1';
        preLocalisedEl2.id = 'localisedEl' + uniqId + '2';
        preLocalisedEl3.id = 'localisedEl' + uniqId + '3';
        preLocalisedEl1.style.display = preLocalisedEl2.style.display = preLocalisedEl3.style.display = styleEl;
        if (linkLocalisation !== false) {
            preLocalisedEl2.href = linkLocalisation;
            preLocalisedEl2.rel = 'noopener noreferrer';
            preLocalisedEl2.target = '_blank';
        }

        targetEl.appendChild(preLocalisedEl1); targetEl.appendChild(preLocalisedEl2); targetEl.appendChild(preLocalisedEl3);
        if (baseAdd !== false)
            baseContent = baseAdd;
        baseContent.getElementById('localisedEl' + uniqId + '1').textContent = parsedMessage[1];
        if (parsedLen === 3) {
            baseContent.getElementById('localisedEl' + uniqId + '2').textContent = 'SWViewer';
            baseContent.getElementById('localisedEl' + uniqId + '3').textContent = parsedMessage[2];
        } else {
            baseContent.getElementById('localisedEl' + uniqId + '2').textContent = parsedMessage[2];
            baseContent.getElementById('localisedEl' + uniqId + '3').textContent = parsedMessage[3];
        }
    }
}
