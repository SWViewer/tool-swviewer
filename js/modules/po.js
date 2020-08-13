const createPO = (po) => {
    return bakeEl({
        type: "div",
        att: { id: po.id, class: "po__base" },
        child: [
            bakeEl({
                type: 'div',
                att: { class: 'po__header action-header' },
                child: (header => {
                    let hChilds = [bakeEl({ type: 'span', child: header.title, att: { class: 'action-header__title fs-lg' } })]
                    if (header.buttons) header.buttons.forEach((btn) => hChilds.push(bakeEl({ 
                        type: 'div', att: { id: btn.id, class: btn.class + " secondary-hover", onclick: btn.onClick, 'aria-label': btn.toolTip, 'i-tooltip': 'bottom-right' }, 
                        child: bakeEl({ type: 'img', att: { class: "touch-ic secondary-icon " + btn.img.class, src: btn.img.src, alt: btn.img.alt } }) 
                    })));
                    hChilds.push(bakeEl({ type: 'div', att: { class: 'mobile-only secondary-hover', onclick: 'closePO()', 'aria-label': 'Close [esc]', 'i-tooltip': 'bottom-right' },
                        child: bakeEl({ type: 'img', att: { class: 'touch-ic secondary-icon', src: './img/cross-filled.svg', alt: 'Cross image' } })
                    }))
                    hChilds.push(bakeEl({ type: 'span', child: 'esc', att: { class: 'desktop-only po__esc secondary-hover fs-md', onclick: 'closePO();' } }));
                    return hChilds;
                })(po.header)
            }),
            bakeEl({
                type: 'div',
                att: { class: 'po__content' },
                child: bakeEl({
                    type: 'div', 
                    att: { class: 'po__content-body secondary-scroll' },
                    child: po.content.child
                })
            })
        ]
    });
};