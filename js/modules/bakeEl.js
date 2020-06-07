const bakeEl = (e) => {
    if (e.type === undefined) return console.error('Must provide type to bakeEl.')
    let el = document.createElement(e.type);
    if (typeof e.att === 'object') Object.keys(e.att).forEach((k) => { if (typeof e.att[k] === 'string') el.setAttribute(k, e.att[k]) });
    if (typeof e.child === 'object' && e.child instanceof Node && e.child instanceof HTMLElement) el.appendChild(e.child);
    else if (typeof e.child === 'string') el.appendChild(document.createTextNode(e.child));
    else if (Array.isArray(e.child)) {
        e.child.forEach((i) => {
            if (typeof i === 'object' && i instanceof Node && i instanceof HTMLElement) el.appendChild(i);
            else if (typeof i === 'string') el.appendChild(document.createTextNode(i));
        });
    }
    return el;
}