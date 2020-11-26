const createPW = (pw) => {
    var noDrawerStyle = "";
    if (pw.drawer === undefined) noDrawerStyle = "grid-template-areas: 'pw__header pw__header' 'pw__content pw__content';";

    return bakeEl({ 
        type: "div", att: { id: pw.id, class: "pw__base", style: "display: none;" + noDrawerStyle}, 
        child: ((pw) => {
            // Childrens of pw
            let pwChilds = [
                bakeEl({ 
                    type: 'div', att: { class: "pw__header action-header" },
                     child: ((header) => {
                        // Childrens of header
                        let hChilds = [ 
                            bakeEl({ type: 'div', child: bakeEl({ type: 'img', att: { class: "touch-ic secondary-icon", src: './img/drawer-filled.svg', alt: useLang["pw-img-drawer"] } }), att: { class: 'mobile-only secondary-hover', onclick: 'openSidebar();', 'aria-label': 'Sidebar', 'i-tooltip': `bottom${(dirLang === 'rtl')? 'right': 'left'}` } }), 
                            bakeEl({ type: 'span', child: header.title, att: {class: 'action-header__title fs-xl'} }) 
                        ];
                        header.buttons.forEach((btn) => hChilds.push(bakeEl({ 
                            type: 'div', att: { id: btn.id, class: btn.class + " secondary-hover", onclick: btn.onClick, 'aria-label': btn.toolTip, 'i-tooltip': `bottom-${(dirLang === 'rtl')? 'left': 'right'}` }, 
                            child: bakeEl({ type: 'img', att: { class: "touch-ic secondary-icon " + btn.img.class, src: btn.img.src, alt: btn.img.alt } }) 
                        })));
                        hChilds.push(bakeEl({ type: 'span', child: 'esc', att: { class: 'desktop-only pw__esc secondary-hover fs-md', onclick: 'closePW();' } }));
                        return hChilds;
                     })(pw.header)
                }),
                bakeEl({ 
                    type: 'div', att: { class: 'pw__content' }, 
                    child: ((content) => {
                        // Childrens of content
                        let cChilds = [ bakeEl({ type: 'div', child: content.child, att: { id: content.id, class: 'pw__content-body secondary-scroll', style: content.style } }) ];
                        if (content.floatbar !== undefined) {
                            cChilds.push(bakeEl({ 
                                type: 'div', att: { class: 'pw__floatbar'}, 
                                child: ((floatbar) => {
                                    // Childrens of floatbar
                                    let fChilds = [
                                        bakeEl({ 
                                            type: 'form', att: { id: floatbar.id, onSubmit: floatbar.onSubmit },
                                            child: bakeEl({ type: 'input', att: { id: floatbar.input.id, class: 'secondary-placeholder fs-md', autocomplete: 'off', onfocus: floatbar.input.onFocus, 'max-length': floatbar.input.maxLength, placeholder: floatbar.input.placeholder } })
                                        })
                                    ];
                                    floatbar.buttons.forEach((btn) => {
                                        fChilds.push(bakeEl({ type: 'span', att: { 'vr-line': '' } }));
                                        fChilds.push(bakeEl({ 
                                            type: 'div', att: { id:  btn.id, class: 'secondary-hover', style: btn.style, onclick: btn.onClick, 'aria-label': btn.toolTip, 'i-tooltip': `top-${(dirLang === 'rtl')? 'left': 'right'}`}, 
                                            child: ((btn) => {
                                                if (btn.child !== undefined) return btn.child;
                                                if (btn.img !== undefined) return bakeEl({ type: 'img', att: { class: 'touch-ic secondary-icon ' + btn.img.class, src: btn.img.src, alt: btn.img.alt } });
                                            })(btn)
                                        }));
                                    })
                                    return fChilds;
                                })(content.floatbar)
                            }));
                        }
                        return cChilds;
                    })(pw.content)
                })
            ]
            //Create Drawer
            if (pw.drawer !== undefined) {
                pwChilds.push(bakeEl({ type: 'div', child: pw.drawer.child, att: { id: pw.drawer.id, class: 'pw__drawer secondary-scroll' } }));
                pwChilds.push(bakeEl({ type: 'div',  att: { id: pw.overlay.id, class: 'pw__overlay', onclick: pw.overlay.onClick } }));
            }
            return pwChilds;
        })(pw) 
    });
};
