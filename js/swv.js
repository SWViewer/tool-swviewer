var old;
var times = 1;
var i = 0;
var edits_history = [];
angular.module("swv", ["ui.directives", "ui.filters"])
.controller("Queue", function ($scope, $compile, $timeout) {
    var server_url, server_name, script_path, server_uri, namespace, user, dnew, title, wikidatat, userip, wiki,
        timestamp, summary, withoutSection, speedySummary, othersArray, protectArray, reportArray, speedySection,
        speedyWarnSummary, warn;
    const ns = ["<font color='green'>Main</font>", "<font color='tomato'>Talk</font>", "<font color='tomato'>User</font>", "<font color='tomato'>User talk</font>", "<font color='orange'>Project</font>", "<font color='tomato'>Project talk</font>", "<font color='orange'>File</font>", "<font color='tomato'>File talk</font>", "<font color='tomato'>MediaWiki</font>", "<font color='tomato'>MediaWiki talk</font>", "<font color='orange'>Template</font>", "<font color='tomato'>Template talk</font>", "<font color='orange'>Help</font>", "<font color='tomato'>Help talk</font>", "<font color='orange'>Category</font>", "<font color='tomato'>Category talk</font>"];
    const wikis = ["afwiki", "alswiki", "amwiki", "anwiki", "angwiki", "arwikisource", "arwikiversity", "aswikisource", "astwiki", "avwiki", "azwiki", "wikisource", "wikisourcewiki", "sourceswiki", "bat_smgwiki", "bgwikibooks", "brwiki", "bswiki", "bswikiquote", "cawikiquote", "cswiktionary", "csbwiki", "csbwiktionary", "cywiki", "dawikisource", "dewikisource", "dinwiki", "diqwiki", "elwikiquote", "elwikisource", "enwikiversity", "eowikinews", "eowiki", "eowikisource", "eswiktionary", "etwikibooks", "fawikiquote", "fawikivoyage", "fiwikisource", "frwikinews", "frpwiki", "gawiki", "glwikibooks", "hewikibooks", "hewikiquote", "hrwikisource", "hrwiktionary", "htwiki", "huwikibooks", "huwikiquote", "huwiktionary", "hywiktionary", "iawiktionary", "idwikiquote", "iewiktionary", "iowiki", "iswiktionary", "jvwiki", "kawiki", "kkwiki", "kowikibooks", "kowikisource", "kuwiki", "kywiki", "lawiki", "lawikisource", "lawiktionary", "ladwiki", "lbwiki", "liwiki", "lmowiki", "ltgwiki", "lvwiki", "maiwiki", "map_bmswiki", "metawiki", "mgwiki", "mkwikibooks", "mlwiki", "mlwiktionary", "mrwikisource", "mrjwiki", "mswiktionary", "ndswiktionary", "nds_nlwiki", "newwiki", "nlwikisource", "nowikisource", "orwikisource", "pamwiki", "pdcwiki", "plwikinews", "plwikiquote", "pnbwiktionary", "pswiki", "rmywiki", "rowikinews", "ruewiki", "sawiki", "sawiktionary", "sdwiktionary", "siwiki", "skwiktionary", "sqwiki", "srwikisource", "stwiktionary", "stqwiki", "svwikibooks", "svwikinews", "svwikiversity", "tawikinews", "tawiki", "tewiki", "ttwiki", "tyvwiki", "ugwiki", "urwiki", "vepwiki", "vlswiki", "yiwiktionary", "zhwikiquote", "zh_classicalwiki", "zh_yuewiki", "abwiki", "adywiki", "afwiktionary", "angwiktionary", "arcwiki", "aywiki", "aywiktionary", "barwiki", "be_x_oldwiki", "be_taraskwiki", "biwiki", "bnwikisource", "bswikisource", "cawikibooks", "crwiki", "crhwiki", "cuwiki", "cvwiki", "dewikibooks", "dewiktionary", "dvwiki", "dvwiktionary", "eewiki", "eowikibooks", "eswikibooks", "eswikinews", "eswikiquote", "eswikisource", "eswikiversity", "euwikiquote", "fawiktionary", "fjwiki", "fowiktionary", "fywiki", "glwiktionary", "guwiki", "hewikisource", "hewiktionary", "hiwiktionary", "hsbwiki", "hsbwiktionary", "huwikisource", "hywikibooks", "iawiki", "idwikibooks", "iewiki", "jbowiki", "kawiktionary", "klwiki", "kmwikibooks", "kmwiktionary", "knwiki", "kuwikiquote", "kvwiki", "kywikibooks", "ltwikisource", "ltwiktionary", "lvwiktionary", "mediawikiwiki", "mznwiki", "nawiktionary", "nahwiktionary", "ndswiki", "newiki", "newiktionary", "nlwiktionary", "nnwikiquote", "nnwiktionary", "ocwiki", "outreachwiki", "pagwiki", "papwiki", "piwiki", "plwikibooks", "ptwikibooks", "ptwikinews", "ptwikiquote", "ptwikisource", "ptwikivoyage", "ptwiktionary", "quwiki", "rmwiki", "ruwikiquote", "ruwikisource", "rwwiki", "sawikisource", "sahwikisource", "scowiki", "skwikibooks", "skwiki", "skwikiquote", "slwikibooks", "slwiki", "snwiki", "sowiki", "sqwikibooks", "sqwikiquote", "stwiki", "suwiktionary", "swwiktionary", "tawikiquote", "tawikisource", "tgwiki", "tgwiktionary", "thwikibooks", "thwikiquote", "thwikisource", "thwiktionary", "tkwiki", "tnwiki", "towiki", "tpiwiki", "twwiki", "tywiki", "udmwiki", "ukwikiquote", "uzwiki", "viwikibooks", "viwikisource", "vowiki", "vowiktionary", "wowiki", "xhwiki", "yiwikisource", "yowiki", "zh_min_nanwiki", "amwiktionary", "arwikinews", "arwiktionary", "astwiktionary", "bewikisource", "betawikiversity", "bmwiki", "bnwiki", "brwiktionary", "bswikibooks", "bswiktionary", "bxrwiki", "cawiktionary", "cswikinews", "cswikiquote", "cswikiversity", "dawikibooks", "dewikinews", "dewikiquote", "dtywiki", "enwikibooks", "enwikiquote", "etwiki", "etwikiquote", "euwikibooks", "extwiki", "fawikibooks", "ffwiki", "fiwikinews", "fiwiktionary", "fjwiktionary", "frwikibooks", "frwikiversity", "ganwiki", "gdwiki", "glwikiquote", "gnwiktionary", "gotwiki", "guwikisource", "gvwiki", "hewikinews", "hiwikibooks", "hiwiki", "hifwiki", "hrwiki", "iawikibooks", "idwikisource", "ikwiki", "incubatorwiki", "jamwiki", "kaawiki", "kabwiki", "kbdwiki", "kgwiki", "kiwiki", "knwikiquote", "kswiki", "kuwikibooks", "kuwiktionary", "kwwiki", "kywikiquote", "liwikisource", "ltwikibooks", "ltwikiquote", "mdfwiki", "mgwikibooks", "miwiki", "mlwikiquote", "mrwikibooks", "mrwikiquote", "mrwiktionary", "mtwiki", "myvwiki", "nlwikibooks", "nowikibooks", "nowiktionary", "novwiki", "nvwiki", "olowiki", "omwiki", "oswiki", "plwikisource", "plwikivoyage", "ptwikiversity", "quwiktionary", "rowikiquote", "rowikisource", "ruwikibooks", "ruwikimedia", "ruwikinews", "ruwiktionary", "sahwiki", "sewiki", "sgwiki", "slwiktionary", "sqwikinews", "srwikibooks", "srwikinews", "srnwiki", "sswiki", "suwiki", "svwikiquote", "svwikisource", "swwiki", "szlwiki", "szywiki", "shywiktionary", "tcywiki", "tewikisource", "tewiktionary", "thwiki", "tiwiki", "tlwiki", "ttwiktionary", "ukwikinews", "ukwiktionary", "urwikiquote", "wuuwiki", "xalwiki", "zhwikibooks", "acewiki", "afwikiquote", "anwiktionary", "arwikibooks", "azwikiquote", "azwiktionary", "bewiki", "bewiktionary", "bgwiki", "bgwikiquote", "bgwiktionary", "bjnwiki", "bpywiki", "brwikisource", "bswikinews", "bugwiki", "cdowiki", "chrwiki", "chywiki", "ckbwiki", "cswikibooks", "cswikisource", "dawiktionary", "dsbwiki", "elwikinews", "elwikiversity", "elwiktionary", "eowiktionary", "etwikisource", "etwiktionary", "fawikinews", "fiwikibooks", "fiwikiversity", "fiwikivoyage", "fiu_vrowiki", "fowiki", "fowikisource", "fywikibooks", "gagwiki", "glkwiki", "guwikiquote", "guwiktionary", "hawiki", "hywiki", "hywikiquote", "idwiktionary", "ilowiki", "iowiktionary", "iswiki", "jvwiktionary", "kawikibooks", "kkwikibooks", "kkwiktionary", "kmwiki", "kowikiversity", "koiwiki", "krcwiki", "kwwiktionary", "lezwiki", "lgwiki", "liwikibooks", "liwiktionary", "lnwiktionary", "mhrwiki", "mnwiki", "mnwwiki", "mnwiktionary", "mswikibooks", "mwlwiki", "newikibooks", "nlwikiquote", "nowikiquote", "nsowiki", "nycwikimedia", "orwiktionary", "pawikibooks", "pawikisource", "pflwiki", "pihwiki", "plwiktionary", "pnbwiki", "pntwiki", "pswiktionary", "rowiktionary", "ruwikiversity", "rwwiktionary", "sawikibooks", "sawikiquote", "sdwiki", "siwikibooks", "siwiktionary", "slwikiquote", "slwikiversity", "specieswiki", "srwiktionary", "suwikiquote", "svwiktionary", "tawikibooks", "tewikiquote", "tgwikibooks", "trwikibooks", "trwikimedia", "trwikinews", "trwikiquote", "trwikisource", "trwiktionary", "tswiki", "ukwikibooks", "uzwikiquote", "vewiki", "vecwiktionary", "viwikiquote", "viwiktionary", "wawiki", "xmfwiki", "yiwiki", "zeawiki", "zhwikinews", "zhwikisource", "zh_min_nanwiktionary", "afwikibooks", "akwiki", "arwikiquote", "arzwiki", "aswiki", "azwikibooks", "azwikisource", "bawiki", "bclwiki", "bewikibooks", "bewikiquote", "bgwikisource", "bhwiki", "bnwikibooks", "bnwiktionary", "bowiki", "brwikiquote", "cawikinews", "cawikisource", "cbk_zamwiki", "cewiki", "chwiki", "chrwiktionary", "cowiki", "cvwikibooks", "cywikibooks", "cywikiquote", "cywikisource", "cywiktionary", "dawikiquote", "dewikiversity", "dkwikimedia", "dzwiki", "elwikibooks", "eowikiquote", "euwiki", "euwiktionary", "fawikisource", "fiwikiquote", "frwikiquote", "frrwiki", "fywiktionary", "gawiktionary", "gdwiktionary", "glwiki", "glwikisource", "gnwiki", "gvwiktionary", "hawiktionary", "hakwiki", "hawwiki", "hiwikiquote", "hiwikisource", "hrwikibooks", "hrwikiquote", "hywikisource", "igwiki", "iswikibooks", "iswikiquote", "iswikisource", "iuwiki", "iuwiktionary", "jbowiktionary", "kawikiquote", "klwiktionary", "knwikisource", "knwiktionary", "kowikinews", "kowikiquote", "kowiktionary", "kswiktionary", "kshwiki", "kywiktionary", "lawikibooks", "lawikiquote", "lbwiktionary", "lbewiki", "liwikiquote", "lnwiki", "lowiki", "lowiktionary", "ltwiki", "miwiktionary", "minwiki", "mkwikisource", "mkwiktionary", "mlwikibooks", "mlwikisource", "mswiki", "mtwiktionary", "mywiki", "mywiktionary", "nawiki", "nahwiki", "nlwikimedia", "nowikinews", "nrmwiki", "nywiki", "ocwikibooks", "ocwiktionary", "omwiktionary", "orwiki", "pawiki", "pawiktionary", "pcdwiki", "plwikimedia", "rnwiki", "rowikibooks", "roa_rupwiki", "roa_rupwiktionary", "sgwiktionary", "shwiktionary", "skwikisource", "slwikisource", "smwiki", "gcrwiki", "smwiktionary", "sowiktionary", "sqwiktionary", "srwikiquote", "sswiktionary", "tawiktionary", "tewikibooks", "tetwiki", "tiwiktionary", "tkwiktionary", "tlwikibooks", "tlwiktionary", "tnwiktionary", "tpiwiktionary", "tswiktionary", "ttwikibooks", "tumwiki", "uawikimedia", "nqowiki", "ugwiktionary", "ukwikisource", "urwikibooks", "urwiktionary", "uzwiktionary", "wawiktionary", "wowikiquote", "wowiktionary", "zawiki", "zh_min_nanwikisource", "zuwiki", "zuwiktionary", "arwikimedia", "bdwikimedia", "bewikimedia", "brwikimedia", "cawikimedia", "cowikimedia", "eewikimedia", "fiwikimedia", "mkwikimedia", "mxwikimedia", "nowikimedia", "sewikimedia", "ptwikimedia", "bawikibooks", "itwikibooks", "itwikinews", "jawikinews", "nlwikinews", "liwikinews", "furwiki", "lijwiki", "roa_tarawiki", "scwiki", "lrcwiki", "gomwiki", "atjwiki", "banwiki", "kbpwiki", "gorwiki", "inhwiki", "lfnwiki", "satwiki", "shnwiki", "jawikiquote", "sahwikiquote", "jawikisource", "vecwikisource", "euwikisource", "pmswikisource", "itwikiversity", "jawikiversity", "hiwikiversity", "zhwikiversity", "frwikivoyage", "itwikivoyage", "nlwikivoyage", "ruwikivoyage", "svwikivoyage", "eswikivoyage", "rowikivoyage", "elwikivoyage", "hewikivoyage", "ukwikivoyage", "viwikivoyage", "zhwikivoyage", "hiwikivoyage", "bnwikivoyage", "pswikivoyage", "cowiktionary", "hifwiktionary", "yuewiktionary", "wikimaniawiki"];
    const active_users = ["alswikibooks", "jawikibooks", "enwikinews", "cebwiki", "emlwiki", "mkwiki", "napwiki", "nnwiki", "pmswiki", "scnwiki", "shwiki", "vecwiki", "warwiki", "azbwiki", "alswikiquote", "itwikiquote", "frwikisource", "itwikisource", "dewikivoyage", "alswiktionary", "itwiktionary", "jawiktionary", "mgwiktionary", "mowiktionary", "scnwiktionary", "simplewiktionary", "zhwiktionary"];
    var vandalsReport = [];
    $scope.descriptions = config["wikis"][0]["others"][0]["rollback"];
    $scope.speedys = config["wikis"][0]["others"][0]["speedy"];
    $scope.offlineUsers = offlineUsers;
    $scope.users = [];
    $scope.project_url = "";
    $scope.user = "";
    $scope.title = "";
    var countConnectAttemp = 0;
    var checkWarn = false;
    var checkWarnDelete = false;
    var isdelete = false;
    var warnDelete = null;


    $scope.select = function (edit) {
        if (!$scope.recentChange.isConnected) $scope.recentChange.connect();
        uiEnableNew();
        firstClickEdit = true;
        document.getElementById("queue").classList.add("disabled"); // disable queue during change diff
        homeBtn(false);
        if (document.getElementById('eqBody').classList.contains('eq__body__active')) toggleMDrawer();
        if ((typeof user !== "undefined") && (i === 0)) {
            if (edits_history.length === 6)
                edits_history.splice(5, 1);
            var eh = {
                "server_url": server_url,
                "server_name": server_name,
                "script_path": script_path,
                "server_uri": server_uri,
                "wiki": wiki,
                "namespace": namespace,
                "user": user,
                "old": old,
                "dnew": dnew,
                "title": title,
                "userip": userip,
                "wikidatat": wikidatat,
                "summary": summary
            };
            edits_history.unshift(eh);
        }
        i = 0;
        nextDiffStyle();
        $scope.selected = edit;
        server_url = edit.server_url;
        server_name = edit.server_name;
        script_path = edit.script_path;
        server_uri = edit.server_uri;
        wiki = edit.wiki;
        namespace = edit.namespace;
        user = edit.user;
        old = edit.old;
        dnew = edit['new'];
        title = edit.title;
        userip = edit.isIp;
        summary = edit.comment;
        $scope.user = user;
        $scope.title = title;
        $scope.project_url = server_url + script_path;
        wikidatat = edit.wikidata_title;
        changeRollbacksDescription(wiki);
        SHOW_DIFF(edit.server_url, edit.server_name, edit.script_path, edit.server_uri, edit.wiki, edit.namespace, edit.user, edit.old, edit['new'], edit.title, edit.isIp, edit.comment, wikidatat, true);
        $scope.edits.splice($scope.edits.indexOf(edit), 1);
    };
    $scope.Back = function () {
        if (edits_history.length > 0 && edits_history.length - 1 >= i) {
            uiEnableNew();
            homeBtn(false);
            if (i === 0) {
                if (edits_history.length === 6) edits_history.splice(5, 1);
                var eh = {
                    "server_url": server_url,
                    "server_name": server_name,
                    "script_path": script_path,
                    "server_uri": server_uri,
                    "wiki": wiki,
                    "namespace": namespace,
                    "user": user,
                    "old": old,
                    "dnew": dnew,
                    "title": title,
                    "userip": userip,
                    "summary": summary,
                    "wikidatat": wikidatat
                };
                edits_history.unshift(eh);
                i = i + 1;
            }
            server_url = edits_history[i]["server_url"];
            server_name = edits_history[i]["server_name"];
            script_path = edits_history[i]["script_path"];
            server_uri = edits_history[i]["server_uri"];
            wiki = edits_history[i]["wiki"];
            namespace = edits_history[i]["namespace"];
            user = edits_history[i]["user"];
            old = edits_history[i]["old"];
            dnew = edits_history[i]["dnew"];
            title = edits_history[i]["title"];
            userip = edits_history[i]["userip"];
            summary = edits_history[i]["summary"];
            wikidatat = edits_history[i]["wikidatat"];
            $scope.user = user;
            $scope.title = title;
            $scope.project_url = server_url + script_path;
            changeRollbacksDescription(wiki);
            SHOW_DIFF(server_url, server_name, script_path, server_uri, wiki, namespace, user, old, dnew, title, userip, summary, wikidatat, true);
            i = i + 1;
        }
    };
    $scope.editColor = function (edit) {
        if (vandals.indexOf(edit.user) !== -1 || (vandals.indexOf(edit.user) !== -1 && suspects.indexOf(edit.user) !== -1))
            return {color: "red"};
        else if (suspects.indexOf(edit.user) !== -1)
            return {color: "pink"};
    };
    $scope.descriptionColor = function (description) {
        if (checkWarn === true && description.warn !== null && typeof description.warn !== "undefined" && description.warn !== "")
            return {color: "var(--tc-positive)"};
        else {
            if (description.global === true) {
                return {color: "var(--tc-secondary)"};
            }
            if (typeof description.global == "undefined")
                return {color: "var(--tc-secondary-low)"};
        }
    };
    $scope.speedyColor = function (speedy) {
        if (checkWarnDelete === true && speedy.warn !== null && typeof speedy.warn !== "undefined" && speedy.warn !== "")
            return {color: "var(--tc-positive)"};
        else
            return {color: "var(--link-color)"};
    };

    $scope.browser = function () {
        if (typeof dnew !== "undefined") {
            var urlbrowser;
            if (old !== null)
                urlbrowser = server_url + script_path + "/index.php?diff=" + dnew + "&oldid=" + old + "&uselang=en&redirect=no&mobileaction=toggle_view_desktop";
            else
                urlbrowser = server_uri + "?uselang=en&redirect=no&mobileaction=toggle_view_desktop";
            var diffWindow = window.open(urlbrowser, "_blank");
            diffWindow.location;
            diffWindow.focus();
        }
    };

    $scope.SD = function (tmpl, summary) {
        isdelete = true;
        var dtext = document.getElementById('textpage').value;
        document.getElementById('textpage').value = tmpl.replace(/\$1/gi, userSelf) + dtext;
        document.getElementById('summaryedit').value = summary;
        setTimeout($scope.doEdit(), 500);
    };

    $scope.checkEdit = function () {
        if (typeof dnew == "undefined")
            return;
        document.getElementById("speedyReasonsBox").classList.add("disabled");
        document.getElementById('warn-box-delete').parentElement.parentElement.classList.add('disabled');

        $scope.speedys.forEach(function (elS) {
            if (typeof elS.warn !== "undefined")
                document.getElementById('warn-box-delete').parentElement.parentElement.classList.remove('disabled');
        });

        if (defaultDeleteList.indexOf(wiki) !== -1) {
            document.getElementById('warn-box-delete').classList.add('t-btn__active');
            checkWarnDelete = true;
        } else {
            document.getElementById('warn-box-delete').classList.remove('t-btn__active');
            checkWarnDelete = false;
        }

        document.getElementById("editFormBody").classList.add("disabled");
        document.getElementById('textpage').value = "";
        document.getElementById('btn-group-delete').style.display = "block";

        $scope.isCURRENT(server_url, script_path, title, dnew, old, function (cb) {
            if (cb == null || cb === false)
                return;
            var url = "php/getPage.php";
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'text',
                data: {
                    server: server_url + script_path,
                    oldid: dnew
                },
                success: function (datapage) {
                    if (datapage === "Error! Loading page is not success") {
                        document.getElementById("speedyReasonsBox").classList.remove("disabled");
                        alert('Failed... dev code: 004.1. Failed http-request. Maybe page was delete or server is down.');
                    } else {
                        document.getElementById('textpage').value = "";
                        document.getElementById('summaryedit').value = "";
                        document.getElementById('textpage').value = datapage;
                        // document.getElementById('textpage').focus(); //it create a lag in mobile ui animation
                        document.getElementById('textpage').scrollTop = 0;
                        document.getElementById("editFormBody").classList.remove("disabled");
                        document.getElementById("speedyReasonsBox").classList.remove("disabled");
                    }
                }, error: function (error) {
                    document.getElementById("speedyReasonsBox").classList.remove("disabled");
                    alert('Failed... dev code: 004; error code: ' + error.status + '.');
                    document.getElementById("editFormBody").classList.remove("disabled");
                }
            });
        });
    };

    $scope.doEdit = function () {
        var isdeletetmp = false;
        if (isdelete === true) {
            isdelete = false;
            isdeletetmp = true;
        }
        if ((document.getElementById('textpage').value == null) || (typeof document.getElementById('textpage').value == "undefined"))
            return;
        uiDisable();
        var textpage = document.getElementById('textpage').value;
        var summaryEdit = "";
        if ((document.getElementById('summaryedit').value !== "") && (document.getElementById('summaryedit').value !== null) && (typeof document.getElementById('summaryedit').value !== "undefined"))
            summaryEdit = document.getElementById('summaryedit').value;
        document.getElementById('textpage').value = "";
        document.getElementById('summaryedit').value = "";
        closePW();
        $.ajax({
            url: 'php/doEdit.php',
            type: 'POST',
            crossDomain: true,
            dataType: 'text',
            data: {
                project: server_url + script_path + "/api.php",
                wiki: wiki,
                isdelete: isdeletetmp,
                page: title,
                text: textpage,
                summary: summaryEdit,
                basetimestamp: timestamp
            },
            success: function (dataedit) {
                dataedit = JSON.parse(dataedit);
                if (dataedit["result"] === "Success") {
                    if (isdeletetmp === true) {
                        suspects.push(user);
                        var rawSend = {"type": "synch", "wiki": wiki, "nickname": user, "vandal": "2", "page": title};
                        connectTalk.talkSendInside(rawSend);
                    }
                    if (isdeletetmp === true && checkWarnDelete === true && warnDelete !== null && speedyWarnSummary !== null) {
                        $.ajax({
                            url: 'php/doEdit.php', type: 'POST',
                            beforeSend: function (xhr) {
                                xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Warns');
                            },
                            crossDomain: true, dataType: 'text',
                            data: {
                                getfirstuser: 1,
                                warn: 1,
                                project: server_url + script_path + "/api.php",
                                wiki: wiki,
                                page: title,
                                text: "1",
                                user: user,
                                summary: "1"
                            },
                            success: function (datafirstuser) {
                                if (datafirstuser !== null && datafirstuser !== "") {
                                    datafirstuser = JSON.parse(datafirstuser);
                                    if (datafirstuser["result"] === "sucess") {
                                        $.ajax({
                                            url: 'php/doEdit.php', type: 'POST',
                                            beforeSend: function (xhr) {
                                                xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Warns');
                                            },
                                            crossDomain: true, dataType: 'text',
                                            data: {
                                                warn: "speedy",
                                                project: server_url + script_path + "/api.php",
                                                wiki: wiki,
                                                page: "User_talk:" + datafirstuser["user"],
                                                text: warnDelete.replace(/\$1/gi, title).replace(/\$2/gi, user).replace(/\$3/gi, userSelf),
                                                sectiontitle: speedySection,
                                                summary: speedyWarnSummary.replace(/\$1/gi, title)
                                            },
                                            success: function () {
                                                $scope.reqEnd(dataedit);
                                            },
                                            error: function () {
                                                $scope.reqEnd(dataedit);
                                            }
                                        });
                                    } else
                                        $scope.reqEnd(dataedit);
                                } else
                                    $scope.reqEnd(dataedit);
                            },
                            error: function () {
                                $scope.reqEnd(dataedit);
                            }
                        });
                    } else
                        $scope.reqEnd(dataedit);
                } else {
                    if (dataedit['code'] === "undofailure" || dataedit['code'] === "editconflict" || dataedit['code'] === "alreadyrolled") {
                        $scope.isCURRENT(server_url, script_path, title, dnew, old, function (cb2) {
                            if (cb2 == null) {
                                uiEnable();
                                return;
                            }
                            if (cb2 === false) {
                                uiEnable();
                                return;
                            }
                            document.getElementById('page').srcdoc = starterror + "Edit error: " + escapeXSS(dataedit['result']) + enderror;
                            uiEnable();
                        });
                    } else {
                        // if null-edit
                        if (dataedit['result'] == null || dataedit['code'] === "alreadydone") {
                            document.getElementById('page').srcdoc = starterror + "Such changes has already been made." + enderror;
                            $scope.isCURRENT(server_url, script_path, title, dnew, old, function (cb3) {
                                if (cb3 == null) {
                                    uiEnable();
                                    return;
                                }
                                if (cb3 === false) {
                                    uiEnable();
                                    return;
                                }
                                document.getElementById('page').srcdoc = starterror + "Such changes has already been made." + enderror;
                                uiEnable();
                            });
                        } else {
                            document.getElementById('page').srcdoc = starterror + "Edit error: " + escapeXSS(dataedit['result']) + enderror;
                            uiEnable();
                        }
                    }
                }
            }, error: function (error, e2) {
                document.getElementById('page').srcdoc = starterror + "Failed... dev code: 007; error code: " + escapeXSS(error.status) + escapeXSS(e2) + enderror;
                uiEnable();
            }
        });
    };


    $scope.customRevertSummary = function () {
        if ((old !== null) && (isNaN(old) === false)) {
            document.getElementById('credit').value = "";
            openPO('customRevert');

            if (warn !== null && typeof warn !== "undefined") {
                document.getElementById('warn-box').parentElement.parentElement.classList.remove('disabled');
                if (defaultWarnList.indexOf(wiki) !== -1) {
                    document.getElementById('warn-box').classList.add('t-btn__active');
                    checkWarn = true;
                } else {
                    document.getElementById('warn-box').classList.remove('t-btn__active');
                    checkWarn = false;
                }
            } else {
                document.getElementById('warn-box').parentElement.parentElement.classList.add('disabled');
            }
            // document.getElementById('credit').focus(); //it create a lag in mobile ui animation
        }
    };

    $scope.selectDescription = function (description) {
        withoutSection = false;
        if (description.hasOwnProperty("summary"))
            if (description.summary !== null && description.summary !== "")
                if (checkWarn === true && warn !== null && typeof description.warn !== "undefined" && typeof warn[description.warn] !== "undefined") {
                    if (typeof description.withoutSection !== "undefined")
                        if (description.withoutSection === true)
                            withoutSection = true;
                    $scope.Revert(description.summary, description.warn);
                } else
                    $scope.Revert(description.summary, null);
    };

    $scope.selectSpeedy = function (speedy) {
        if (speedy.hasOwnProperty("template"))
            if (speedy.template !== null && speedy.template !== "") {
                warnDelete = null;
                speedySection = null;
                if (typeof speedy.warn !== "undefined" && speedy.warn !== null && speedy.warn !== "")
                    warnDelete = speedy.warn;
                if (typeof speedy.sectionWarn !== "undefined" && speedy.sectionWarn !== null && speedy.sectionWarn !== "")
                    speedySection = speedy.sectionWarn.replace(/\$1/gi, title);
                $scope.SD(speedy.template, speedySummary);
            }
    };

    $scope.requestsForm = function () {
        // global block start
        var lineReport = [];
        vandalsReport.forEach(function (el, eln) {
            if (el["user"] === user) {
                var n;
                if (el["newid"] !== null) {
                    n = {
                        header: el['wiki'],
                        project: el['project'],
                        line: "<a href='" + el['project'] + "/wiki/Special:Diff/" + el['oldid'] + "/" + el['newid'] + "' target='_blank'>" + eln + "</a>"
                    };
                    lineReport.push(n);
                } else {
                    n = {
                        header: el['wiki'],
                        project: el['project'],
                        line: "<a href='" + el['project'] + "/w/index.php?oldid=" + el['oldid'] + "'' target='_blank'>" + eln + "</a>"
                    };
                    lineReport.push(n);
                }
            }
        });
        var headersReport = [];
        var wikisReport = [];
        var reportHeader = "";
        var wikiReport = "";
        lineReport.forEach(function (el, eln) {
            createCB(eln, el["line"], "reportDiffs");
            if (headersReport.indexOf(el["header"]) === -1) {
                headersReport.push(el["header"]);
                wikisReport.push(el["project"]);
            }
        });
        headersReport.forEach(function (header) {
            reportHeader += header + ", ";
        });
        reportHeader = reportHeader.slice(0, -2);
        wikisReport.forEach(function (p, pp) {
            wikiReport += "[" + p + "/wiki/Special:Contribs/" + encodeURIComponent(user).replace(/'/g, '%27') + " " + headersReport[pp] + "], ";
        });
        wikiReport = wikiReport.slice(0, -2);
        document.getElementById("reportHeader").value = reportHeader;
        if (wikisReport.length === 0)
            document.getElementById("reportComment").value = "See contribs: [" + server_url + "/wiki/Special:Contribs/" + encodeURIComponent(user).replace(/'/g, '%27') + " " + wiki + "].";
        else
            document.getElementById("reportComment").value = "See contribs: " + wikiReport + ".";
        // global block end


        // local protect start
        var checkProtect = false;
        if (protectArray !== null)
            if (checkKey("pageProtect", wiki, protectArray))
                if (checkKey("regexProtect", wiki, protectArray))
                    if (checkKey("sectionProtect", wiki, protectArray) || (checkKey("withoutSectionProtect", wiki, protectArray) && protectArray["withoutSectionProtect"] === true))
                        if (checkKey("textProtect", wiki, protectArray))
                            if (checkKey("summaryProtect", wiki, protectArray))
                                checkProtect = true;
        if (checkProtect === false) {
            document.getElementById("protectDiffsLocal").classList.add("disabled");
        } else {
            document.getElementById("protectCommentLocal").value = protectArray["textProtect"].replace(/\$1/gi, title);
            if (checkKey("withoutSectionProtect", wiki, protectArray) && protectArray["withoutSectionProtect"] === true)
                document.getElementById("protectHeaderLocal").classList.add("disabled");
            else {
                document.getElementById("protectHeaderLocal").value = protectArray["sectionProtect"].replace(/\$1/gi, title);
                document.getElementById("protectHeaderLocal").classList.remove("disabled");
            }
            document.getElementById("protectDiffsLocal").classList.remove("disabled");
        }
        // local protect end


        // local report start
        var checkReportNotAuto = false;
        if (reportArray !== null)
            if (checkKey("pageReport", wiki, reportArray))
                if (checkKey("regexReport", wiki, reportArray))
                    if (checkKey("sectionReport", wiki, reportArray) || (checkKey("withoutSectionReport", wiki, reportArray) && reportArray["withoutSectionReport"] === true))
                        if (checkKey("textReport", wiki, reportArray))
                            if (checkKey("summaryReport", wiki, reportArray))
                                checkReportNotAuto = true;
        if (checkReportNotAuto === false) {
            document.getElementById("reportDiffsLocal").classList.add("disabled");
        } else {
            document.getElementById("reportCommentLocal").value = reportArray["textReport"].replace(/\$1/gi, user);
            if (checkKey("withoutSectionReport", wiki, reportArray) && reportArray["withoutSectionReport"] === true)
                document.getElementById("reportHeaderLocal").classList.add("disabled");
            else {
                document.getElementById("reportHeaderLocal").value = reportArray["sectionReport"].replace(/\$1/gi, user);
                document.getElementById("reportHeaderLocal").classList.remove("disabled");
            }
            document.getElementById("reportDiffsLocal").classList.remove("disabled");
        }
        // local report end

        // SRM start
        var checkSRM = true;

        if (checkSRM === false)
            document.getElementById("othersDiffsGlobal").classList.add("disabled");
        else {
            var textSRM = othersArray["SRM"][0]["text"].replace(/\$1/gi, user).replace(/\$2/gi, title).replace(/\$3/gi, wiki).replace(/\$4/gi, "\n");
            if (userip === "registered")
                textSRM = textSRM.replace(/\$5/gi, "sultool");
            if (userip === "ip")
                textSRM = textSRM.replace(/\$5/gi, "luxotool");
            document.getElementById("othersCommentGlobal").value = textSRM;
            document.getElementById("othersHeaderGlobal").value = othersArray["SRM"][0]["section"].replace(/\$1/gi, wiki);
            document.getElementById("othersDiffsGlobal").classList.remove("disabled");
        }
        // SRM end
    };

// .........................................................................................................................
    $scope.sendSRM = function () {
        var sectionSRM = "";
        if (typeof document.getElementById("othersHeaderGlobal").value === "undefined" || document.getElementById("othersHeaderGlobal").value == null || document.getElementById("othersHeaderGlobal").value === "")
            return;
        if (typeof document.getElementById("othersCommentGlobal").value === "undefined" || document.getElementById("othersCommentGlobal").value == null || document.getElementById("othersCommentGlobal").value === "")
            return;
        sectionSRM = document.getElementById("othersHeaderGlobal").value;
        var textSRM = document.getElementById("othersCommentGlobal").value;
        var summarySRM = "+ new request (" + wiki + ")";
        var pageSRM = "Steward requests/Miscellaneous";

        $.ajax({
            url: 'php/doEdit.php', type: 'POST',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / M.Report');
            },
            crossDomain: true, dataType: 'json',
            data: {
                checkreport: 1,
                srm: 1,
                warn: 1,
                project: server_url + script_path + "/api.php",
                wiki: wiki,
                page: pageSRM,
                text: "{{Status|in progress}}\n" + textSRM + "--~~~~",
                user: user,
                regexreport: 1,
                summary: summarySRM
            },
            success: function (s) {
                if (s["result"] === false) {
                    $.ajax({
                        url: 'php/doEdit.php', type: 'POST',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / M.Report');
                        },
                        crossDomain: true, dataType: 'json',
                        data: {
                            warn: "SRM",
                            withoutsection: false,
                            project: server_url + script_path + "/api.php",
                            wiki: wiki,
                            page: pageSRM,
                            text: textSRM,
                            sectiontitle: sectionSRM,
                            summary: summarySRM
                        },
                        success: function () {
                            closePO();
                        },
                        error: function () {
                            alert("Unknow network error");
                            closePO();
                        },
                    });
                } else
                    alert("Already requested!");
            }
        });

    };


    $scope.sendReportLocal = function () {
        var sectionReport = "";
        if (checkKey("withoutSectionReport", wiki, reportArray) === false || reportArray["withoutSectionReport"] === false) {
            if (typeof document.getElementById("reportHeaderLocal").value === "undefined" || document.getElementById("reportHeaderLocal").value == null || document.getElementById("reportHeaderLocal").value === "")
                return;
            else
                sectionReport = document.getElementById("reportHeaderLocal").value;
        }
        if (typeof document.getElementById("reportCommentLocal").value === "undefined" || document.getElementById("reportCommentLocal").value == null || document.getElementById("reportCommentLocal").value === "")
            return;

        var checkReport = false;
        if (reportArray !== null)
            if (checkKey("pageReport", wiki, reportArray))
                if (checkKey("regexReport", wiki, reportArray))
                    checkReport = true;
        if (checkReport === false)
            return;

        var pageReport = reportArray["pageReport"];
        var regexReport = reportArray["regexReport"];
        var textReport = document.getElementById("reportCommentLocal").value;
        var summaryReport = reportArray["summaryReport"].replace(/\$1/gi, user);
        var withoutSectionReport = false;
        if (checkKey("withoutSectionReport", wiki, reportArray))
            if (reportArray["withoutSectionReport"] === true)
                withoutSectionReport = true;


        $.ajax({
            url: 'php/doEdit.php', type: 'POST',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / M.Report');
            },
            crossDomain: true, dataType: 'json',
            data: {
                checkreport: 1,
                warn: 1,
                project: server_url + script_path + "/api.php",
                wiki: wiki,
                page: pageReport,
                text: textReport,
                user: user,
                regexreport: regexReport,
                summary: summaryReport
            },
            success: function (s) {
                if (s["result"] === false) {
                    $.ajax({
                        url: 'php/doEdit.php', type: 'POST',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / M.Report');
                        },
                        crossDomain: true, dataType: 'json',
                        data: {
                            warn: "report",
                            withoutsection: withoutSectionReport,
                            project: server_url + script_path + "/api.php",
                            wiki: wiki,
                            page: pageReport,
                            text: textReport,
                            sectiontitle: sectionReport,
                            summary: summaryReport
                        },
                        success: function () {
                            closePO();
                        },
                        error: function () {
                            alert("Unknow network error");
                            closePO();
                        },
                    });
                } else
                    alert("Already requested!");
            }
        });
    };


    $scope.sendRequestProtect = function () {
        var sectionProtect = "";
        if (checkKey("withoutSectionProtect", wiki, protectArray) === false || protectArray["withoutSectionProtect"] === false) {
            if (typeof document.getElementById("protectHeaderLocal").value === "undefined" || document.getElementById("protectHeaderLocal").value == null || document.getElementById("protectHeaderLocal").value === "")
                return;
            else
                sectionProtect = document.getElementById("protectHeaderLocal").value;
        }
        if (typeof document.getElementById("protectCommentLocal").value === "undefined" || document.getElementById("protectCommentLocal").value == null || document.getElementById("protectCommentLocal").value === "")
            return;

        var checkProtect = false;
        if (protectArray !== null)
            if (checkKey("pageProtect", wiki, protectArray))
                if (checkKey("regexProtect", wiki, protectArray))
                    checkProtect = true;
        if (checkProtect === false)
            return;

        var pageProtect = protectArray["pageProtect"];
        var regexProtect = protectArray["regexProtect"];
        var textProtect = document.getElementById("protectCommentLocal").value;
        var summaryProtect = protectArray["summaryProtect"].replace(/\$1/gi, title);
        var withoutSectionProtect = false;
        if (checkKey("withoutSectionProtect", wiki, protectArray))
            if (protectArray["withoutSectionProtect"] === true)
                withoutSectionProtect = true;


        $.ajax({
            url: 'php/doEdit.php', type: 'POST',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Protect');
            },
            crossDomain: true, dataType: 'json',
            data: {
                checkreport: 1,
                warn: 1,
                project: server_url + script_path + "/api.php",
                wiki: wiki,
                page: pageProtect,
                text: textProtect,
                user: title,
                regexreport: regexProtect,
                summary: summaryProtect
            },
            success: function (s) {
                if (s["result"] === false) {
                    $.ajax({
                        url: 'php/doEdit.php', type: 'POST',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Protect');
                        },
                        crossDomain: true, dataType: 'json',
                        data: {
                            warn: "protect",
                            withoutsection: withoutSectionProtect,
                            project: server_url + script_path + "/api.php",
                            wiki: wiki,
                            page: pageProtect,
                            text: textProtect,
                            sectiontitle: sectionProtect,
                            summary: summaryProtect
                        },
                        success: function () {
                            closePO();
                        },
                        error: function () {
                            alert("Unknow network error");
                            closePO();
                        },
                    });
                } else
                    alert("Already requested!");
            }
        });
    };

    $scope.Revert = function (summaryPreset, warnPreset) {
        if ((old == null) || (isNaN(old) === true))
            return;
        uiDisable();
        var summarypre = "";
        if (summaryPreset !== null && typeof summaryPreset !== "undefined")
            summarypre = summaryPreset.replace(/\$7/gi, title);
        else {
            if (document.getElementById('credit').value !== "" && document.getElementById('credit').value !== null && document.getElementById('customRevert').classList.contains('po__active'))
                summarypre = document.getElementById('credit').value;
        }
        document.getElementById('credit').value = "";
        closePO();
        checkLast(server_url, server_name, script_path, server_uri, wiki, namespace, user, old, dnew, times,function(cb0) {
            if (cb0 === false) {
                SHOW_DIFF(server_url, server_name, script_path, server_uri, wiki, namespace, user, old, dnew, title, userip, summary, wikidatat, "second");
                createDialog({
                    parentId: 'angularapp', id: 'multipleEditDialog',
                    title: 'Multiple edits',
                    alert: { message: 'This user made multiple edits on this article. Please check article history first.' },
                    buttons: [{ type: 'accent', title: 'Alright', remove: true }]
                })
                return;
            }

            $scope.isCURRENT(server_url, script_path, title, dnew, old, function (cb) {
                if (cb == null || cb === false) {
                    uiEnable();
                    return;
                }

                var rbmode = "rollback";
                if (isGlobalModeAccess === true && local_wikis.indexOf(wiki) === -1)
                    rbmode = "undo";

                var undoSummary = 'Undid edits by [[Special:Contribs/$2|$2]] ([[User talk:$2|talk]]) to last version by $1';
                if (config["wikis"][0].hasOwnProperty(wiki))
                    if (config["wikis"][0][wiki][0].hasOwnProperty("defaultUndoSummary"))
                        if (config["wikis"][0][wiki][0]["defaultUndoSummary"] !== null && config["wikis"][0][wiki][0]["defaultUndoSummary"] !== "")
                            undoSummary = config["wikis"][0][wiki][0]["defaultUndoSummary"];
                var revertData = {};
                if (summarypre === "") {
                    if (rbmode === "undo")
                        revertData = {
                            rbmode: rbmode,
                            basetimestamp: timestamp,
                            page: title,
                            id: dnew,
                            user: user,
                            wiki: wiki,
                            summary: undoSummary.replace(/\$2/gi, user).replace(/\$3/gi, title),
                            project: server_url + script_path + "/api.php"
                        };
                    else
                        revertData = {
                            rbmode: rbmode,
                            basetimestamp: timestamp,
                            page: title,
                            id: dnew,
                            user: user,
                            wiki: wiki,
                            project: server_url + script_path + "/api.php"
                        };
                    var rawV = {user: user, project: server_url + script_path, wiki: wiki, oldid: old, newid: dnew};
                    vandalsReport.push(rawV);
                    vandals.push(user);
                    var rawSend = {"type": "synch", "wiki": wiki, "nickname": user, "vandal": "1", "page": title};
                    connectTalk.talkSendInside(rawSend);
                } else {
                    var undoPrefix = 'Undid edits by [[Special:Contribs/$2|$2]] ([[User talk:$2|talk]]) to last version by $1: '.replace(/\$2/gi, user).replace(/\$3/gi, title);
                    if (config["wikis"][0].hasOwnProperty(wiki))
                        if (config["wikis"][0][wiki][0].hasOwnProperty("defaultUndoPrefix"))
                            if (config["wikis"][0][wiki][0]["defaultUndoPrefix"] !== null && config["wikis"][0][wiki][0]["defaultUndoPrefix"] !== "")
                                undoPrefix = config["wikis"][0][wiki][0]["defaultUndoPrefix"].replace(/\$2/gi, user).replace(/\$3/gi, title);

                    var rollbackPrefix = 'Reverted edits by [[Special:Contribs/$2|$2]] ([[User talk:$2|talk]]) to last version by $1: ';
                    if (config["wikis"][0].hasOwnProperty(wiki))
                        if (config["wikis"][0][wiki][0].hasOwnProperty("defaultRollbackPrefix"))
                            if (config["wikis"][0][wiki][0]["defaultRollbackPrefix"] !== null && config["wikis"][0][wiki][0]["defaultRollbackPrefix"] !== "")
                                rollbackPrefix = config["wikis"][0][wiki][0]["defaultRollbackPrefix"].replace(/\$7/gi, title);

                    if (rbmode === "undo")
                        rollbackPrefix = undoPrefix;
                    revertData = {
                        rbmode: rbmode,
                        basetimestamp: timestamp,
                        page: title,
                        id: dnew,
                        user: user,
                        wiki: wiki,
                        summary: rollbackPrefix + summarypre,
                        project: server_url + script_path + "/api.php"
                    };
                    rawV = {user: user, project: server_url + script_path, wiki: wiki, oldid: old, newid: dnew};
                    vandalsReport.push(rawV);
                    suspects.push(user);
                    rawSend = {"type": "synch", "wiki": wiki, "nickname": user, "vandal": "2", "page": title};
                    connectTalk.talkSendInside(rawSend);
                }
                $.ajax({
                    url: 'php/rollback.php',
                    type: 'POST',
                    crossDomain: true,
                    data: revertData,
                    dataType: 'json',
                    success: function (datarollback) {
                        if (datarollback["result"] === "Success") {
                            $scope.$apply(function () {
                                $scope.edits.map(function (e, index) {
                                    if (e.wiki === wiki && e.title === title) {
                                        $scope.edits.splice($scope.edits.indexOf($scope.edits[index]), 1);
                                    }
                                });
                            });

                            if (warnPreset !== null && typeof warnPreset !== "undefined") {
                                var checkWarnCorrect = false;
                                if (warn !== null)
                                    if (typeof warn["summaryWarn"] !== "undefined")
                                        if (warn["summaryWarn"] !== null && warn["summaryWarn"] !== "")
                                            if (typeof warn["sectionWarn"] !== "undefined")
                                                if (warn["sectionWarn"] !== null && warn["sectionWarn"] !== "")
                                                    if (typeof warn["countWarn"] !== "undefined")
                                                        if (warn["countWarn"] !== null && warn["countWarn"] !== "")
                                                            if (typeof warn[warnPreset] !== "undefined")
                                                                if (warn[warnPreset] !== null && warn[warnPreset] !== "")
                                                                    if (typeof warn[warnPreset][0]["tags"] !== "undefined")
                                                                        if (warn[warnPreset][0]["tags"] !== null && warn[warnPreset][0]["tags"] !== "")
                                                                            if (typeof warn[warnPreset][0]["templates"] !== "undefined")
                                                                                if (warn[warnPreset][0]["templates"] !== null && warn[warnPreset][0]["templates"] !== "") {
                                                                                    checkWarnCorrect = true;
                                                                                    var timeWarn = moment.utc().subtract('10', 'days').format('YYYY-MM-DDTHH:mm:ss') + "Z";
                                                                                    var warnSection = warn["sectionWarn"];
                                                                                    var warnSummary = warn["summaryWarn"];
                                                                                    var warnCount = Number(warn["countWarn"]) - 1;
                                                                                    var warnMonth = moment.utc().format('MMMM');

                                                                                    if (typeof warn["months"] !== "undefined")
                                                                                        if (typeof warn["months"][0][parseInt(moment.utc().format('MM'))] !== "undefined")
                                                                                            if (warn["months"][0][parseInt(moment.utc().format('MM'))] !== null)
                                                                                                warnMonth = warn["months"][0][parseInt(moment.utc().format('MM'))];
                                                                                    var tags = [];
                                                                                    var templates = [];

                                                                                    for (var keyTags in warn[warnPreset][0]["tags"][0]) {
                                                                                        if (warn[warnPreset][0]["tags"][0].hasOwnProperty(keyTags))
                                                                                            tags.push(warn[warnPreset][0]["tags"][0][keyTags]);
                                                                                    }
                                                                                    for (var keyTemplates in warn[warnPreset][0]["templates"][0]) {
                                                                                        if (warn[warnPreset][0]["templates"][0].hasOwnProperty(keyTemplates))
                                                                                            templates.push(warn[warnPreset][0]["templates"][0][keyTemplates]);
                                                                                    }

                                                                                    var url = server_url + script_path + "/api.php?action=query&prop=revisions&titles=User_talk:" + encodeURIComponent(user) + "&rvslots=main&rvprop=ids&rvdir=newer&rvstart=" + timeWarn + "&rvlimit=500&format=json&utf8=1";
                                                                                    $.ajax({
                                                                                        url: url,
                                                                                        type: 'GET',
                                                                                        beforeSend: function (xhr) {
                                                                                            xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Warns');
                                                                                        },
                                                                                        crossDomain: true,
                                                                                        dataType: 'jsonp',
                                                                                        success: function (idsWarns) {
                                                                                            var level = -1;
                                                                                            var pageIdsWarns = null;
                                                                                            if (typeof idsWarns["query"] !== "undefined" && typeof idsWarns["query"]["pages"] !== "undefined") {
                                                                                                for (var k in idsWarns["query"]["pages"]) {
                                                                                                    if (idsWarns["query"]["pages"].hasOwnProperty(k))
                                                                                                        if (Number(k) !== -1)
                                                                                                            pageIdsWarns = idsWarns["query"]["pages"][k];
                                                                                                }
                                                                                                var oldIdsWarns = -1;
                                                                                                var newIdsWarns = -1;
                                                                                                if (pageIdsWarns !== null && typeof pageIdsWarns["revisions"] !== "undefined") {
                                                                                                    newIdsWarns = pageIdsWarns["revisions"][pageIdsWarns["revisions"].length - 1]["revid"];
                                                                                                    if (typeof pageIdsWarns["revisions"][0]["parentid"] !== "undefined" && pageIdsWarns["revisions"][0]["parentid"] !== null && pageIdsWarns["revisions"][0]["parentid"] !== "")
                                                                                                        oldIdsWarns = pageIdsWarns["revisions"][0]["parentid"];
                                                                                                }
                                                                                                var url = server_url + script_path + "/api.php?action=compare&fromrev=" + oldIdsWarns + "&torev=" + newIdsWarns + "&utf8=1&format=json";
                                                                                                if (oldIdsWarns === -1)
                                                                                                    url = server_url + script_path + "/api.php?action=compare&fromrev=" + newIdsWarns + "&torelative=prev&utf8=1&format=json";
                                                                                                if (oldIdsWarns === 0)
                                                                                                    url = server_url + script_path + "/api.php?action=query&prop=revisions&titles=User_talk:" + encodeURIComponent(user) + "&rvslots=main&rvprop=content&rvlimit=1&format=json&utf8=1";
                                                                                                $.ajax({
                                                                                                    url: url,
                                                                                                    type: 'GET',
                                                                                                    crossDomain: true,
                                                                                                    dataType: 'jsonp',
                                                                                                    success: function (revisionsWarn) {
                                                                                                        var diffWarnContent = "";
                                                                                                        if ((typeof revisionsWarn["compare"] !== "undefined" && typeof revisionsWarn["compare"]["*"] !== "undefined") || (typeof revisionsWarn["query"] !== "undefined" && typeof revisionsWarn["query"]["pages"] !== "undefined" && revisionsWarn["query"]["pages"] !== -1)) {
                                                                                                            if (typeof revisionsWarn["query"] !== "undefined" && typeof revisionsWarn["query"]["pages"] !== "undefined") {
                                                                                                                var pageIdContent = "";
                                                                                                                for (var k in revisionsWarn["query"]["pages"]) {
                                                                                                                    if (revisionsWarn["query"]["pages"].hasOwnProperty(k))
                                                                                                                        if (Number(k) !== -1)
                                                                                                                            pageIdContent = revisionsWarn["query"]["pages"][k];
                                                                                                                }
                                                                                                                if (typeof pageIdContent["revisions"] !== "undefined" && typeof pageIdContent["revisions"][0] !== "undefined" && typeof pageIdContent["revisions"][0]["slots"] !== "undefined" && typeof pageIdContent["revisions"][0]["slots"]["main"] !== "undefined" && typeof pageIdContent["revisions"][0]["slots"]["main"]["*"] !== "undefined")
                                                                                                                    diffWarnContent = pageIdContent["revisions"][0]["slots"]["main"]["*"];
                                                                                                            } else {
                                                                                                                if (typeof revisionsWarn["compare"] !== "undefined" && typeof revisionsWarn["compare"]["*"] !== "undefined")
                                                                                                                    diffWarnContent = revisionsWarn["compare"]["*"];
                                                                                                            }
                                                                                                            diffWarnContent = getNewFromDiff(diffWarnContent);
                                                                                                            for (var tagWarn in tags) {
                                                                                                                if (tags.hasOwnProperty(tagWarn))
                                                                                                                    if (diffWarnContent.indexOf(tags[tagWarn]) !== -1)
                                                                                                                        if (level < tagWarn)
                                                                                                                            level = Number(tagWarn);
                                                                                                            }
                                                                                                        }

                                                                                                        if (level === -1 || (level !== -1 && level < warnCount)) {
                                                                                                            level = level + 1;
                                                                                                            if (typeof templates[level] !== "undefined") {

                                                                                                                $.ajax({
                                                                                                                    url: 'php/doEdit.php',
                                                                                                                    type: 'POST',
                                                                                                                    beforeSend: function (xhr) {
                                                                                                                        xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Warns');
                                                                                                                    },
                                                                                                                    crossDomain: true,
                                                                                                                    dataType: 'json',
                                                                                                                    data: {
                                                                                                                        warn: "rollback",
                                                                                                                        withoutsection: withoutSection,
                                                                                                                        project: server_url + script_path + "/api.php",
                                                                                                                        wiki: wiki,
                                                                                                                        page: 'User_talk:' + user,
                                                                                                                        text: templates[level].replace(/\$1/gi, title).replace(/\$2/gi, old).replace(/\$3/gi, dnew).replace(/\$4/gi, user).replace(/\$5/gi, "Special:Diff/" + old + "/" + dnew).replace(/\$6/gi, server_url + script_path + "/index.php?oldid=" + old + "&diff=" + dnew),
                                                                                                                        sectiontitle: warnSection.replace(/\$DD/gi, moment.utc().format('DD')).replace(/\$MMMM/gi, warnMonth).replace(/\$MM/gi, moment.utc().format('MM')).replace(/\$YYYY/gi, moment.utc().format('YYYY')),
                                                                                                                        summary: warnSummary.replace(/\$1/gi, level + 1)
                                                                                                                    },
                                                                                                                    success: function () {
                                                                                                                        $scope.reqEnd(datarollback);
                                                                                                                    },
                                                                                                                    error: function () {
                                                                                                                        $scope.reqEnd(datarollback);
                                                                                                                    }
                                                                                                                });
                                                                                                            } else
                                                                                                                $scope.reqEnd(datarollback);
                                                                                                        } else {
                                                                                                            if (level === warnCount) {
                                                                                                                var checkReport = false;
                                                                                                                if (reportArray !== null)
                                                                                                                    if (checkKey("autoReport", wiki, reportArray))
                                                                                                                        if (reportArray["autoReport"] === true)
                                                                                                                            if (checkKey("pageReport", wiki, reportArray))
                                                                                                                                if (checkKey("regexReport", wiki, reportArray))
                                                                                                                                    if (checkKey("sectionReport", wiki, reportArray) || (checkKey("withoutSectionReport", wiki, reportArray) && reportArray["withoutSectionReport"] === true))
                                                                                                                                        if (checkKey("textReport", wiki, reportArray)) {
                                                                                                                                            checkReport = true;
                                                                                                                                            var pageReport = reportArray["pageReport"];
                                                                                                                                            var regexReport = reportArray["regexReport"].replace(/\$1/gi, user);
                                                                                                                                            var regexReport2 = "";
                                                                                                                                            if (checkKey("regexReport2", wiki, reportArray))
                                                                                                                                                regexReport2 = reportArray["regexReport2"].replace(/\$1/gi, user);
                                                                                                                                            var textReport = reportArray["textReport"].replace(/\$1/gi, user);
                                                                                                                                            if (checkKey("textReportIP", wiki, reportArray))
                                                                                                                                                if (userip === "ip")
                                                                                                                                                    textReport = reportArray["textReportIP"].replace(/\$1/gi, user);
                                                                                                                                            var summaryReport = "[[Special:Contribs/" + user + "|" + user + "]]";
                                                                                                                                            if (checkKey("summaryReport", wiki, reportArray))
                                                                                                                                                summaryReport = reportArray["summaryReport"].replace(/\$1/gi, user);
                                                                                                                                            var withoutSectionReport = false;
                                                                                                                                            if (checkKey("withoutSectionReport", wiki, reportArray))
                                                                                                                                                if (reportArray["withoutSectionReport"] === true)
                                                                                                                                                    withoutSectionReport = true;
                                                                                                                                            var sectionReport = null;
                                                                                                                                            if (withoutSectionReport === false)
                                                                                                                                                sectionReport = reportArray["sectionReport"].replace(/\$1/gi, user);


                                                                                                                                            $.ajax({
                                                                                                                                                url: 'php/doEdit.php',
                                                                                                                                                type: 'POST',
                                                                                                                                                beforeSend: function (xhr) {
                                                                                                                                                    xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Report');
                                                                                                                                                },
                                                                                                                                                crossDomain: true,
                                                                                                                                                dataType: 'json',
                                                                                                                                                data: {
                                                                                                                                                    checkreport: 1,
                                                                                                                                                    warn: 1,
                                                                                                                                                    project: server_url + script_path + "/api.php",
                                                                                                                                                    wiki: wiki,
                                                                                                                                                    page: pageReport,
                                                                                                                                                    text: textReport,
                                                                                                                                                    user: user,
                                                                                                                                                    regexreport: regexReport,
                                                                                                                                                    regexreport2: regexReport2,
                                                                                                                                                    summary: summaryReport
                                                                                                                                                },
                                                                                                                                                success: function (s) {
                                                                                                                                                    if (s["result"] === false) {
                                                                                                                                                        var isTop = false;
                                                                                                                                                        if (config["wikis"][0].hasOwnProperty(wiki))
                                                                                                                                                            if (config["wikis"][0][wiki][0].hasOwnProperty("report"))
                                                                                                                                                                if (config["wikis"][0][wiki][0]["report"][0].hasOwnProperty("reportTop"))
                                                                                                                                                                    if (config["wikis"][0][wiki][0]["report"][0]["reportTop"] !== null && config["wikis"][0][wiki][0]["report"][0]["reportTop"] !== "" && config["wikis"][0][wiki][0]["report"][0]["reportTop"] === true)
                                                                                                                                                                        isTop = true;
                                                                                                                                                        var preamb = false;
                                                                                                                                                        if (config["wikis"][0].hasOwnProperty(wiki))
                                                                                                                                                            if (config["wikis"][0][wiki][0].hasOwnProperty("report"))
                                                                                                                                                                if (config["wikis"][0][wiki][0]["report"][0].hasOwnProperty("reportPreamb"))
                                                                                                                                                                    if (config["wikis"][0][wiki][0]["report"][0]["reportPreamb"] !== null && config["wikis"][0][wiki][0]["report"][0]["reportPreamb"] !== "" && config["wikis"][0][wiki][0]["report"][0]["reportPreamb"] === true)
                                                                                                                                                                        preamb = true;
                                                                                                                                                        createDialog({
                                                                                                                                                            parentId: "angularapp",
                                                                                                                                                            id: "autoReportDialog",
                                                                                                                                                            title: "Auto report",
                                                                                                                                                            alert: { emoji: "😡", message: "This user has been warned multiple times. Do you want to report them to administators?" },
                                                                                                                                                            buttons: [{
                                                                                                                                                                type: "negative",
                                                                                                                                                                title: "Report",
                                                                                                                                                                onClick: () => {
                                                                                                                                                                    $.ajax({
                                                                                                                                                                        url: 'php/doEdit.php',
                                                                                                                                                                        type: 'POST',
                                                                                                                                                                        beforeSend: function (xhr) {
                                                                                                                                                                            xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Report');
                                                                                                                                                                        },
                                                                                                                                                                        crossDomain: true,
                                                                                                                                                                        dataType: 'json',
                                                                                                                                                                        data: {
                                                                                                                                                                            warn: "report",
                                                                                                                                                                            withoutsection: withoutSectionReport,
                                                                                                                                                                            project: server_url + script_path + "/api.php",
                                                                                                                                                                            wiki: wiki,
                                                                                                                                                                            top: isTop,
                                                                                                                                                                            preamb: preamb,
                                                                                                                                                                            page: pageReport,
                                                                                                                                                                            text: textReport,
                                                                                                                                                                            sectiontitle: sectionReport,
                                                                                                                                                                            summary: summaryReport
                                                                                                                                                                        },
                                                                                                                                                                        success: function () {
                                                                                                                                                                            $scope.reqEnd(datarollback);
                                                                                                                                                                        },
                                                                                                                                                                        error: function () {
                                                                                                                                                                            $scope.reqEnd(datarollback);
                                                                                                                                                                        }
                                                                                                                                                                    });
                                                                                                                                                                },
                                                                                                                                                                remove: true
                                                                                                                                                            }, {
                                                                                                                                                                title: "Cancel",
                                                                                                                                                                onClick: () => {
                                                                                                                                                                    $scope.reqEnd(datarollback);
                                                                                                                                                                },
                                                                                                                                                                remove: true
                                                                                                                                                            }]
                                                                                                                                                        })
                                                                                                                                                    } else
                                                                                                                                                        $scope.reqEnd(datarollback);
                                                                                                                                                },
                                                                                                                                                error: function () {
                                                                                                                                                    $scope.reqEnd(datarollback);
                                                                                                                                                }
                                                                                                                                            });
                                                                                                                                        }
                                                                                                            }
                                                                                                            if (checkReport === false)
                                                                                                                $scope.reqEnd(datarollback);
                                                                                                        }
                                                                                                    }, error() {
                                                                                                        $scope.reqEnd(datarollback);
                                                                                                    }
                                                                                                });
                                                                                            } else
                                                                                                $scope.reqEnd(datarollback);
                                                                                        },
                                                                                        error() {
                                                                                            $scope.reqEnd(datarollback);
                                                                                        }
                                                                                    });
                                                                                } else
                                                                                    $scope.reqEnd(datarollback);
                                if (checkWarnCorrect === false) {
                                    alert("Warning error. Maybe confug is not correctly");
                                    $scope.reqEnd(datarollback);
                                }
                            } else
                                $scope.reqEnd(datarollback);
                        } else {
                            if (datarollback['code'] === "undofailure" || datarollback['code'] === "editconflict" || datarollback['code'] === "alreadyrolled") {
                                $scope.isCURRENT(server_url, script_path, title, dnew, old, function (cb2) {
                                    if (cb2 == null) {
                                        uiEnable();
                                        return;
                                    }
                                    if (cb2 === false) {
                                        uiEnable();
                                        return;
                                    }
                                    document.getElementById('page').srcdoc = starterror + "Rollback error: " + escapeXSS(datarollback['result']) + enderror;
                                    uiEnable();
                                });
                            } else {
                                document.getElementById('page').srcdoc = starterror + "Rollback error: " + escapeXSS(datarollback['result']) + enderror;
                                uiEnable();
                            }
                        }
                    }, error: function (error) {
                        document.getElementById('page').srcdoc = starterror + "Rollback error. Please open page in the new tab.<br><small>Error code: " + escapeXSS(error.status) + enderror;
                        uiEnable();
                    }
                });
            });
        });
    };

    function getNewFromDiff(diffContent) {
        return diffContent.replace(/<tr>([^]*?)<\/tr>/g, function ($0, $1) {
            if ($1.search(/<td class="deletedline">/g) === -1 &&
                $1.search(/<td class="diff-marker">-<\/td>/g) === -1 &&
                $1.search(/<td class="diff-context">/g) === -1 &&
                $1.search(/<ins/g) === -1 &&
                $1.search(/<del/g) === -1)
                return $1;
            else
                return "";
        });
    }

    function connectTalk() {
        var sc = new WebSocket("wss://tools.wmflabs.org/iluvatarbot/:9030?name=" + userSelf + "&token=" + talktoken);

        sc.onclose = function () {
            setTimeout(function () {
                connectTalk();
            }, 1000);
        };
        sc.onerror = function () {
            $scope.$apply(function () {
                $scope.users = [];
            });
            if (countConnectAttemp === 0) {
                if (document.getElementById('talkForm') !== null) {
                    var newDiv = document.createElement('div');
                    newDiv.className = 'phrase-talk';
                    newDiv.style.color = 'var(--tc-negative)';
                    newDiv.textContent = "SYSTEM: connection lost";
                    document.getElementById('form-talk').appendChild(newDiv);
                    scrollToBottom("form-talk");
                }
            }
            countConnectAttemp++;
            document.getElementById('badge-talk').style.background = "rgb(251, 47, 47)";
            document.getElementById('badge-talk').classList.remove('badge-ic__primary');
            sc.close();
        };

        function sendTalk () {
            var phraseTalk = document.getElementById('phrase-send-talk').value;
            if (typeof phraseTalk !== "undefined" && phraseTalk !== null && phraseTalk !== "" && sc.readyState === 1) {
                document.getElementById('phrase-send-talk').value = '';
                sc.send(JSON.stringify({"type": "message", "text": phraseTalk}));
            }
        };

        sc.onmessage = function (event) {
            var msg = JSON.parse(event.data);
            var indextmp = 0;
            if (msg.type === 'hello') {
                if (countConnectAttemp >= 1) {
                    $scope.user = [];
                    if (document.getElementById('talkForm') !== null) {
                        (async function () {
                            const talkModule = await import('./modules/talk.js');
                            talkModule.downloadHistoryTalk();
                            var newDiv = document.createElement('div');
                            newDiv.className = 'phrase-talk';
                            newDiv.style.color = 'var(--tc-positive)';
                            newDiv.textContent = "SYSTEM: connection restored";
                            document.getElementById('form-talk').appendChild(newDiv);
                            scrollToBottom("form-talk");
                        })();
                    }
                    document.getElementById('badge-talk').style.background = "none";
                    document.getElementById('badge-talk').classList.add('badge-ic__primary');
                }
                countConnectAttemp = 0;
                $scope.$apply(function () {
                    let data = msg.clients.split(',');
                    $scope.users = data.filter((value, index) => data.indexOf(value) === index);
                });
                while (indextmp <= $scope.users.length - 1) {
                    if ($scope.offlineUsers.indexOf($scope.users[indextmp]) !== -1)
                        $scope.$apply(function () {
                            $scope.offlineUsers.splice($scope.offlineUsers.indexOf($scope.users[indextmp]), 1);
                        });
                    indextmp++;
                }

            }

            if (msg.type === 'connected') {
                $scope.$apply(function () {
                    if ($scope.users.find(user => user === msg.nickname) === undefined) $scope.users.push(msg.nickname);
                });
                indextmp = 0;
                if ($scope.offlineUsers.indexOf(msg.nickname) !== -1)
                    $scope.$apply(function () {
                        $scope.offlineUsers.splice($scope.offlineUsers.indexOf(msg.nickname), 1);
                    });
            }
            if (msg.type === 'disconnected') {
                $scope.$apply(function () {
                    let data = msg.clients.split(',');
                    $scope.users = data.filter((value, index) => data.indexOf(value) === index);
                });
                if ($scope.offlineUsers.indexOf(msg.client) === -1 && $scope.users.indexOf(msg.client) === -1)
                    $scope.$apply(function () {
                        $scope.offlineUsers.push(msg.client);
                    });
            }
            if (msg.type === 'message') {
                if (document.getElementById('talkForm') !== null) {
                    (async function () {
                        const talkModule = await import('./modules/talk.js');
                        if (talkModule.daysAgoToday === false && talkModule.historyCount !== 0) {
                            talkModule.addToTalkSection("Today", false);
                            talkModule.daysAgoToday = true;
                        }
                        talkModule.addToTalk(null, msg.nickname, msg.text);
                    })();
                }
                if (msg.nickname !== userSelf && !document.getElementById('btn-talk').classList.contains('tab__active')) {
                    var userSelfTmp1 = "@" + userSelf + " ";
                    var userSelfTmp2 = "@" + userSelf + ",";
                    if ((msg.text.toUpperCase().indexOf(userSelfTmp1.toUpperCase()) !== -1 || msg.text.toUpperCase().indexOf(userSelfTmp2.toUpperCase()) !== -1) && (sound === 1 || sound === 2 || sound === 3 || sound === 4) && typeof privateMessageSound !== "undefined")
                        playSound(privateMessageSound, true);
                    else {
                        if (typeof messageSound !== "undefined" && (sound === 1 || sound === 2))
                            playSound(messageSound, false);
                    }
                }
                if (!document.getElementById('btn-talk').classList.contains('tab__active')) {
                    document.getElementById('badge-talk').style.background = "rgb(36, 164, 100)";
                    document.getElementById('badge-talk').classList.remove('badge-ic__primary');
                }
            }

            if (msg.type === "synch") {
                $scope.edits.map(function (e, index) {
                    if (e.wiki === msg.wiki && e.title === msg.page)
                        $scope.$apply(function () {
                            $scope.edits.splice($scope.edits.indexOf($scope.edits[index]), 1);
                        });
                });
                if (msg.vandal === "1")
                    $scope.$apply(function () {
                        vandals.push(msg.nickname);
                    });
                if (msg.vandal === "2")
                    $scope.$apply(function () {
                        suspects.push(msg.nickname);
                    });
            }
        };

        function talkSendInside(messageInside) {
            if (sc.readyState === 1)
                sc.send(JSON.stringify(messageInside));
        }

        connectTalk.talkSendInside = talkSendInside;
        connectTalk.sendTalk = sendTalk;
    }

    connectTalk();
    $scope.sendTalkMsg = connectTalk.sendTalk;
    $scope.displayTalkPeople = () => {
        const peopleTemplate = `<div style="display: flex; flex-direction: column-reverse"> <div class="user-container fs-md" ng-repeat="talkUser in users|unique: talkUser as filteredUsersTalk"> <div class="user-talk" onclick="selectTalkUsers(this)">{{talkUser}}</div> <a class="user-talk-CA" rel="noopener noreferrer" href="https://meta.wikimedia.org/wiki/Special:CentralAuth/{{talkUser}}" target="_blank">CA</a> </div> </div> <div style="display: flex; flex-direction: column-reverse"> <div ng-repeat="talkUserOffline in offlineUsers track by $index"> <div class="user-talk fs-md" style="color: gray;">{{talkUserOffline}}</div> </div> </div>`;
        var peopleCompiled = $compile(peopleTemplate)($scope)
        angular.element(document.getElementById('talkPeopleContent')).append(peopleCompiled);
        $scope.$apply();
    }

    $scope.oresWikiList = {};
    $.ajax({
        type: 'GET',
        url: 'https://ores.wikimedia.org/v3/scores',
        dataType: 'text json',
        success: result => oresWikiList = result
    });
    $scope.genORES = (wiki, revId, refObj) => {
        if (Object.keys(oresWikiList).length === 0) return;
        if (Object.keys(oresWikiList).find(oresWiki => oresWiki === wiki) === undefined) return;
        var oresModel;
        if (oresWikiList[wiki]['models']['damaging'] !== undefined) oresModel = 'damaging';
        else if (oresWikiList[wiki]['models']['reverted'] !== undefined) oresModel = 'reverted';
        else return;
        $.ajax({
            type: 'GET',
            url: `https://ores.wikimedia.org/v3/scores/${wiki}/${revId}/${oresModel}`,
            dataType: 'text',
            success: res => {
                let oresData = JSON.parse(res);
                if (oresData[wiki]['scores'][revId][oresModel]['score'] === undefined) return;
                const damage = oresData[wiki]['scores'][revId][oresModel]['score']['probability']['true'];
                const damagePer = parseInt(damage * 100);
                refObj.ores = { score: damagePer, color: `hsl(0, ${damagePer}%, 56%)` };
                $scope.$apply();
            },
            error: e => { console.error(e); return; }
        });
    }
    $scope.edits = [];
    if (typeof (EventSource) == "undefined") {
        alert("Sorry, your browser does not support server-sent events.");
        return;
    }

    $scope.recentChange = {
        connect: function () {
            if (this.isConnected) return;
            this.source = new EventSource("https://stream.wikimedia.org/v2/stream/recentchange");
            this.isConnected = true;
            this.source.onmessage = function (event) {
                var stuff = JSON.parse(event.data);
                var namespacetemp = "";
                var swmt = false; var setusers = false;
        
                if (isGlobal === true || isGlobalModeAccess === true) {
                    swmt = (presets[selectedPreset]["swmt"] === "1" || presets[selectedPreset]["swmt"] === "2") ? true : false;
                    setusers = (presets[selectedPreset]["users"] === "1" || presets[selectedPreset]["users"] === "2") ? true : false;
                }
        
                var registeredSSE = (presets[selectedPreset]["registered"] === "1") ? true : false;
                var rcMode1 = (presets[selectedPreset]["new"] === "1") ? "new" : "none";
                var rcMode2 = (presets[selectedPreset]["onlynew"] === "1") ? "none" : "edit";
                var anonsSSE = (presets[selectedPreset]["anons"] === "1") ? true : false;
        
                if (stuff.namespace >= 0 && stuff.namespace <= 15) namespacetemp = ns[stuff.namespace];
                else namespacetemp = "<font color='brown'>Non-canon (" + stuff.namespace + ")</font>";
        
                if (stuff.user !== userSelf && (stuff.type === rcMode1 || stuff.type === rcMode2) && stuff.bot === false && (presets[selectedPreset]["namespaces"].indexOf(stuff.namespace.toString()) >= 0 || presets[selectedPreset]["namespaces"].length === 0) && stuff.patrolled !== true && ((presets[selectedPreset]["blprojects"].indexOf(stuff.wiki) >= 0) || (local_wikis.indexOf(stuff.wiki) >= 0 && isGlobal === false) || (wikis.indexOf(stuff.wiki) >= 0 && swmt === true && (isGlobal === true || isGlobalModeAccess === true)) || (active_users.indexOf(stuff.wiki) >= 0 && setusers === true && (isGlobal === true || isGlobalModeAccess === true)))) {
                    if (typeof sandboxlist[stuff.wiki] !== "undefined")
                        if (sandboxlist[stuff.wiki] === stuff.title)
                            return;
                    if (global.indexOf(stuff.user) !== -1 || presets[selectedPreset]["wlusers"].indexOf(stuff.user) !== -1 || presets[selectedPreset]["wlprojects"].indexOf(stuff.wiki) !== -1)
                        return;
        
                    // IP user
                    if (/^\d*?\.\d*?\.\d*?\.\d*?$/.test(stuff.user) || stuff.user.indexOf(":") !== -1) {
                        if (!anonsSSE)
                            return;
        
                        // if it's element of WD. Get label
                        if (stuff.wiki === "wikidatawiki" && (stuff.namespace === 120 || stuff.namespace === 0)) {
                            $scope.wikidata_title(stuff, namespacetemp, "ip");
                            return;
                        }
                        // remove old edits by same user in same wiki on same page
                        $scope.edits.map(function (e, index) {
                            if (e.wiki === stuff.wiki && e.title === stuff.title && e.user === stuff.user && checkMode === 2)
                                $scope.$apply(function () {
                                    $scope.edits.splice($scope.edits.indexOf($scope.edits[index]), 1);
                                });
                        });
                        $scope.$apply(function () {
                            if (countqueue !== 0 && $scope.edits.length >= countqueue) {
                                if (terminateStream === 1) {
                                    $scope.recentChange.disconnect();
                                    return;
                                }
                                $scope.edits.pop();
                            }
                            var new_el = {
                                "server_url": stuff.server_url,
                                "server_name": stuff.server_name,
                                "script_path": stuff.server_script_path,
                                "server_uri": stuff.meta.uri,
                                "wiki": stuff.wiki,
                                "namespace": namespacetemp,
                                "user": stuff.user,
                                "title": stuff.title,
                                "comment": stuff.comment,
                                "old": stuff.revision.old,
                                "new": stuff['revision']['new'],
                                "isIp": "ip",
                                "wikidata_title": null,
                                "ores": undefined,
                                "isNew": (stuff.type === "new") ? "N" : ""
                            };
                            $scope.genORES(stuff.wiki, stuff['revision']['new'], new_el);
                            $scope.edits.unshift(new_el);
                            if ((sound === 1 || sound === 4 || sound === 5) && typeof newSound !== "undefined")
                                playSound(newSound, false);
                        });
                        return;
                    }
                    if (!registeredSSE)
                        return;
        
                    // Registered user
                    var url = stuff.server_url + stuff.server_script_path + "/api.php?action=query&list=users&ususers=" + encodeURIComponent(stuff.user).replace(/'/g, '%27') + "&usprop=groups|registration|editcount&utf8&format=json";
                    $.ajax({
                        url: url, type: 'GET',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / SSE parsing');
                        },
                        crossDomain: true, dataType: 'jsonp', success: function (datainfo) {
                            var groups = datainfo["query"]["users"][0]["groups"];
                            if (groups.includes("sysop") === false && groups.includes("editor") === false && groups.includes("autoreviewer") === false && groups.includes("confirmed") === false && groups.includes("extendedconfirmed") === false && groups.includes("filemover") === false && groups.includes("patroller") === false && groups.includes("templateeditor") === false && groups.includes("autopatrolled") === false && groups.includes("autoeditor") === false && groups.includes("closer") === false && groups.includes("rollbacker") === false && groups.includes("translator") === false && groups.includes("translationadmin") === false && groups.includes("engineer") === false && groups.includes("global-renamer") === false && groups.includes("oversight") === false && groups.includes("reviewer") === false && groups.includes("bureaucrat") === false) {
                                var d = new Date();
                                if (datainfo["query"]["users"][0]["registration"] == null)
                                    return; // WMF have lost registration date of some very old accounts
                                var dateDiff = (Date.UTC(d.getUTCFullYear(), d.getUTCMonth(), d.getUTCDate(), d.getUTCHours(), d.getUTCMinutes(), d.getUTCSeconds(), d.getUTCMilliseconds()) - Date.parse(datainfo["query"]["users"][0]["registration"])) / 1000 / 60 / 60 / 24;
                                if (parseInt(datainfo["query"]["users"][0]["editcount"]) >= parseInt(presets[selectedPreset]["editscount"]) && dateDiff >= parseInt(presets[selectedPreset]["regdays"]))
                                    return;
                                var url = "https://cvn.wmflabs.org/api.php?users=" + encodeURIComponent(stuff.user).replace(/'/g, '%27');
                                $.ajax({
                                    url: url, type: 'GET',
                                    beforeSend: function (xhr) {
                                        xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / SSE parsing');
                                    },
                                    crossDomain: true, dataType: 'jsonp', success: function (data) {
                                        if (typeof data["users"][stuff.user] !== "undefined")
                                            if (data["users"][stuff.user]["type"] === "whitelist")
                                                return;
        
                                        // if it's element of WD. Get label
                                        if (stuff.wiki === "wikidatawiki" && (stuff.namespace === 120 || stuff.namespace === 0)) {
                                            $scope.wikidata_title(stuff, namespacetemp, "registered");
                                            return;
                                        }
                                        // remove old edits by same user in same wiki on same page
                                        $scope.edits.map(function (e, index) {
                                            if (e.wiki === stuff.wiki && e.title === stuff.title && e.user === stuff.user && checkMode === 2)
                                                $scope.$apply(function () {
                                                    $scope.edits.splice($scope.edits.indexOf($scope.edits[index]), 1);
                                                });
                                        });
                                        $scope.$apply(function () {
                                            if (countqueue !== 0 && $scope.edits.length >= countqueue) {
                                                if (terminateStream === 1) {
                                                    $scope.recentChange.disconnect();
                                                    return;
                                                }
                                                $scope.edits.pop();
                                            }
                                            var new_el = {
                                                "server_url": stuff.server_url,
                                                "server_name": stuff.server_name,
                                                "script_path": stuff.server_script_path,
                                                "server_uri": stuff.meta.uri,
                                                "wiki": stuff.wiki,
                                                "namespace": namespacetemp,
                                                "user": stuff.user,
                                                "title": stuff.title,
                                                "comment": stuff.comment,
                                                "old": stuff.revision.old,
                                                "new": stuff['revision']['new'],
                                                "isIp": "registered",
                                                "wikidata_title": null,
                                                "ores": undefined,
                                                "isNew": (stuff.type === "new") ? "N" : ""
                                            };
                                            $scope.genORES(stuff.wiki, stuff['revision']['new'], new_el);
                                            $scope.edits.unshift(new_el);
                                            if ((sound === 1 || sound === 4 || sound === 5) && typeof newSound !== "undefined")
                                                playSound(newSound, false);
                                        });
                                    }
                                });
                            }
                        }
                    });
                }
            };
        },
        disconnect: function () {
            if (!this.isConnected) return;
            this.source.close();
            this.isConnected = false;
        }
    }
    $scope.recentChange.connect();

    $scope.wikidata_title = function (stuff, nstmp, reg) {
        var namespacetemp = nstmp;
        var wikidata_title = null;
        if (stuff.title.search(/^P\d*?$/gm) !== -1 || stuff.title.search(/^Q\d*?$/gm) !== -1) {
            var url = "https://www.wikidata.org/w/api.php?action=wbgetentities&ids=" + encodeURIComponent(stuff.title) + "&props=labels&languages=en&format=json&utf8=1";
            $.ajax({
                url: url, type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / SSE parsing');
                },
                crossDomain: true, dataType: 'jsonp', success: function (wikidatatitle) {
                    if (wikidatatitle.hasOwnProperty("entities"))
                        if (wikidatatitle["entities"].hasOwnProperty(stuff.title))
                            if (wikidatatitle["entities"][stuff.title].hasOwnProperty("labels"))
                                if (wikidatatitle["entities"][stuff.title]["labels"].hasOwnProperty("en"))
                                    if (wikidatatitle["entities"][stuff.title]["labels"]["en"].hasOwnProperty("value"))
                                        if (wikidatatitle["entities"][stuff.title]["labels"]["en"]["value"] !== null || wikidatatitle["entities"][stuff.title]["labels"]["en"]["value"] !== "")
                                            wikidata_title = wikidatatitle["entities"][stuff.title]["labels"]["en"]["value"];
                    $scope.$apply(function () {
                        if (countqueue !== 0 && $scope.edits.length >= countqueue) {
                            if (terminateStream === 1) {
                                $scope.recentChange.disconnect();
                                return;
                            }
                            $scope.edits.pop();
                        }
                        var new_el = {
                            "server_url": stuff.server_url,
                            "server_name": stuff.server_name,
                            "script_path": stuff.server_script_path,
                            "server_uri": stuff.meta.uri,
                            "wiki": stuff.wiki,
                            "namespace": namespacetemp,
                            "user": stuff.user,
                            "title": stuff.title,
                            "comment": stuff.comment,
                            "old": stuff.revision.old,
                            "new": stuff['revision']['new'],
                            "isIp": reg,
                            "wikidata_title": wikidata_title,
                            "ores": undefined,
                            "isNew": (stuff.type === "new") ? "N" : ""
                        };
                        $scope.genORES(stuff.wiki, stuff['revision']['new'], new_el);
                        $scope.edits.unshift(new_el);
                        if ((sound === 1 || sound === 4 || sound === 5) && typeof newSound !== "undefined")
                            playSound(newSound, false);
                    });
                }, error: function () {
                    $scope.$apply(function () {
                        if (countqueue !== 0 && $scope.edits.length >= countqueue) {
                            if (terminateStream === 1) {
                                $scope.recentChange.disconnect();
                                return;
                            }
                            $scope.edits.pop();
                        }
                        var new_el = {
                            "server_url": stuff.server_url,
                            "server_name": stuff.server_name,
                            "script_path": stuff.server_script_path,
                            "server_uri": stuff.meta.uri,
                            "wiki": stuff.wiki,
                            "namespace": namespacetemp,
                            "user": stuff.user,
                            "title": stuff.title,
                            "comment": stuff.comment,
                            "old": stuff.revision.old,
                            "new": stuff['revision']['new'],
                            "isIp": reg,
                            "wikidata_title": wikidata_title,
                            "ores": undefined,
                            "isNew": (stuff.type === "new") ? "N" : ""
                        };
                        $scope.genORES(stuff.wiki, stuff['revision']['new'], new_el);
                        $scope.edits.unshift(new_el);
                        if ((sound === 1 || sound === 4 || sound === 5) && typeof newSound !== "undefined")
                            playSound(newSound, false);
                    });
                }
            });
        } else {
            $scope.$apply(function () {
                if (countqueue !== 0 && $scope.edits.length >= countqueue) {
                    if (terminateStream === 1) {
                        $scope.recentChange.disconnect();
                        return;
                    }
                    $scope.edits.pop();
                }
                var new_el = {
                    "server_url": stuff.server_url,
                    "server_name": stuff.server_name,
                    "script_path": stuff.server_script_path,
                    "server_uri": stuff.meta.uri,
                    "wiki": stuff.wiki,
                    "namespace": namespacetemp,
                    "user": stuff.user,
                    "title": stuff.title,
                    "comment": stuff.comment,
                    "old": stuff.revision.old,
                    "new": stuff['revision']['new'],
                    "isIp": reg,
                    "wikidata_title": wikidata_title,
                    "ores": undefined,
                    "isNew": (stuff.type === "new") ? "N" : ""
                };
                $scope.genORES(stuff.wiki, stuff['revision']['new'], new_el);
                $scope.edits.unshift(new_el);
                if ((sound === 1 || sound === 4 || sound === 5) && typeof newSound !== "undefined")
                    playSound(newSound, false);
            });
        }
    };

    $scope.removeLast = function () {
        $timeout(function () {
            $scope.$apply(function () {
                if ($scope.edits.length >= countqueue)
                    while ($scope.edits.length > countqueue)
                        $scope.edits.pop();
                else $scope.recentChange.connect();
            });
        }, 0);
    };

    $scope.isCURRENT = function (tSERVER_URL, tSCRIPT_PATH, tTITLE, tDNEW, tOLD, CALLBACK) {
        var url = tSERVER_URL + tSCRIPT_PATH + "/api.php?action=query&prop=revisions&titles=" + encodeURIComponent(tTITLE).replace(/'/g, '%27') + "&rvprop=ids|timestamp|user|comment&rvlimit=1&format=json&utf8=1";
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / isCurrent');
            },
            crossDomain: true,
            dataType: 'jsonp',
            success: function (data) {
                if (typeof data.error !== "undefined") {
                    document.getElementById('page').srcdoc = starterror + "Opening error; server error info: " + escapeXSS(data.error.info) + enderror;
                    CALLBACK(null);
                    return;
                }

                if (typeof data["query"]["pages"] !== "undefined") {
                    var pageId = "";
                    for (var k in data["query"]["pages"]) {
                        pageId = k;
                    }
                }

                if (typeof data["query"]["pages"][pageId]["revisions"][0]["revid"] == "undefined" || typeof data["query"]["pages"] == "undefined" || data["query"]["pages"] === "-1") {
                    if (typeof data.error !== "undefined")
                        document.getElementById('page').srcdoc = starterror + "Opening error. Maybe page was deleted.<br><small>Server error info: " + escapeXSS(data.error.info) + "</small>" + enderror;
                    else
                        document.getElementById('page').srcdoc = starterror + "Opening error. Maybe page was deleted.";
                    CALLBACK(null);
                    return;
                }

                if (tDNEW === data["query"]["pages"][pageId]["revisions"][0]["revid"]) {
                    timestamp = data["query"]["pages"][pageId]["revisions"][0]["timestamp"];
                    CALLBACK(true);
                    return;
                }

                if (tOLD == null)
                    old = tDNEW;
                else
                    old = tOLD;
                user = data["query"]["pages"][pageId]["revisions"][0]["user"];
                dnew = data["query"]["pages"][pageId]["revisions"][0]["revid"];
                summary = data["query"]["pages"][pageId]["revisions"][0]["comment"];
                $scope.user = user;
                if (i > 0) {
                    edits_history[i - 1]["user"] = user;
                    edits_history[i - 1]["dnew"] = dnew;
                    edits_history[i - 1]["summary"] = summary;
                    if (tOLD == null)
                        edits_history[i - 1]["old"] = old;
                }
                var url = tSERVER_URL + tSCRIPT_PATH + "/api.php?action=compare&format=json&uselang=en&fromrev=" + old + "&torev=" + dnew + "&utf8=1&prop=diff";
                $.ajax({
                    url: url,
                    type: 'GET',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / isCurrent');
                    },
                    crossDomain: true,
                    dataType: 'jsonp',
                    success: function (data) {
                        if (typeof data.error !== "undefined") {
                            document.getElementById('page').srcdoc = starterror + "Opening error; Server error info: " + escapeXSS(data.error.info) + enderror;
                            CALLBACK(null);
                            return;
                        }
                        if (typeof data.compare['*'] == "undefined") {
                            document.getElementById('page').srcdoc = starterror + "Opening error. Maybe page has been deleted.<br><small>Server error info: " + escapeXSS(data.error.info) + "</small>" + enderror;
                            CALLBACK(null);
                        } else {
                            url = tSERVER_URL + tSCRIPT_PATH + "/api.php?action=query&list=users&ususers=" + encodeURIComponent(user).replace(/'/g, '%27') + "&usprop=editcount&utf8&format=json";
                            $.ajax({
                                url: url,
                                type: 'GET',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / isCurrent');
                                },
                                crossDomain: true,
                                dataType: 'jsonp',
                                success: function (data4) {
                                    lastOpenedPW = undefined;
                                    if (typeof data4.error !== "undefined") {
                                        document.getElementById('page').srcdoc = starterror + "Opening error; server error info: " + escapeXSS(data.error.info) + enderror;
                                        CALLBACK(null);
                                        return;
                                    }

                                    if (typeof data4["query"]["users"][0]["editcount"] == "undefined") {
                                        userip = "ip";
                                        document.getElementById("userLinkSpec").style.color = "green";
                                        document.getElementById("userLinkSpec").textContent = user;
                                        if (i > 0)
                                            edits_history[i - 1]["userip"] = "ip";
                                    } else {
                                        userip = "registered";
                                        document.getElementById("userLinkSpec").style.color = "#3366BB";
                                        document.getElementById("userLinkSpec").textContent = user;
                                        if (i > 0)
                                            edits_history[i - 1]["userip"] = "registered";
                                    }
                                    document.getElementById('com').textContent = "Comment: " + summary;
                                    if (data.compare['*'] === "" || data.compare['*'].indexOf("<tr>") === -1)
                                        document.getElementById('page').srcdoc = starterror + "This edit has been reverted already." + enderror;
                                    else {
                                        if (wiki !== "commonswiki" && wiki !== "wikidatawiki")
                                            document.getElementById('page').srcdoc = diffstart + escapeXSSDiff(data.compare['*']) + diffend;
                                        else
                                            document.getElementById('page').srcdoc = diffstart + structuredData(data.compare['*'].replace(/<a class="mw-diff-movedpara-left".*?<\/a>/g, '-').replace(/<a class="mw-diff-movedpara-right".*?<\/a>/g, '+').replace(/<a name="movedpara_.*?<\/a>/g, ''), tSERVER_URL) + diffend;
                                    }
                                    document.getElementById('page').scrollTop = 0;
                                    $scope.$apply(function () {
                                        $scope.edits.map(function (e, index) {
                                            if (e.wiki === wiki && e.title === title) {
                                                $scope.edits.splice($scope.edits.indexOf($scope.edits[index]), 1);
                                            }
                                        });
                                    });
                                    if (data.compare['*'] !== "" || data.compare['*'].indexOf("<tr>") !== -1) {
                                        document.getElementById('editForm').style.display = 'none';
                                        alert("Can't perform this action, this is not latest revision. Loaded new revision.");
                                    }
                                    CALLBACK(false);
                                }, error: function (error) {
                                    lastOpenedPW = undefined;
                                    alert('Failed... dev code: 001; error code: ' + error.status + '.');
                                }
                            });
                        }
                    }, error: function (error) {
                        alert('Failed... dev code: 002; error code: ' + error.status + '.');
                    }
                });
            }, error: function (error) {
                alert('Failed... dev code: 003; error code: ' + error.status + '.');
            }
        });
    };

    $scope.reqEnd = function (tDATA) {
        user = tDATA["user"];
        old = tDATA["oldrevid"];
        dnew = tDATA["newrevid"];
        $scope.user = user;
        summary = tDATA["summary"];
        if (i > 0) {
            edits_history[i - 1]["user"] = user;
            edits_history[i - 1]["old"] = old;
            edits_history[i - 1]["dnew"] = dnew;
            edits_history[i - 1]["summary"] = summary;
            edits_history[i - 1]["userip"] = "registered";
        }
        userip = "registered";
        var url = server_url + script_path + "/api.php?action=compare&format=json&uselang=en&fromrev=" + old + "&torev=" + dnew + "&utf8=1&prop=diff";
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / reqEnd');
            },
            crossDomain: true,
            dataType: 'jsonp',
            success: function (data) {
                if (typeof data.error !== "undefined") {
                    document.getElementById('page').srcdoc = starterror + "Opening error; server error info: " + escapeXSS(data.error.infoescapeXSS) + enderror;
                    uiEnable();
                    return;
                }
                document.getElementById("userLinkSpec").style.color = "#3366BB";
                document.getElementById("userLinkSpec").textContent = user;
                document.getElementById('com').textContent = "Comment: " + summary;
                if (wiki !== "commonswiki" && wiki !== "wikidatawiki")
                    document.getElementById('page').srcdoc = diffstart + escapeXSSDiff(data.compare['*']) + diffend;
                else
                    document.getElementById('page').srcdoc = diffstart + structuredData(data.compare['*'].replace(/<a class="mw-diff-movedpara-left".*?<\/a>/g, '-').replace(/<a class="mw-diff-movedpara-right".*?<\/a>/g, '+').replace(/<a name="movedpara_.*?<\/a>/g, ''), server_url) + diffend;
                document.getElementById('page').scrollTop = 0;
                uiEnable();
            }, error: function (error) {
                alert('Failed... dev code: 009; error code: ' + error.status + '.');
                uiEnable();
            }
        });
    };

    var noDiffON = false;
    $scope.nextDiff = function () {
        if ($scope.edits.length > 0) {
            $scope.select($scope.edits[0]);
        } else if (!noDiffON) {
            noDiffON = true;
            document.getElementById('next-diff-title').style.width = '70px';
            setTimeout(() => {
                noDiffON = false;
                document.getElementById('next-diff-title').style.width = '1px';
            }, 1000);
        }
    };

    $scope.openLink = function (tTYPE) {
        if (tTYPE === "page") {
            var urldiff = server_url + "/w/index.php?oldid=" + dnew;
            var diffWindow = window.open(urldiff, "_blank");
            diffWindow.location;
            diffWindow.focus();
            return;
        }
        var urldiff = server_url + "/wiki/Special:Contributions/" + encodeURIComponent(user).replace(/'/g, '%27');
        if (tTYPE === "diff") {
            var diffWindow = window.open(urldiff, "_blank");
            diffWindow.location;
            diffWindow.focus();
        }
    };
    $scope.copyViewHistory = () => copyToClipboard(encodeURI(`${$scope.project_url}/index.php?title=${$scope.title}&action=history`));
    $scope.copyGlobalContribs = () => copyToClipboard(encodeURI(`https://tools.wmflabs.org/guc/?src=hr&by=date&user=${$scope.user}`));
    $scope.copyCentralAuth = () => copyToClipboard(encodeURI(`https://meta.wikimedia.org/wiki/Special:CentralAuth?target=${$scope.user}`));

    function changeRollbacksDescription(wiki) {
        $scope.descriptions = config["wikis"][0]["others"][0]["rollback"];
        othersArray = config["wikis"][0]["others"][0];
        warn = null;
        protectArray = null;
        reportArray = null;
        if (config["wikis"][0].hasOwnProperty(wiki))
            if (config["wikis"][0][wiki][0].hasOwnProperty("rollback"))
                $scope.descriptions = config["wikis"][0][wiki][0]["rollback"];

        $scope.speedys = config["wikis"][0]["others"][0]["speedy"];
        if (config["wikis"][0].hasOwnProperty(wiki))
            if (config["wikis"][0][wiki][0].hasOwnProperty("speedy"))
                $scope.speedys = config["wikis"][0][wiki][0]["speedy"];

        speedySummary = "+ delete";
        if (config["wikis"][0].hasOwnProperty(wiki))
            if (config["wikis"][0][wiki][0].hasOwnProperty("speedySummary"))
                speedySummary = config["wikis"][0][wiki][0]["speedySummary"];

        if (config["wikis"][0].hasOwnProperty(wiki))
            if (config["wikis"][0][wiki][0].hasOwnProperty("rollback"))
                if (config["wikis"][0][wiki][0].hasOwnProperty("warn"))
                    warn = config["wikis"][0][wiki][0]["warn"][0];

        speedyWarnSummary = null;
        if (config["wikis"][0].hasOwnProperty(wiki))
            if (config["wikis"][0][wiki][0].hasOwnProperty("speedyWarnSummary"))
                speedyWarnSummary = config["wikis"][0][wiki][0]["speedyWarnSummary"];


        if (config["wikis"][0].hasOwnProperty(wiki))
            if (config["wikis"][0][wiki][0].hasOwnProperty("protect"))
                protectArray = config["wikis"][0][wiki][0]["protect"][0];

        if (config["wikis"][0].hasOwnProperty(wiki))
            if (config["wikis"][0][wiki][0].hasOwnProperty("report"))
                reportArray = config["wikis"][0][wiki][0]["report"][0];

    }

    document.getElementById('warn-box').onclick = function () {
        if (checkWarn === false) {
            this.classList.add('t-btn__active');
            checkWarn = true;
            if (defaultWarnList.indexOf(wiki) === -1) {
                defaultWarnList.push(wiki);
                $.ajax({
                    url: 'php/settings.php',
                    type: 'POST',
                    crossDomain: true,
                    data: {action: 'set', query: "defaultwarn", defaultwarn: defaultWarnList.join(',')},
                    dataType: 'json'
                });
            }

        } else {
            this.classList.remove('t-btn__active');
            checkWarn = false;
            if (defaultWarnList.indexOf(wiki) !== -1) {
                for (var dflcount = defaultWarnList.length - 1; dflcount >= 0; dflcount--) {
                    if (defaultWarnList[dflcount] === wiki) {
                        defaultWarnList.splice(dflcount, 1);
                    }
                }
                $.ajax({
                    url: 'php/settings.php',
                    type: 'POST',
                    crossDomain: true,
                    data: {action: 'set', query: "defaultwarn", defaultwarn: defaultWarnList.join(',')},
                    dataType: 'json'
                });
            }
        }
        $scope.$apply(function () {
            $scope.descriptions;
        });
    };

    document.getElementById("warn-box-delete").onclick = function () {
        if (checkWarnDelete === false) {
            this.classList.add('t-btn__active');
            checkWarnDelete = true;
            if (defaultDeleteList.indexOf(wiki) === -1) {
                defaultDeleteList.push(wiki);
                $.ajax({
                    url: 'php/settings.php',
                    type: 'POST',
                    crossDomain: true,
                    data: {action: 'set', query: "defauldelete", defaultdelete: defaultDeleteList.join(',')},
                    dataType: 'json'
                });
            }

        } else {
            this.classList.remove('t-btn__active');
            checkWarnDelete = false;
            if (defaultDeleteList.indexOf(wiki) !== -1) {
                for (var dflcount = defaultDeleteList.length - 1; dflcount >= 0; dflcount--) {
                    if (defaultDeleteList[dflcount] === wiki) {
                        defaultDeleteList.splice(dflcount, 1);
                    }
                }
                $.ajax({
                    url: 'php/settings.php',
                    type: 'POST',
                    crossDomain: true,
                    data: {action: 'set', query: "defaultdelete", defaultdelete: defaultDeleteList.join(',')},
                    dataType: 'json'
                });
            }
        }
        $scope.$apply(function () {
            $scope.speedy;
        });
    };

    var timeoutkey = setTimeout(function () {
        checktimeout = true;
    }, 5000);
    var checktimeout = true;
    document.onkeydown = function (e) {
        if (!e)
            e = window.event;
        var keyCode = e.which || e.keyCode || e.key;
        $scope.keyDownFunct(keyCode);
    };

    $scope.keyDownFunct = function (keyCode) {
        keyCode = Number(keyCode);
        if (keyCode !== 13 && keyCode !== 27 && keyCode !== 32 && keyCode !== 65 && keyCode !== 191 && keyCode !== 85 && keyCode !== 80 && keyCode !== 83 && keyCode !== 84 && keyCode !== 89 && keyCode !== 79 && keyCode !== 76 && keyCode !== 69 && keyCode !== 82 && keyCode !== 219 && keyCode !== 144 && keyCode !== 145 && keyCode !== 37 && keyCode !== 38 && keyCode !== 39 && keyCode !== 40)
            if (isNotModal()) {
                if (timeoutkey) {
                    clearTimeout(timeoutkey);
                    timeoutkey = null;
                    checktimeout = false;
                }
                timeoutkey = setTimeout(function () {
                    checktimeout = true;
                }, 5000);
            }

        if (checktimeout === false && isNotModal())
            return;

        if (keyCode === 82)
            if (isNotModal()) {
                document.getElementById('revert').click();
                return false;
            }
        if (keyCode === 89)
            if (isNotModal()) {
                document.getElementById('customRevertBtn').click();
                return false;
            }
        if (keyCode === 219 || keyCode === 80)
            if (isNotModal()) {
                document.getElementById('back').click();
                return false;
            }
        if (keyCode === 69)
            if (isNotModal()) {
                document.getElementById('editBtn').click();
                return false;
            }
        if (keyCode === 76)
            if (isNotModal()) {
                document.getElementById('btn-logs').click();
                return false;
            }
        if (keyCode === 79)
            if (isNotModal()) {
                document.getElementById('browser').click();
                return false;
            }
        if (keyCode === 84)
            if (isNotModal()) {
                document.getElementById('btn-talk').click();
                return false;
            }
        if (keyCode === 83)
            if (isNotModal()) {
                document.getElementById('btn-settings').click();
                return false;
            }
        if (keyCode === 85)
            if (isNotModal()) {
                document.getElementById('btn-unlogin').click();
                return false;
            }
        if (keyCode === 191)
            if (isNotModal()) {
                document.getElementById('luxo').click();
                return false;
            }
        if (keyCode === 65)
            if (isNotModal()) {
                if (document.getElementById('userLinkSpec').style.display !== "none") {
                    document.getElementById('userLinkSpec').click();
                    return false;
                }
            }
        if (keyCode === 32) {
            if (isNotModal()) {
                if ($scope.edits.length > 0) {
                    $scope.select($scope.edits[0]);
                    $scope.$digest();
                    return false;
                }
            }
        }
        if (keyCode === 27) {
            if (Object.keys(dialogStack).length !== 0) {
                removeDialog();
                return false;
            }
            else if (document.getElementById('POOverlay').classList.contains('po__overlay__active')) {
                closePO();
                return false;
            } else if (!document.getElementById('btn-home').classList.contains('tab__active')) {
                closePW();
                return false;
            }
        }
    };

});

