createPO({
    id: 'about',
    header: {
        title: "About",
        buttons: [
            {
                class: 'mobile-only',
                onClick: 'closePW();',
                toolTip: 'Close[ese]',
                img: {
                    class: '',
                    src: '',
                    alt: 'Cross'    
                }
            }
        ]
    },
    content: {
        child: 'htmlelement || or array of them'
    }
});