createPW({
    id: "talkForm",
    header: {
        title: "Talk",
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
        id: 'talk-content',
        style: 'width: 100%;',
        child: 'htmlelement || or array of them',
        floatbar: {
            onSubmit: 'onclickevent();',
            input: {
                id: 'phrase-send-talk',
                onFocus: 'eventListener()',
                maxLength: '600',
                placeholder: 'Please type here...'
            },
            buttons: [
                {
                    id: 'btn-talk',
                    onClick: 'closePW();',
                    toolTip: 'Close[ese]',
                    img: {
                        class: 'touch-ic__w-free',
                        src: '',
                        alt: 'Cross'    
                    }
                }, {
                    style: 'height: 100%',
                    child: bakeEl()
                }
            ]

        }

    },
    drawer: {
        id: 'talkPWDrawer',
        child: 'htmlelement || or array of them'
    },
    overlay: {
        id: 'talkPWOverlay',
        onClick: 'eventlistener'
    }
});