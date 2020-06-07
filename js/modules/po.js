const createPO = (po) => {
    return bakeEl({
        type: "div",
        att: { id: po.id, class: "po__base" },
        child: [
            bakeEl({
                type: 'div',
                att: { class: 'po__header action-header' },
                child: [
                    bakeEl({ type: 'span', child: po.header.title, att: { class: 'action-header__title fs-lg' } }),
                    bakeEl({ type: 'div', att: { class: 'mobile-only secondary-hover', onclick: 'closePO()', 'aria-label': 'Close [esc]', 'i-tooltip': 'bottom-right' },
                        child: bakeEl({ type: 'img', att: { class: 'touch-ic secondary-icon', src: './img/cross-filled.svg', alt: 'Cross image' } })
                    }),
                    bakeEl({ type: 'span', child: 'esc', att: { class: 'desktop-only po__esc secondary-hover fs-md', onclick: 'closePO();' } })
                ]
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

export { createPO }