function SHOW_DIFF(tSERVER_URL, tSERVER_NAME, tSCRIPT_PATH, tSERVER_URI, tWIKI, tNAMESPACE, tUSER, tOLD, tDNEW, tTITLE, tUSERIP, tSUMMARY, tWDT, checkPrev = false) {
    uiDisableList();
    closePW();
    closeMoreControl();
    document.getElementById('description-container').style.marginTop = '0px';
    if (tUSERIP === "registered")
        document.getElementById("userLinkSpec").style.color = "#3366BB";
    else
        document.getElementById("userLinkSpec").style.color = "green";
    if (tUSERIP === "ip")
        document.getElementById('CAUTH').classList.add('disabled');
    else
        document.getElementById('CAUTH').classList.remove('disabled');
    document.getElementById("userLinkSpec").textContent = tUSER;
    document.getElementById('com').textContent = "Comment: " + tSUMMARY;

    var diffTempNs = tNAMESPACE;
    if (tWIKI === "wikidatawiki") {
        if (tNAMESPACE === "<font color='brown'>Non-canon (146)</font>")
            diffTempNs = "Lexeme";
        if (tNAMESPACE === "<font color='brown'>Non-canon (122)</font>")
            diffTempNs = "Query";
        if (tNAMESPACE === "<font color='brown'>Non-canon (120)</font>")
            if (tTITLE.search(/^P\d*?$/gm) !== -1)
                diffTempNs = "Property";
        if (tWDT !== null) {
            document.getElementById("tit").setAttribute("aria-label", tWDT);
            document.getElementById("tit").setAttribute("i-tooltip", "bottom-left");
            document.getElementById("pageLinkSpec").style.borderBottom = "1px dotted var(--link-color)";
        } else {
            document.getElementById("tit").removeAttribute("aria-label");
            document.getElementById("tit").removeAttribute("i-tooltip");
            document.getElementById("pageLinkSpec").style.borderBottom = "unset";
        }
    } else {
        if (tWIKI === "enwiki" && tNAMESPACE === "<font color='brown'>Non-canon (118)</font>")
            diffTempNs = "Draft";
        if (tWIKI === "enwiki" && tNAMESPACE === "<font color='brown'>Non-canon (119)</font>")
            diffTempNs = "Draft talk";
        document.getElementById("tit").removeAttribute("aria-label");
        document.getElementById("tit").removeAttribute("i-tooltip");
        document.getElementById("pageLinkSpec").style.borderBottom = "unset";
    }

    document.getElementById('pageLinkSpec').textContent = tTITLE;
    if ((tWIKI === "testwiki") || (tWIKI === "test2wiki") || (tWIKI === "testwikidata") || (tWIKI === "testwikidatawiki"))
        document.getElementById('wiki').innerHTML = "Wiki: <font color='tomato'>" + tWIKI + "</font>";
    else
        document.getElementById('wiki').innerHTML = "Wiki: " + tWIKI;
    document.getElementById('ns').innerHTML = "Namespace: " + diffTempNs;

    if (typeof tOLD !== "undefined" && ((checkPrev === true && checkMode === 2) || checkPrev === "second")) {
        times = 2;
        var url = tSERVER_URL + tSCRIPT_PATH + "/api.php?action=query&prop=revisions&titles=" + encodeURIComponent(tTITLE) + "&rvprop=ids|user&rvlimit=1&rvexcludeuser=" + encodeURIComponent(tUSER) + "&rvstartid=" + tDNEW + "&format=json&utf8=1";
        $.ajax({
            url: url, type: 'GET', beforeSend: function (xhr) {
                xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / diff');
            }, crossDomain: true, dataType: 'jsonp', success: function (fdata) {
                if (typeof fdata["query"] !== "undefined" && typeof fdata["query"]["pages"] !== "undefined") {
                    var pageFData = null;
                    for (var k in fdata["query"]["pages"]) {
                        if (fdata["query"]["pages"].hasOwnProperty(k))
                            if (Number(k) !== -1)
                                pageFData = fdata["query"]["pages"][k];
                    }
                    if (pageFData !== null && typeof pageFData["revisions"] !== "undefined" && typeof pageFData["revisions"][0] !== "undefined" && typeof pageFData["revisions"][0]["revid"] !== "undefined" && pageFData["revisions"][0]["revid"] !== 0) {
                        tOLD = pageFData["revisions"][0]["revid"];
                        old = tOLD;
                        if (i > 0)
                            edits_history[i - 1]["old"] = old;
                    }
                }
                endShowDiff(tSERVER_URL, tSERVER_NAME, tSCRIPT_PATH, tSERVER_URI, tWIKI, tNAMESPACE, tUSER, tOLD, tDNEW);
            }, error() {
                endShowDiff(tSERVER_URL, tSERVER_NAME, tSCRIPT_PATH, tSERVER_URI, tWIKI, tNAMESPACE, tUSER, tOLD, tDNEW);
            }
        });
    } else {
        times = 1;
        endShowDiff(tSERVER_URL, tSERVER_NAME, tSCRIPT_PATH, tSERVER_URI, tWIKI, tNAMESPACE, tUSER, tOLD, tDNEW);
    }
}

