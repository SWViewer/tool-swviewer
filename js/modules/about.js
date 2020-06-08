const addAbout = (id) => {
    fetch('https://tools.wmflabs.org/swviewer/templates/about.html')
    .then(res => res.text())
    .then(text => {
        let parser = new DOMParser();
        let about = parser.parseFromString(text, 'text/html');
        document.getElementById(id).append(about.body);
    })
    .catch(e => console.log(e));
};

const createAboutPO = p => {
    if (document.getElementById('about') === null) {
        p.append(createPO({
            id: 'about',
            header: { title: "About" },
            content: {
                child: bakeEl({ type: 'div', att: { id: 'abox', class: 'fs-md' } })
            }
        }));
        addAbout('abox');
    }
};

createAboutPO(document.getElementById('angularapp') || document.body);