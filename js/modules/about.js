const addAbout = (id) => {
    fetch('https://swviewer.toolforge.org/templates/about.html')
        .then(res => res.text())
        .then(text => {
            let parser = new DOMParser();
            let about = parser.parseFromString(text, 'text/html');

            var elementsLang = about.getElementsByClassName("custom-lang");
            for (el in elementsLang) {
                if (elementsLang.hasOwnProperty(el)) {
                    var attrs = elementsLang[el].attributes;
                    for (l in attrs) {
                        if (attrs.hasOwnProperty(l)) {
                            if (typeof attrs[l].value !== "undefined")
                                if (useLang.hasOwnProperty(attrs[l].value.replace("[", "").replace("]", "")))
                                    elementsLang[el].setAttribute(attrs[l].name, useLang[attrs[l].value.replace("[", "").replace("]", "")]);
                        }
                    }
                    if (typeof elementsLang[el].value !== "undefined")
                        if (useLang.hasOwnProperty(elementsLang[el].value.replace("[", "").replace("]", "")))
                            elementsLang[el].value = useLang[elementsLang[el].value.replace("[", "").replace("]", "")];
                    if (typeof elementsLang[el].textContent !== "undefined")
                        if (useLang.hasOwnProperty(elementsLang[el].textContent.replace("[", "").replace("]", "")))
                            elementsLang[el].textContent = useLang[elementsLang[el].textContent.replace("[", "").replace("]", "")];
                }
            }

            if (languageIndex !== "en") {
                about.getElementById("tw2").textContent = about.getElementById("tw2").textContent.replace("$1", useLang["@metadata"]["langName"]);

                about.getElementById("tw").style.display = "block";
                for (translator in useLang["@metadata"]["authors"]) {
                    if (useLang["@metadata"]["authors"].hasOwnProperty(translator)) {
                        var lit = document.createElement('li');
                        lit.appendChild(document.createTextNode(useLang["@metadata"]["authors"][translator]));
                        about.getElementById('tl').appendChild(lit);
                    }
                }
            } else about.getElementById("tw2").textContent = useLang["about-frame-translators-tw-base"];



            sandwichLocalisation(about, dirLang, about.getElementById("tw2").textContent, about.getElementById('tw2'), "link", 4, "inline", "T", "https://translatewiki.net/wiki/Translating:SWViewer");
            sandwichLocalisation(about, dirLang, useLang['about-frame-app'], about.getElementById('appname'), "name", 3, "inline", "N", false);
            sandwichLocalisation(about, dirLang, useLang['about-frame-doc'], about.getElementById('appdoc'), "$1", 4, "inline", "D", "https://meta.wikimedia.org/wiki/Special:MyLanguage/SWViewer");
            sandwichLocalisation(about, dirLang, useLang['about-frame-q'], about.getElementById('appq'), "$1", 4, "inline", "Q", "https://meta.wikimedia.org/wiki/Talk:SWViewer");
            about.getElementById("localisedElN2").style.fontWeight = 'bold';
            about.getElementById("localisedElQ1").style.marginLeft = '3px';
            about.getElementById('traffic').style.marginLeft = '3px';

            document.getElementById(id).append(about.body);
        })
        .catch(e => console.log(e));
};

const createAboutPO = p => {
    if (document.getElementById('about') === null) {
        p.append(createPO({
            id: 'about',
            header: { title: useLang["about"] },
            content: {
                child: bakeEl({ type: 'div', att: { id: 'abox', class: 'fs-md' } })
            }
        }));
        addAbout('abox');
    }
};

createAboutPO(document.getElementById('angularapp') || document.body);