function checkLast(tSERVER_URL, tSERVER_NAME, tSCRIPT_PATH, tSERVER_URI, tWIKI, tNAMESPACE, tUSER, tOLD, tDNEW, times, CALLBACK) {
    if (checkMode !== 1 ||times === 2) {
        CALLBACK(null);
        return;
    }
    if (typeof tOLD !== "undefined") {
        var url = tSERVER_URL + tSCRIPT_PATH + "/api.php?action=compare&fromrev=" + tDNEW + "&torelative=prev&prop=user&utf8=1&format=json";
        $.ajax({
            url: url, type: 'GET', beforeSend: function (xhr) {
                xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / checkLast');
            }, crossDomain: true, dataType: 'jsonp', success: function (fdata) {
                if (typeof fdata["compare"] !== "undefined" && typeof fdata["compare"]["fromuser"] !== "undefined") {
                    if (fdata["compare"]["fromuser"] === tUSER) {
                        CALLBACK(false);
                        return;
                    }
                }
                CALLBACK(null);
            }, error() {
                CALLBACK(null);
            }
        });
    } else
        CALLBACK(true);
}

function endShowDiff(tSERVER_URL, tSERVER_NAME, tSCRIPT_PATH, tSERVER_URI, tWIKI, tNAMESPACE, tUSER, tOLD, tDNEW) {
    var url = tSERVER_URL + tSCRIPT_PATH + "/api.php?action=compare&format=json&uselang=en&fromrev=" + tOLD + "&torev=" + tDNEW + "&utf8=1&prop=diff";
    if (typeof tOLD === "undefined") {
        uiDisableNew();
        url = tSERVER_URL + tSCRIPT_PATH + "/api.php?action=compare&format=json&uselang=en&fromrev=" + tDNEW + "&torelative=prev&utf8=1&prop=diff";
    }
    $.ajax({
        url: url,
        type: 'GET',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / diff');
        },
        crossDomain: true,
        dataType: 'jsonp',
        success: function (data) {
            if (typeof data.error !== "undefined") {
                if (data.error.code === "nosuchrevid")
                    document.getElementById('page').srcdoc = starterror + "Opening error. This page has been deleted." + enderror;
                else
                    document.getElementById('page').srcdoc = starterror + "Opening error. Maybe page has been deleted.<br><small>Server error info: " + escapeXSS(data.error.info) + "</small>" + enderror;
                uiEnable();
                return;
            }

            if (data.compare['*'] === "" || data.compare['*'].indexOf("<tr>") === -1) {
                if (typeof tOLD !== "undefined")
                    document.getElementById('page').srcdoc = starterror + "The edit has already was reverted." + enderror;
                else {
                    var newPageDiff = startstring + data.compare['*'] + endstring;
                    document.getElementById('page').srcdoc = newstart + newPageDiff + newend;
                }
            } else {
                var diffTextToFrame = data.compare['*'];
                if (typeof tOLD === "undefined")
                    diffTextToFrame = diffTextToFrame.replace(/(<td colspan="2" class="diff-lineno">)Line 1:(<\/td>)/, function ($0, $1, $2) {
                        return $1 + "Not exist" + $2;
                    });
                if (tWIKI !== "commonswiki" && tWIKI !== "wikidatawiki")
                    diffTextToFrame = escapeXSSDiff(diffTextToFrame);
                else
                    diffTextToFrame = structuredData(diffTextToFrame.replace(/<a class="mw-diff-movedpara-left".*?<\/a>/g, '-').replace(/<a class="mw-diff-movedpara-right".*?<\/a>/g, '+').replace(/<a name="movedpara_.*?<\/a>/g, ''), tSERVER_URL);
                document.getElementById('page').srcdoc = diffstart + diffTextToFrame + diffend;
            }
            uiEnable();
        },
        error: function (error) {
            alert('Failed... dev code: 010; error code: ' + error.status + '.');
            uiEnable();
        }
    });
    document.getElementById('page').scrollTop = 0;
}

function nextDiffStyle() {
    document.getElementById("page-welcome").style.display = "none";
    document.getElementById("description-container").style.display = "grid";
    document.getElementById("controlsBase").style.display = "block";
    document.getElementById("moreOptionBtnMobile").classList.remove('disabled');
    document.getElementById("page").style.display = "block";
}

function uiDisable() {
    document.getElementById("queue").classList.add("disabled");
    document.getElementById("control").classList.add("disabled");
    document.getElementById('next-diff').classList.add('disabled');
}

function uiDisableList() {
    document.getElementById("control").classList.add("disabled");
}

function uiDisableNew() {
    document.getElementById("revert").classList.add("disabled");
    document.getElementById("customRevertBtn").classList.add("disabled");
}

function uiEnable() {
    document.getElementById("queue").classList.remove("disabled");
    document.getElementById("control").classList.remove("disabled");
    document.getElementById('next-diff').classList.remove('disabled');
}

function uiEnableNew() {
    document.getElementById("queue").classList.remove("disabled");
    document.getElementById("revert").classList.remove("disabled");
    document.getElementById("customRevertBtn").classList.remove("disabled");
}

function isNotModal() {
    return !document.getElementById('customRevert').classList.contains("po__active") &&
        document.getElementById('textpage') !== document.activeElement &&
        document.getElementById('summaryedit') !== document.activeElement &&
        document.getElementById('phrase-send-talk') !== document.activeElement &&
        document.getElementById('logsSearch-input') !== document.activeElement &&
        document.getElementById('max-queue') !== document.activeElement &&
        document.getElementById('max-edits') !== document.activeElement &&
        document.getElementById('max-days') !== document.activeElement &&
        document.getElementById('ns-input') !== document.activeElement &&
        document.getElementById('bl-p') !== document.activeElement &&
        document.getElementById('wladdu') !== document.activeElement &&
        document.getElementById('wladdp') !== document.activeElement &&
        document.getElementById('statInput') !== document.activeElement &&
        Object.keys(dialogStack).length === 0 &&
        document.getElementById('page-welcome').style.display !== "block";
}

function selectTalkUsers(selectedUser) {
    document.getElementById("phrase-send-talk").value = "@" + selectedUser.textContent + ", " + document.getElementById("phrase-send-talk").value;
    document.getElementById("phrase-send-talk").focus();
    closePWDrawer('talkPWDrawer', 'talkPWOverlay');
}

function checkKey(k, wiki, targetArray) {
    var checkArrayKey = false;
    if (targetArray.hasOwnProperty(k))
        if (targetArray[k] !== null)
            if (targetArray[k] !== "")
                if (typeof targetArray[k] !== "undefined")
                    checkArrayKey = true;
    return checkArrayKey;
}

function keyDownFunctOutside(keyCode) {
    angular.element(document.getElementById('angularapp')).scope().keyDownFunct(keyCode);
}

function structuredData(str, server) {
    str = str.replace(/<a title="(.*?)" href="(\/w.*?)">(.*?)<\/a>/g, function ($0, $1, $2, $3) {
        return '<a title="' + $1 + '" href="' + server + $2 + '">' + $3 + '</a>';
    });
    return str;
}

function escapeXSS(str) {
    if (str === undefined) return 'undefined';
    str = str.replace(/&amp;/g, 'ampersanttempprepswv');
    str = str.replace(/&lt;/g, 'leftbracetempprepswv');
    str = str.replace(/&gt;/g, 'rightbracetempprepswv');
    str = str.replace(/<ins class="diffchange diffchange-inline">/g, 'inspreptempswv').replace(/<\/ins>/g, 'insendpreptempswv');
    str = str.replace(/<del class="diffchange diffchange-inline">/g, 'delpreptempswv').replace(/<\/del>/g, 'delendpreptempswv');

    str = str.replace(/&/g, '&ampprerep').replace(/;/g, '&#59;').replace(/&ampprerep/g, '&amp;').replace(/</g, '&lt;');
    str = str.replace(/>/g, '&gt;').replace(/ /g, '&#32;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/\//g, '&#47;');
    str = str.replace(/\\n/g, '&prerepn').replace(/\\/g, '&#92;').replace(/&prerepn/g, '\n').replace(/\(/g, '&#40;');
    str = str.replace(/\)/g, '&#41;').replace(/{/g, '&#123;').replace(/}/g, '&#125;');
    str = str.replace(/:/g, '&#58;').replace(/=/g, '&#61;').replace(/\?/g, '&#63;').replace(/@/g, '&#64;').replace(/\|/g, '&#124;');
    str = str.replace(/\[/g, '&#91;').replace(/]/g, '&#93;').replace(/\+/g, '&#43;').replace(/-/g, '&#45;').replace(/\*/g, '&#42;');
    str = str.replace(/,/g, '&#44;');

    str = str.replace(/leftbracetempprepswv/g, '&lt;');
    str = str.replace(/rightbracetempprepswv/g, '&gt;');
    str = str.replace(/ampersanttempprepswv/g, '&amp;');

    return str;
}

function escapeXSSDiff(str) {
    str = str.replace(/<a class="mw-diff-movedpara-left".*?<\/a>/g, '-').replace(/<a class="mw-diff-movedpara-right".*?<\/a>/g, '+').replace(/<a name="movedpara_.*?<\/a>/g, '');
    str = str.replace(/(<td class="diff-addedline"><div>)(.*?)(<\/div><\/td>)/g, function ($0, $1, $2, $3) {
        return $1 + escapeXSS($2) + $3;
    });
    str = str.replace(/(<td class="diff-deletedline"><div>)(.*?)(<\/div><\/td>)/g, function ($0, $1, $2, $3) {
        return $1 + escapeXSS($2) + $3;
    });
    str = str.replace(/(<td class="diff-context"><div>)(.*?)(<\/div><\/td>)/g, function ($0, $1, $2, $3) {
        return $1 + escapeXSS($2) + $3;
    });
    str = str.replace(/(<ins class="diffchange diffchange-inline">)(.*?)(<\/ins>)/g, function ($0, $1, $2, $3) {
        return $1 + escapeXSS($2) + $3;
    });
    str = str.replace(/(<del class="diffchange diffchange-inline">)(.*?)(<\/del>)/g, function ($0, $1, $2, $3) {
        return $1 + escapeXSS($2) + $3;
    });

    str = str.replace(/inspreptempswv/g, '<ins class="diffchange diffchange-inline">').replace(/insendpreptempswv/g, '</ins>');
    str = str.replace(/delpreptempswv/g, '<del class="diffchange diffchange-inline">').replace(/delendpreptempswv/g, '</del>');

    return str;
}