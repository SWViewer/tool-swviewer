const Guesture = {
    active: null,
    types: ["rightSwipe", "leftSwipe", "bottomSwipe", "topSwipe"],
    onSwipe: function (target, type, callback, isLive) {
        if (Guesture.types.find(guesture => guesture === type) === undefined) {
            console.error(`Guesture type "${type}" is not valid!`);
            return false;
        }

        const minDist = 40;
        const touch = {
            s: { x: null, y: null },
            m: { x: null, y: null, }
        };
        const delta = { x: null, y: null };
        const DIR = { isH: null, isRB: null };

        const calcSwipe = (cx, cy, isEnd) => {
            if (!isEnd && cx !== null && cy !== null) {
                touch.m.x = cx;
                touch.m.y = cy; 
            }
            if (touch.m.x === null || touch.m.y === null || touch.s.x === null || touch.s.y === null) return;

            delta.x = touch.m.x - touch.s.x;
            delta.y = touch.m.y - touch.s.y;

            if (Guesture.active === null && Math.abs(delta.x) < minDist && Math.abs(delta.y) < minDist) return;

            if (DIR.isH === null) {
                DIR.isH = (Math.abs(delta.x) > Math.abs(delta.y))? true: false;
                DIR.isRB = (DIR.isH)? ((delta.x >= 0)? true: false): ((delta.y >= 0)? true: false);
            }

            if (DIR.isH === true && DIR.isRB === true && type === "rightSwipe") callbacker("rightSwipe", (!isEnd)? delta.x: undefined);
            else if (DIR.isH === true && DIR.isRB === false && type === "leftSwipe") callbacker("leftSwipe", (!isEnd)? delta.x: undefined);
            else if (DIR.isH === false && DIR.isRB === true && type === "bottomSwipe") callbacker("bottomSwipe", (!isEnd)? delta.y: undefined);
            else if (DIR.isH === false && DIR.isRB === false && type === "topSwipe") callbacker("topSwipe", (!isEnd)? delta.y: undefined);
            
            function callbacker(gType, m) {
                if (Guesture.active !== null && Guesture.active !== gType) return;

                Guesture.active = gType;
                callback(m);
            }
        }
        if (target.constructor === Array) target.forEach(t => touchSetup(t));
        else touchSetup(target);
        function touchSetup(t) {
            t.addEventListener('touchstart', e => {
                const coords = e.touches[0];
                touch.s.x = Math.floor(coords.clientX);
                touch.s.y = Math.floor(coords.clientY);
            }, false);
            t.addEventListener('touchmove', e => {
                const coords = e.touches[0];
                if (isLive) {
                    calcSwipe(Math.floor(coords.clientX), Math.floor(coords.clientY), false);
                    return;
                }
                touch.m.x = Math.floor(coords.clientX);
                touch.m.y = Math.floor(coords.clientY);
            });
            t.addEventListener('touchend', e => {
                calcSwipe(null, null, true);
                touch.s.x = null; touch.s.y = null;
                touch.m.x = null; touch.m.y = null;
                delta.x = null; delta.y = null;
                DIR.isH = null; DIR.isRB = null;
                Guesture.active = null;
            });
        }
        return true;
    }
}