angular.module("swv", ["ui.directives", "ui.filters"]).controller("Queue", function($scope) {
    var server_url, server_name, script_path, server_uri, namespace, user, old, dnew, title, userip, wiki, timestamp, summary, withoutSection, speedySummary, othersArray, protectArray, reportArray, speedySection, speedyWarnSummary, warn;
    const ns = ["<font color='green'>Main</font>", "<font color='tomato'>Talk</font>", "<font color='tomato'>User</font>", "<font color='tomato'>User talk</font>", "<font color='orange'>Project</font>", "<font color='tomato'>Project talk</font>", "<font color='orange'>File</font>", "<font color='tomato'>File talk</font>", "<font color='tomato'>MediaWiki</font>", "<font color='tomato'>MediaWiki talk</font>", "<font color='orange'>Template</font>", "<font color='tomato'>Template talk</font>", "<font color='orange'>Help</font>", "<font color='tomato'>Help talk</font>", "<font color='orange'>Category</font>", "<font color='tomato'>Category talk</font>"];
    const wikis = ["afwiki", "alswiki", "amwiki", "anwiki", "angwiki", "arwikisource", "arwikiversity", "aswikisource", "astwiki", "avwiki", "azwiki", "wikisource", "wikisourcewiki", "sourceswiki", "bat_smgwiki", "bgwikibooks", "brwiki", "bswiki", "bswikiquote", "cawikiquote", "cswiktionary", "csbwiki", "csbwiktionary", "cywiki", "dawikisource", "dewikisource", "dinwiki", "diqwiki", "elwikiquote", "elwikisource", "enwikiversity", "eowikinews", "eowiki", "eowikisource", "eswiktionary", "etwikibooks", "fawikiquote", "fawikivoyage", "fiwikisource", "frwikinews", "frpwiki", "gawiki", "glwikibooks", "hewikibooks", "hewikiquote", "hrwikisource", "hrwiktionary", "htwiki", "huwikibooks", "huwikiquote", "huwiktionary", "hywiktionary", "iawiktionary", "idwikiquote", "iewiktionary", "iowiki", "iswiktionary", "jvwiki", "kawiki", "kkwiki", "kowikibooks", "kowikisource", "kuwiki", "kywiki", "lawiki", "lawikisource", "lawiktionary", "ladwiki", "lbwiki", "liwiki", "lmowiki", "ltgwiki", "lvwiki", "maiwiki", "map_bmswiki", "metawiki", "mgwiki", "mkwikibooks", "mlwiki", "mlwiktionary", "mrwikisource", "mrjwiki", "mswiktionary", "ndswiktionary", "nds_nlwiki", "newwiki", "nlwikisource", "nowikisource", "orwikisource", "pamwiki", "pdcwiki", "plwikinews", "plwikiquote", "pnbwiktionary", "pswiki", "rmywiki", "rowikinews", "ruewiki", "sawiki", "sawiktionary", "sdwiktionary", "siwiki", "skwiktionary", "sqwiki", "srwikisource", "stwiktionary", "stqwiki", "svwikibooks", "svwikinews", "svwikiversity", "tawikinews", "tawiki", "tewiki", "ttwiki", "tyvwiki", "ugwiki", "urwiki", "vepwiki", "vlswiki", "yiwiktionary", "zhwikiquote", "zh_classicalwiki", "zh_yuewiki", "abwiki", "adywiki", "afwiktionary", "angwiktionary", "arcwiki", "aywiki", "aywiktionary", "barwiki", "be_x_oldwiki", "biwiki", "bnwikisource", "bswikisource", "cawikibooks", "crwiki", "crhwiki", "cuwiki", "cvwiki", "dewikibooks", "dewiktionary", "dvwiki", "dvwiktionary", "eewiki", "eowikibooks", "eswikibooks", "eswikinews", "eswikiquote", "eswikisource", "eswikiversity", "euwikiquote", "fawiktionary", "fjwiki", "fowiktionary", "fywiki", "glwiktionary", "guwiki", "hewikisource", "hewiktionary", "hiwiktionary", "hsbwiki", "hsbwiktionary", "huwikisource", "hywikibooks", "iawiki", "idwikibooks", "iewiki", "jbowiki", "kawiktionary", "klwiki", "kmwikibooks", "kmwiktionary", "knwiki", "kuwikiquote", "kvwiki", "kywikibooks", "ltwikisource", "ltwiktionary", "lvwiktionary", "mediawikiwiki", "mznwiki", "nawiktionary", "nahwiktionary", "ndswiki", "newiki", "newiktionary", "nlwiktionary", "nnwikiquote", "nnwiktionary", "ocwiki", "outreachwiki", "pagwiki", "papwiki", "piwiki", "plwikibooks", "ptwikibooks", "ptwikinews", "ptwikiquote", "ptwikisource", "ptwikivoyage", "ptwiktionary", "quwiki", "rmwiki", "ruwikiquote", "ruwikisource", "rwwiki", "sawikisource", "sahwikisource", "scowiki", "skwikibooks", "skwiki", "skwikiquote", "slwikibooks", "slwiki", "snwiki", "sowiki", "specieswiki", "sqwikibooks", "sqwikiquote", "stwiki", "suwiktionary", "swwiktionary", "tawikiquote", "tawikisource", "tgwiki", "tgwiktionary", "thwikibooks", "thwikiquote", "thwikisource", "thwiktionary", "tkwiki", "tnwiki", "towiki", "tpiwiki", "twwiki", "tywiki", "udmwiki", "ukwikiquote", "uzwiki", "viwikibooks", "viwikisource", "vowiki", "vowiktionary", "wowiki", "xhwiki", "yiwikisource", "yowiki", "zh_min_nanwiki", "amwiktionary", "arwikinews", "arwiktionary", "astwiktionary", "bewikisource", "betawikiversity", "bmwiki", "bnwiki", "brwiktionary", "bswikibooks", "bswiktionary", "bxrwiki", "cawiktionary", "cswikinews", "cswikiquote", "cswikiversity", "dawikibooks", "dewikinews", "dewikiquote", "dtywiki", "enwikibooks", "enwikiquote", "etwiki", "etwikiquote", "euwikibooks", "extwiki", "fawikibooks", "ffwiki", "fiwikinews", "fiwiktionary", "fjwiktionary", "frwikibooks", "frwikiversity", "ganwiki", "gdwiki", "glwikiquote", "gnwiktionary", "gotwiki", "guwikisource", "gvwiki", "hewikinews", "hiwikibooks", "hiwiki", "hifwiki", "hrwiki", "iawikibooks", "idwikisource", "ikwiki", "incubatorwiki", "jamwiki", "kaawiki", "kabwiki", "kbdwiki", "kgwiki", "kiwiki", "knwikiquote", "kswiki", "kuwikibooks", "kuwiktionary", "kwwiki", "kywikiquote", "liwikisource", "ltwikibooks", "ltwikiquote", "mdfwiki", "mgwikibooks", "miwiki", "mlwikiquote", "mrwikibooks", "mrwikiquote", "mrwiktionary", "mtwiki", "myvwiki", "nlwikibooks", "nowikibooks", "nowiktionary", "novwiki", "nvwiki", "olowiki", "omwiki", "oswiki", "plwikisource", "plwikivoyage", "ptwikiversity", "quwiktionary", "rowikiquote", "rowikisource", "ruwikibooks", "ruwikimedia", "ruwikinews", "ruwiktionary", "sahwiki", "sewiki", "sgwiki", "slwiktionary", "sqwikinews", "srwikibooks", "srwikinews", "srnwiki", "sswiki", "suwiki", "svwikiquote", "svwikisource", "swwiki", "szlwiki", "tcywiki", "tewikisource", "tewiktionary", "thwiki", "tiwiki", "tlwiki", "ttwiktionary", "ukwikinews", "ukwiktionary", "urwikiquote", "sourceswiki", "wuuwiki", "xalwiki", "zhwikibooks", "acewiki", "afwikiquote", "anwiktionary", "arwikibooks", "azwikiquote", "azwiktionary", "bewiki", "bewiktionary", "bgwiki", "bgwikiquote", "bgwiktionary", "bjnwiki", "bpywiki", "brwikisource", "bswikinews", "bugwiki", "cdowiki", "chrwiki", "chywiki", "ckbwiki", "cswikibooks", "cswikisource", "dawiktionary", "dsbwiki", "elwikinews", "elwikiversity", "elwiktionary", "eowiktionary", "etwikisource", "etwiktionary", "fawikinews", "fiwikibooks", "fiwikiversity", "fiwikivoyage", "fiu_vrowiki", "fowiki", "fowikisource", "fywikibooks", "gagwiki", "glkwiki", "guwikiquote", "guwiktionary", "hawiki", "hywiki", "hywikiquote", "idwiktionary", "ilowiki", "iowiktionary", "iswiki", "jvwiktionary", "kawikibooks", "kkwikibooks", "kkwiktionary", "kmwiki", "kowikiversity", "koiwiki", "krcwiki", "kwwiktionary", "lezwiki", "lgwiki", "liwikibooks", "liwiktionary", "lnwiktionary", "mhrwiki", "mnwiki", "mnwiktionary", "mswikibooks", "mwlwiki", "newikibooks", "nlwikiquote", "nowikiquote", "nsowiki", "nycwikimedia", "orwiktionary", "pawikibooks", "pawikisource", "pflwiki", "pihwiki", "plwiktionary", "pnbwiki", "pntwiki", "pswiktionary", "rowiktionary", "ruwikiversity", "rwwiktionary", "sawikibooks", "sawikiquote", "sdwiki", "siwikibooks", "siwiktionary", "slwikiquote", "slwikiversity", "specieswiki", "srwiktionary", "suwikiquote", "svwiktionary", "tawikibooks", "tewikiquote", "tgwikibooks", "trwikibooks", "trwikimedia", "trwikinews", "trwikiquote", "trwikisource", "trwiktionary", "tswiki", "ukwikibooks", "uzwikiquote", "vewiki", "vecwiktionary", "viwikiquote", "viwiktionary", "wawiki", "xmfwiki", "yiwiki", "zeawiki", "zhwikinews", "zhwikisource", "zh_min_nanwiktionary", "afwikibooks", "akwiki", "arwikiquote", "arzwiki", "aswiki", "azwikibooks", "azwikisource", "bawiki", "bclwiki", "bewikibooks", "bewikiquote", "bgwikinews", "bgwikisource", "bhwiki", "bnwikibooks", "bnwiktionary", "bowiki", "brwikiquote", "cawikinews", "cawikisource", "cbk_zamwiki", "cewiki", "chwiki", "chrwiktionary", "cowiki", "cvwikibooks", "cywikibooks", "cywikiquote", "cywikisource", "cywiktionary", "dawikiquote", "dewikiversity", "dkwikimedia", "dzwiki", "elwikibooks", "eowikiquote", "euwiki", "euwiktionary", "fawikisource", "fiwikiquote", "frwikiquote", "frrwiki", "fywiktionary", "gawiktionary", "gdwiktionary", "glwiki", "glwikisource", "gnwiki", "gvwiktionary", "hawiktionary", "hakwiki", "hawwiki", "hiwikiquote", "hrwikibooks", "hrwikiquote", "hywikisource", "igwiki", "iswikibooks", "iswikiquote", "iswikisource", "iuwiki", "iuwiktionary", "jbowiktionary", "kawikiquote", "klwiktionary", "knwikisource", "knwiktionary", "kowikinews", "kowikiquote", "kowiktionary", "kswiktionary", "kshwiki", "kywiktionary", "lawikibooks", "lawikiquote", "lbwiktionary", "lbewiki", "liwikiquote", "lnwiki", "lowiki", "lowiktionary", "ltwiki", "miwiktionary", "minwiki", "mkwikisource", "mkwiktionary", "mlwikibooks", "mlwikisource", "mswiki", "mtwiktionary", "mywiki", "mywiktionary", "nawiki", "nahwiki", "nlwikimedia", "nowikinews", "nrmwiki", "nywiki", "ocwikibooks", "ocwiktionary", "omwiktionary", "orwiki", "pawiki", "pawiktionary", "pcdwiki", "plwikimedia", "rnwiki", "rowikibooks", "roa_rupwiki", "roa_rupwiktionary", "sgwiktionary", "shwiktionary", "skwikisource", "slwikisource", "smwiki", "smwiktionary", "sowiktionary", "sqwiktionary", "srwikiquote", "sswiktionary", "tawiktionary", "tewikibooks", "tetwiki", "tiwiktionary", "tkwiktionary", "tlwikibooks", "tlwiktionary", "tnwiktionary", "tpiwiktionary", "tswiktionary", "ttwikibooks", "tumwiki", "uawikimedia", "ugwiktionary", "ukwikisource", "urwikibooks", "urwiktionary", "uzwiktionary", "wawiktionary", "wowikiquote", "wowiktionary", "zawiki", "zh_min_nanwikisource", "zuwiki", "zuwiktionary", "arwikimedia", "bdwikimedia", "bewikimedia", "brwikimedia", "cawikimedia", "cowikimedia", "etwikimedia", "fiwikimedia", "mkwikimedia", "mxwikimedia", "nowikimedia", "sewikimedia", "testwikidatawiki", "wikimania2018wiki", "ptwikimedia", "bawikibooks", "itwikibooks", "itwikinews", "jawikinews", "nlwikinews", "liwikinews", "furwiki", "lijwiki", "roa_tarawiki", "scwiki", "lrcwiki", "gomwiki", "atjwiki", "kbpwiki", "gorwiki", "inhwiki", "lfnwiki", "satwiki", "shnwiki", "jawikiquote", "sahwikiquote", "jawikisource", "vecwikisource", "euwikisource", "pmswikisource", "itwikiversity", "jawikiversity", "hiwikiversity", "zhwikiversity", "frwikivoyage", "itwikivoyage", "nlwikivoyage", "ruwikivoyage", "svwikivoyage", "eswikivoyage", "rowikivoyage", "elwikivoyage", "hewikivoyage", "ukwikivoyage", "viwikivoyage", "zhwikivoyage", "hiwikivoyage", "bnwikivoyage", "pswikivoyage", "cowiktionary", "hifwiktionary", "yuewiktionary", "wikimaniawiki", "testcommonswiki"];
    const active_users = ["alswikibooks", "jawikibooks", "enwikinews", "cebwiki", "emlwiki", "mkwiki", "napwiki", "nnwiki", "pmswiki", "scnwiki", "shwiki", "vecwiki", "warwiki", "azbwiki", "alswikiquote", "itwikiquote", "frwikisource", "itwikisource", "dewikivoyage", "alswiktionary", "itwiktionary", "jawiktionary", "mgwiktionary", "mowiktionary", "scnwiktionary", "simplewiktionary", "zhwiktionary"];
    var edits_history = [];
    var vandalsReport = [];
    $scope.descriptions = config["wikis"][0]["others"][0]["rollback"];
    $scope.speedys = config["wikis"][0]["others"][0]["speedy"];
    $scope.offlineUsers = offlineUsers;
    $scope.users = [];
    var countConnectAttemp = 0;
    var checkWarn = false;
    var checkWarnDelete = false;
    var isdelete = false;
    var warnDelete = null;

    var i = 0;
    checkRollback = false;
    $scope.select = function(edit) {
        uiEnableNew();
        document.getElementById("queue").classList.add("disabled"); // disable queue during change diff
        if ((typeof user !== "undefined") && (i == 0)) {
            if (edits_history.length == 6)
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
        changeRollbacksDescription(wiki);
        SHOW_DIFF(edit.server_url, edit.server_name, edit.script_path, edit.server_uri, edit.wiki, edit.namespace, edit.user, edit.old, edit['new'], edit.title, edit.isIp, edit.comment);
        $scope.edits.splice($scope.edits.indexOf(edit), 1);
    };
    $scope.Back = function() {
        if (edits_history.length > 0 && edits_history.length - 1 >= i) {
            uiEnableNew();
            if (i == 0) {
                if (edits_history.length == 6) edits_history.splice(5, 1);
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
                    "summary": summary
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
            changeRollbacksDescription(wiki);
            SHOW_DIFF(server_url, server_name, script_path, server_uri, wiki, namespace, user, old, dnew, title, userip, summary);
            i = i + 1;
        }
    };
    $scope.editColor = function(edit) {
        if (vandals.indexOf(edit.user) !== -1 || (vandals.indexOf(edit.user) !== -1 && suspects.indexOf(edit.user) !== -1))
            return { color: "red" };
        else
        if (suspects.indexOf(edit.user) !== -1)
            return {color: "pink"};
    };
    $scope.descriptionColor = function(description) {
        if (checkWarn == true && description.warn !== null && typeof description.warn !== "undefined" && description.warn !== "")
            return { color: "var(--tc-lowSecondaryWarn)" };
        else {
            if (description.global == true) {
                return {color: "var(--tc-lowSecondaryGlobal)"};
            }
            if (typeof description.global == "undefined")
                return { color: "var(--tc-lowSecondary)" };
        }
    };
    $scope.speedyColor = function(speedy) {
        if (checkWarnDelete == true && speedy.warn !== null && typeof speedy.warn !== "undefined" && speedy.warn !== "")
            return { color: "var(--tc-lowSecondaryWarn)" };
        else
            return { color: "var(--link-color)" };
    };

    $scope.browser = function() {
        if (typeof dnew !== "undefined") {
            var urlbrowser;
            if (old !== null)
                urlbrowser = server_url + script_path + "/index.php?diff=" + dnew + "&oldid=" + old + "&uselang=en&redirect=no&mobileaction=toggle_view_desktop"
            else
                urlbrowser = server_uri + "?uselang=en&redirect=no&mobileaction=toggle_view_desktop";
            var diffWindow = window.open(urlbrowser, "_blank");
            diffWindow.location;
            diffWindow.focus();
        }
    };

    $scope.SD = function(tmpl, summary) {
        isdelete = true;
        var dtext = document.getElementById('textpage').value;
        document.getElementById('textpage').value = "<noinclude>" + tmpl + "</noinclude>\n" + dtext;
        document.getElementById('summaryedit').value = summary;
        setTimeout($scope.doEdit(), 500);
    };

    $scope.checkEdit = function() {
        if (typeof dnew == "undefined")
            return;

        $("#warn-box-delete").hide();
        $scope.speedys.forEach(function(elS){
            if (typeof elS.warn !== "undefined") {
                $("#warn-box-delete").fadeIn("slow");
                return;
            }
        });

        document.getElementById("editFormBody").classList.add("disabled");
        document.getElementById('textpage').value = "";
        // if (old == null)
        document.getElementById('btn-group-delete').style.display = "block";
        //  else
        //      document.getElementById('btn-group-delete').style.display = "none";

        $scope.isCURRENT(server_url, script_path, title, dnew, old, function(cb) {
            if (cb == null)
                return;
            if (cb == false)
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
                success: function(datapage) {
                    if (datapage == "Error! Loading page is not success")
                        alert('Failed... dev code: 004.1. Failed http-request. Maybe page was delete or server is down.');
                    else {
                        document.getElementById('textpage').value = "";
                        document.getElementById('summaryedit').value = "";
                        document.getElementById('textpage').value = datapage;
                        document.getElementById('textpage').focus();
                        document.getElementById('textpage').scrollTop = 0;
                        document.getElementById("editFormBody").classList.remove("disabled");
                    }
                }, error: function(error) { alert('Failed... dev code: 004; error code: ' + error.status + '.'); document.getElementById("editFormBody").classList.remove("disabled"); }
            });
        });
    };

    $scope.doEdit = function() {
        var isdeletetmp = false;
        if (isdelete == true) {
            isdelete = false;
            isdeletetmp = true;
        }
        if ( (document.getElementById('textpage').value == null) || (typeof document.getElementById('textpage').value == "undefined") )
            return;
        uiDisable();
        var textpage = document.getElementById('textpage').value;
        var summaryEdit = "";
        if ( (document.getElementById('summaryedit').value !== "") && (document.getElementById('summaryedit').value !== null) && (typeof document.getElementById('summaryedit').value !== "undefined") )
            summaryEdit = document.getElementById('summaryedit').value;
        document.getElementById('textpage').value = "";
        document.getElementById('summaryedit').value = "";
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
            success: function(dataedit) {
                dataedit = JSON.parse(dataedit);
                if ( dataedit["result"] == "Success") {
                    if (isdeletetmp == true) {
                        suspects.push(user);
                        var rawSend = {"type": "synch", "wiki": wiki, "nickname": user, "vandal": "2", "page": title};
                        connectTalk.talkSendInside(rawSend);
                    }
                    if (isdeletetmp == true && checkWarnDelete == true && warnDelete !== null && speedyWarnSummary !== null) {
                        $.ajax({ url: 'php/doEdit.php', type: 'POST',
                            beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Warns');},
                            crossDomain: true, dataType: 'text',
                            data: {
                                warn: "speedy",
                                project: server_url + script_path + "/api.php",
                                wiki: wiki,
                                page: "User_talk:" + user,
                                text: warnDelete.replace(/\$1/gi, title).replace(/\$2/gi, user),
                                sectiontitle: speedySection,
                                summary: speedyWarnSummary.replace(/\$1/gi, title)
                            },
                            success: function() {
                                $scope.reqEnd(dataedit);
                            },
                            error: function() {
                                $scope.reqEnd(dataedit);
                            }
                        });
                    }
                    else
                        $scope.reqEnd(dataedit);
                } else {
                    if (dataedit['code'] == "undofailure" || dataedit['code'] == "editconflict" || dataedit['code'] == "alreadyrolled") {
                        $scope.isCURRENT(server_url, script_path, title, dnew, old, function(cb2) {
                            if (cb2 == null) {
                                uiEnable();
                                return;
                            }
                            if (cb2 == false) {
                                uiEnable();
                                return;
                            }
                            document.getElementById('page').srcdoc = starterror + "Edit error: " + dataedit['result'] + enderror;
                            uiEnable();
                        });
                    }
                    else {
                        // if null-edit
                        if (dataedit['result'] == null) {
                            document.getElementById('page').srcdoc = starterror + "Such changes has already been made." + enderror;
                            $scope.isCURRENT(server_url, script_path, title, dnew, old, function(cb3) {
                                if (cb3 == null) {
                                    uiEnable();
                                    return;
                                }
                                if (cb3 == false) {
                                    uiEnable();
                                    return;
                                }
                                document.getElementById('page').srcdoc = starterror + "Such changes has already been made." + enderror;
                                uiEnable();
                            });
                        }
                        else {
                            document.getElementById('page').srcdoc = starterror + "Edit error: " + dataedit['result'] + enderror;
                            uiEnable();
                        }
                    }
                }
            }, error: function(error, e2, e3) {
                document.getElementById('page').srcdoc = starterror + "Failed... dev code: 007; error code: " + error.status + e2 + enderror;
                uiEnable();
            }
        });
    };


    $scope.customRevertSummary = function() {
        if ((old !== null) && (isNaN(old) == false)) {
            document.getElementById('credit').value = "";
            $('#customRevert').modal('show');

            if (warn !== null && typeof warn !== "undefined") {
                $("#warn-box").fadeIn("slow");
            } else {
                $("#warn-box").hide();
            }
            document.getElementById('credit').focus();
        }
    };

    $scope.selectDescription = function(description) {
        withoutSection = false;
        if (description.hasOwnProperty("summary"))
            if (description.summary !== null && description.summary !== "")
                if (checkWarn == true && warn !== null && typeof description.warn !== "undefined" && typeof warn[description.warn] !== "undefined") {
                    if (typeof description.withoutSection !== "undefined")
                        if (description.withoutSection == true)
                            withoutSection = true;
                    $scope.Revert(description.summary, description.warn);
                }
                else
                    $scope.Revert(description.summary, null);
    };

    $scope.selectSpeedy = function(speedy) {
        if (speedy.hasOwnProperty("template"))
            if (speedy.template !== null && speedy.template !== "") {
                warnDelete = null;
                speedySection = null;
                if (typeof speedy.warn !== "undefined" && speedy.warn !== null && speedy.warn !== "")
                    warnDelete = speedy.warn;
                if (typeof speedy.sectionWarn !== "undefined" && speedy.sectionWarn !== null && speedy.sectionWarn  !== "")
                    speedySection = speedy.sectionWarn.replace(/\$1/gi, title);
                $scope.SD(speedy.template, speedySummary);
            }
    };

    $scope.requestsForm = function() {
        // global block start
        var lineReport = [];
        vandalsReport.forEach( function (el, eln){
            if (el["user"] == user) {
                if (el["newid"] !== null) {
                    var n = {header: el['wiki'], project: el['project'], line: "<a href='" + el['project'] + "/wiki/Special:Diff/" + el['oldid'] + "/" + el['newid'] + "' target='_blank'>" + eln + "</a>"};
                    lineReport.push(n);
                }
                else {
                   var n = {header: el['wiki'], project: el['project'], line: "<a href='" + el['project'] + "/w/index.php?oldid=" + el['oldid'] + "'' target='_blank'>" + eln + "</a>"};
                   lineReport.push(n);
               }
           }
        });    
        var headersReport = [];
        var wikisReport = [];
        var reportHeader = "";
        var wikiReport = "";
        lineReport.forEach(function(el, eln) {
            createCB(eln, el["line"], "reportDiffs");
            if (headersReport.indexOf(el["header"]) == "-1") {
                headersReport.push(el["header"]);
                wikisReport.push(el["project"]);
            }
        });
        headersReport.forEach(function(header) {
            reportHeader += header + ", ";
        });
        reportHeader = reportHeader.slice(0,-2);
        wikisReport.forEach(function(p, pp) {
            wikiReport += "[" + p + "/wiki/Special:Contribs/" + encodeURIComponent(user).replace(/'/g, '%27') + " " + headersReport[pp] + "], ";
        });
        wikiReport = wikiReport.slice(0,-2);
        document.getElementById("reportHeader").value = reportHeader;
        if (wikisReport.length == 0)
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
        }
        else {
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
        }
        else {
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

        $('#requestsForm').modal('show');
    };

// .........................................................................................................................
    $scope.sendSRM = function() {
        var sectionSRM = "";
        if (typeof document.getElementById("othersHeaderGlobal").value == undefined || document.getElementById("othersHeaderGlobal").value == null || document.getElementById("othersHeaderGlobal").value == "")
            return;
        if (typeof document.getElementById("othersCommentGlobal").value == undefined || document.getElementById("othersCommentGlobal").value == null || document.getElementById("othersCommentGlobal").value == "")
            return;
        var sectionSRM = document.getElementById("othersHeaderGlobal").value;
        var textSRM = document.getElementById("othersCommentGlobal").value;
        var summarySRM = "+ new request (" + wiki + ")";
        var pageSRM = "Steward requests/Miscellaneous";

        $.ajax({ url: 'php/doEdit.php', type: 'POST', 
            beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / M.Report');},
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
            success: function(s) {
                if (s["result"] === false) {
                    $.ajax({ url: 'php/doEdit.php', type: 'POST', 
                        beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / M.Report');},
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
                        success: function() {
                            document.getElementById("requestsForm").style.display = "none";
                        },
                        error: function() {
                            alert("Unknow network error");
                            document.getElementById("requestsForm").style.display = "none";
                        },
                    });
                }
                else
                    alert("Already requested!");
            }
        });

    };


    $scope.sendReportLocal = function() {
        var sectionReport = "";
        if (checkKey("withoutSectionReport", wiki, reportArray) === false || reportArray["withoutSectionReport"] === false) {
            if (typeof document.getElementById("reportHeaderLocal").value == undefined || document.getElementById("reportHeaderLocal").value == null || document.getElementById("reportHeaderLocal").value == "")
                return;
            else
                sectionReport = document.getElementById("reportHeaderLocal").value;
        }
        if (typeof document.getElementById("reportCommentLocal").value == undefined || document.getElementById("reportCommentLocal").value == null || document.getElementById("reportCommentLocal").value == "")
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


        $.ajax({ url: 'php/doEdit.php', type: 'POST', 
            beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / M.Report');},
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
            success: function(s) {
                if (s["result"] === false) {
                    $.ajax({ url: 'php/doEdit.php', type: 'POST', 
                        beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / M.Report');},
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
                        success: function() {
                            document.getElementById("requestsForm").style.display = "none";
                        },
                        error: function() {
                            alert("Unknow network error");
                            document.getElementById("requestsForm").style.display = "none";
                        },
                    });
                }
                else
                    alert("Already requested!");
            }
        });
    };



    $scope.sendRequestProtect = function() {
        var sectionProtect = "";
        if (checkKey("withoutSectionProtect", wiki, protectArray) === false || protectArray["withoutSectionProtect"] === false) {
            if (typeof document.getElementById("protectHeaderLocal").value == undefined || document.getElementById("protectHeaderLocal").value == null || document.getElementById("protectHeaderLocal").value == "")
                return;
            else
                sectionProtect = document.getElementById("protectHeaderLocal").value;
        }
        if (typeof document.getElementById("protectCommentLocal").value == undefined || document.getElementById("protectCommentLocal").value == null || document.getElementById("protectCommentLocal").value == "")
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


        $.ajax({ url: 'php/doEdit.php', type: 'POST', 
            beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Protect');},
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
            success: function(s) {
                if (s["result"] === false) {
                    $.ajax({ url: 'php/doEdit.php', type: 'POST', 
                        beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Protect');},
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
                        success: function() {
                            document.getElementById("requestsForm").style.display = "none";
                        },
                        error: function() {
                            alert("Unknow network error");
                            document.getElementById("requestsForm").style.display = "none";
                        },
                    });
                }
                else
                    alert("Already requested!");
            }
        });
    };

    $scope.Revert = function(summaryPreset, warnPreset) {
        if ((old == null) || (isNaN(old) == true))
            return;
        uiDisable();
        var summarypre = "";
        if (summaryPreset !== null && typeof summaryPreset !== "undefined")
            summarypre = summaryPreset.replace(/\$7/gi, title);
        else {
            if (document.getElementById('credit').value !== "" && document.getElementById('credit').value !== null && document.getElementById('customRevert').style.display == "block")
                summarypre = document.getElementById('credit').value;
        }
        document.getElementById('credit').value = "";
        $('#customRevert').modal('hide');
        $scope.isCURRENT(server_url, script_path, title, dnew, old, function(cb) {
            if (cb == null) {
                uiEnable();
                return;
            }
            if (cb == false) {
                uiEnable();
                return;
            }

            var rbmode = "rollback";
            if (isGlobalModeAccess === true && local_wikis.indexOf(wiki) == -1)
                rbmode = "undo";

            var undoSummary = 'Undid edits by [[Special:Contribs/$2|$2]] ([[User talk:$2|talk]]) to last version by $1';
            if (config["wikis"][0].hasOwnProperty(wiki))
                if (config["wikis"][0][wiki][0].hasOwnProperty("defaultUndoSummary"))
                    if (config["wikis"][0][wiki][0]["defaultUndoSummary"] !== null && config["wikis"][0][wiki][0]["defaultUndoSummary"] !== "")
                        undoSummary = config["wikis"][0][wiki][0]["defaultUndoSummary"];
            if (summarypre == "") {
                if (rbmode === "undo")
                    var revertData = {
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
                    var revertData = {
                        rbmode: rbmode,
                        basetimestamp: timestamp,
                        page: title,
                        id: dnew,
                        user: user,
                        wiki: wiki,
                        project: server_url + script_path + "/api.php"
                    };
                var rawV = {user: user, project: server_url + script_path, wiki: wiki, oldid: old, newid: dnew};
                vandalsReport.push( rawV );
                vandals.push(user);
                var rawSend = {"type": "synch", "wiki": wiki, "nickname": user, "vandal": "1", "page": title};
                connectTalk.talkSendInside(rawSend);
            }
            else {
                var undoPrefix = 'Undid edits by [[Special:Contribs/$2|$2]] ([[User talk:$2|talk]]) to last version by $1: '.replace(/\$2/gi, user).replace(/\$3/gi, title);
                if (config["wikis"][0].hasOwnProperty(wiki))
                    if (config["wikis"][0][wiki][0].hasOwnProperty("defaultUndoPrefix"))
                        if (config["wikis"][0][wiki][0]["defaultUndoPrefix"] !== null && config["wikis"][0][wiki][0]["defaultUndoPrefix"] !== "")
                            undoPrefix = config["wikis"][0][wiki][0]["defaultUndoPrefix"];

                var rollbackPrefix = 'Reverted edits by [[Special:Contribs/$2|$2]] ([[User talk:$2|talk]]) to last version by $1: ';
                if (config["wikis"][0].hasOwnProperty(wiki))
                    if (config["wikis"][0][wiki][0].hasOwnProperty("defaultRollbackPrefix"))
                        if (config["wikis"][0][wiki][0]["defaultRollbackPrefix"] !== null && config["wikis"][0][wiki][0]["defaultRollbackPrefix"] !== "")
                            rollbackPrefix = config["wikis"][0][wiki][0]["defaultRollbackPrefix"].replace(/\$7/gi, title);

                if (rbmode === "undo")
                    rollbackPrefix = undoPrefix;
                var revertData = {
                    rbmode: rbmode,
                    basetimestamp: timestamp,
                    page: title,
                    id: dnew,
                    user: user,
                    wiki: wiki,
                    summary: rollbackPrefix + summarypre,
                    project: server_url + script_path + "/api.php"
                };
                var rawV = {user: user, project: server_url + script_path, wiki: wiki, oldid: old, newid: dnew};
                vandalsReport.push( rawV );
                suspects.push(user);
                var rawSend = {"type": "synch", "wiki": wiki, "nickname": user, "vandal": "2", "page": title};
                connectTalk.talkSendInside(rawSend);
            }
            $.ajax({
                url: 'php/rollback.php',
                type: 'POST',
                crossDomain: true,
                data: revertData,
                dataType: 'json',
                success: function(datarollback) {
                    if ( datarollback["result"] == "Success" ) {
                        $scope.$apply(function() {
                            $scope.edits.map(function (e, index) {
                                if (e.wiki == wiki && e.title == title) {
                                    $scope.edits.splice($scope.edits.indexOf($scope.edits[index]), 1);
                                }
                            });
                        });

                        if (warnPreset !== null && typeof warnPreset !== "undefined") {
                            if (warn !== null)
                                if (typeof warn["summaryWarn"] !== "undefined")
                                    if (warn["summaryWarn"] !== null && warn["summaryWarn"] !== "")
                                        if (typeof warn["sectionWarn"] !== "undefined")
                                            if (warn["sectionWarn"] !== "null" && warn["sectionWarn"] !== "")
                                                if (typeof warn["countWarn"] !== "undefined")
                                                    if (warn["countWarn"] !== null && warn["countWarn"] !== "")
                                                        if (typeof warn[warnPreset] !== "undefined")
                                                            if (warn[warnPreset] !== null && warn[warnPreset] !== "")
                                                                if (typeof warn[warnPreset][0]["tags"] !== "undefined")
                                                                    if (warn[warnPreset][0]["tags"] !== null && warn[warnPreset][0]["tags"] !== "")
                                                                        if (typeof warn[warnPreset][0]["templates"] !== "undefined")
                                                                            if (warn[warnPreset][0]["templates"] !== null && warn[warnPreset][0]["templates"] !== "") {
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

                                                                                for (keyTags in warn[warnPreset][0]["tags"][0]) {
                                                                                    tags.push(warn[warnPreset][0]["tags"][0][keyTags]);
                                                                                }
                                                                                for (keyTemplates in warn[warnPreset][0]["templates"][0]) {
                                                                                    templates.push(warn[warnPreset][0]["templates"][0][keyTemplates]);
                                                                                }

                                                                                url = server_url + script_path + "/api.php?action=query&prop=revisions&titles=User_talk:" + encodeURIComponent(user) + "&rvslots=main&rvprop=timestamp|content&rvdir=newer&rvstart=" + timeWarn + "&rvlimit=500&format=json&utf8=1";
                                                                                $.ajax({url: url, type: 'GET',
                                                                                    beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Warns');},
                                                                                    crossDomain: true, dataType: 'jsonp', success: function(oldWarns) {
                                                                                        var level = -1;  var pageWarn; var levelSummary;
                                                                                        if (typeof oldWarns["query"] !== "undefined" && typeof oldWarns["query"]["pages"] !== "undefined") {
                                                                                            for(k in oldWarns["query"]["pages"]) {
                                                                                                pageWarn = oldWarns["query"]["pages"][k];
                                                                                            }
                                                                                            for(revisionWarn in pageWarn["revisions"]) {
                                                                                                for(tagWarn in tags) {
                                                                                                    if (pageWarn["revisions"][revisionWarn]["slots"]["main"]["*"].indexOf(tags[tagWarn]) !== -1)
                                                                                                        if (level < tagWarn)
                                                                                                            level = Number(tagWarn);
                                                                                                }
                                                                                            }

                                                                                        }

                                                                                        if (level === -1 || (level !== -1 && level < warnCount) ) {
                                                                                            level = level + 1;
                                                                                            if (typeof templates[level] !== "undefined") {
                                                                                                $.ajax({ url: 'php/doEdit.php', type: 'POST',
                                                                                                    beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Warns');},
                                                                                                    crossDomain: true, dataType: 'json',
                                                                                                    data: {
                                                                                                        warn: "rollback",
                                                                                                        withoutsection: withoutSection,
                                                                                                        project: server_url + script_path + "/api.php",
                                                                                                        wiki: wiki,
                                                                                                        page: 'User_talk:' + user,
                                                                                                        text: templates[level].replace(/\$1/gi, title).replace(/\$2/gi, old).replace(/\$3/gi, dnew).replace(/\$4/gi, user).replace(/\$5/gi, "Special:Diff/" + old + "/" + dnew).replace(/\$6/gi, server_url + script_path + "/index.php?oldid=" + old + "&diff=" + dnew),
                                                                                                        sectiontitle: warnSection.replace(/\$DD/gi, moment.utc().format('DD')).replace(/\$MMMM/gi, warnMonth).replace(/\$MM/gi, moment.utc().format('MM')).replace(/\$YYYY/gi, moment.utc().format('YYYY')),
                                                                                                        summary: warnSummary.replace(/\$1/gi, level+1)
                                                                                                    },
                                                                                                    success: function(s) {
                                                                                                        $scope.reqEnd(datarollback);
                                                                                                    },
                                                                                                    error: function(error) {
                                                                                                        $scope.reqEnd(datarollback);
                                                                                                    }
                                                                                                });
                                                                                            }
                                                                                            else
                                                                                                $scope.reqEnd(datarollback);
                                                                                        }
                                                                                        else {
                                                                                            if (level === warnCount) {
                                                                                                var checkReport = false;
                                                                                                if (reportArray !== null)
                                                                                                    if (checkKey("autoReport", wiki, reportArray))
                                                                                                        if (reportArray["autoReport"] === true)
                                                                                                            if (checkKey("pageReport", wiki, reportArray))
                                                                                                                if (checkKey("regexReport", wiki, reportArray))
                                                                                                                    if (checkKey("sectionReport", wiki, reportArray) || ( checkKey("withoutSectionReport", wiki, reportArray) && reportArray["withoutSectionReport"] === true ) )
                                                                                                                        if (checkKey("textReport", wiki, reportArray)) {
                                                                                                                            checkReport = true;
                                                                                                                            var pageReport = reportArray["pageReport"];
                                                                                                                            var regexReport = reportArray["regexReport"].replace(/\$1/gi, user);
                                                                                                                            
                                                                                                                            var textReport = reportArray["textReport"].replace(/\$1/gi, user);
                                                                                                                            var summaryReport = "[[Special:Contribs/" + user + "|" + user + "]]";
                                                                                                                            if (checkKey("summaryReport", wiki, reportArray))
                                                                                                                                var summaryReport = reportArray["summaryReport"].replace(/\$1/gi, user);
                                                                                                                            var withoutSectionReport = false;
                                                                                                                            if (checkKey("withoutSectionReport", wiki, reportArray))
                                                                                                                                if (reportArray["withoutSectionReport"] === true)
                                                                                                                                    withoutSectionReport = true;
                                                                                                                            var sectionReport = null;
                                                                                                                            if (withoutSectionReport === false)
                                                                                                                                sectionReport = reportArray["sectionReport"].replace(/\$1/gi, user);


                                                                                                                            $.ajax({ url: 'php/doEdit.php', type: 'POST',
                                                                                                                                beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Report');},
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
                                                                                                                                success: function(s) {
                                                                                                                                    if (s["result"] === false) {
                                                                                                                                        if (confirm("Reached maximum warning level. Do you wand to send report about user?"))
                                                                                                                                            $.ajax({ url: 'php/doEdit.php', type: 'POST',
                                                                                                                                                beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / Report');},
                                                                                                                                                crossDomain: true, dataType: 'json',
                                                                                                                                                data: {
                                                                                                                                                    warn: "report",
                                                                                                                                                    withoutsection: withoutSectionReport,
                                                                                                                                                    project: server_url + script_path + "/api.php",
                                                                                                                                                    wiki: wiki,
                                                                                                                                                    page: pageReport,
                                                                                                                                                    text: textReport,
                                                                                                                                                    user: user,
                                                                                                                                                    regexreport: regexReport,
                                                                                                                                                    sectiontitle: sectionReport,
                                                                                                                                                    summary: summaryReport
                                                                                                                                                },
                                                                                                                                                success: function(s) {
                                                                                                                                                    $scope.reqEnd(datarollback);
                                                                                                                                                },
                                                                                                                                                error: function(error) {
                                                                                                                                                    $scope.reqEnd(datarollback);
                                                                                                                                                }
                                                                                                                                            });
                                                                                                                                        else
                                                                                                                                            $scope.reqEnd(datarollback);
                                                                                                                                    }
                                                                                                                                    else
                                                                                                                                        $scope.reqEnd(datarollback);
                                                                                                                                },
                                                                                                                                error: function(error) {
                                                                                                                                    $scope.reqEnd(datarollback);
                                                                                                                                }
                                                                                                                            });
                                                                                                                        }
                                                                                            }
                                                                                            if (checkReport === false)
                                                                                                $scope.reqEnd(datarollback);
                                                                                        }
                                                                                    }});
                                                                            }
                                                                            else
                                                                                $scope.reqEnd(datarollback);
                        }
                        else
                            $scope.reqEnd(datarollback);
                    } else {
                        if (datarollback['code'] == "undofailure" || datarollback['code'] == "editconflict" || datarollback['code'] == "alreadyrolled") {
                            $scope.isCURRENT(server_url, script_path, title, dnew, old, function(cb2) {
                                if (cb2 == null) {
                                    uiEnable();
                                    return;
                                }
                                if (cb2 == false) {
                                    uiEnable();
                                    return;
                                }
                                document.getElementById('page').srcdoc = starterror + "Rollback error: " + datarollback['result'] + enderror;
                                uiEnable();
                            });
                        }
                        else {
                            document.getElementById('page').srcdoc = starterror + "Rollback error: " + datarollback['result'] + enderror;
                            uiEnable();
                        }
                    }
                }, error: function(error) {
                    document.getElementById('page').srcdoc = starterror + "Rollback error. Please open page in the new tab.<br><small>Error code: " + error.status + enderror;
                    uiEnable();
                }
            });
        });
    };

    function connectTalk() {
        var sc = new WebSocket("wss://tools.wmflabs.org/iluvatarbot/:9030?name=" + userSelf + "&token=" + talktoken);

        sc.onclose = function(e){
            setTimeout(function() {
                connectTalk();
            }, 1000);
        };
        sc.onerror = function(err){
            $scope.$apply(function() {
                $scope.users = [];
            });
            if (countConnectAttemp == 0) {
                var newDiv = document.createElement('div');
                newDiv.className = 'phrase-talk';
                newDiv.textContent = "SYSTEM: connection lost";
                document.getElementById('form-talk').appendChild(newDiv);
                scrollToBottom("form-talk");
            }
            countConnectAttemp++;
            document.getElementById('badge-talk').style.background = "rgb(251, 47, 47)";
            sc.close();
        };

        document.getElementById('btn-send-talk').onclick = function() {
            var phraseTalk = document.getElementById('phrase-send-talk').value;
            if (typeof phraseTalk !== "undefined" && phraseTalk !== null && phraseTalk !== "" && sc.readyState === 1) {
                document.getElementById('phrase-send-talk').value = '';
                sc.send(JSON.stringify({"type": "message", "text": phraseTalk}));
            }
        };

        sc.onmessage = function (event) {
            var msg = JSON.parse(event.data);
            if (msg.type === 'hello') {
                if (countConnectAttemp >= 1) {
                    $scope.user = [];
                    downloadHistoryTalk();
                    var newDiv = document.createElement('div');
                    newDiv.className = 'phrase-talk';
                    newDiv.textContent = "SYSTEM: connection restored";
                    document.getElementById('form-talk').appendChild(newDiv);
                    scrollToBottom("form-talk");
                    document.getElementById('badge-talk').style.background = "none"
                }
                countConnectAttemp = 0;
                $scope.$apply(function() { $scope.users = msg.clients.split(','); });
                var indextmp = 0;
                while(indextmp <= $scope.users.length-1) {
                    if ($scope.offlineUsers.indexOf($scope.users[indextmp]) !== -1)
                        $scope.$apply(function() { $scope.offlineUsers.splice($scope.offlineUsers.indexOf($scope.users[indextmp]), 1); });
                    indextmp++;
                }

            }

            if (msg.type === 'connected') {
                $scope.$apply(function() { $scope.users.push(msg.nickname); });
                var indextmp = 0;
                if ($scope.offlineUsers.indexOf(msg.nickname) !== -1)
                    $scope.$apply(function() { $scope.offlineUsers.splice($scope.offlineUsers.indexOf(msg.nickname), 1); });
            }
            if (msg.type === 'disconnected') {
                $scope.$apply(function() {
                    $scope.users = msg.clients.split(',');
                });
                if ($scope.offlineUsers.indexOf(msg.client) == -1 && $scope.users.indexOf(msg.client) == -1)
                    $scope.$apply(function() { $scope.offlineUsers.push(msg.client); });
            }
            if (msg.type === 'message') {
                if (daysAgoToday == false && historyCount !== 0) {
                    addToTalkSection("Today", false);
                    daysAgoToday = true;
                }
                addToTalk(null, msg.nickname, msg.text);
                if (document.getElementById('talkForm').style.display == 'none')
                    document.getElementById('badge-talk').style.background = "rgb(36, 164, 100)";
            }

            if (msg.type === "synch") {
                $scope.edits.map(function (e, index) {
                    if (e.wiki == msg.wiki && e.title == msg.page)
                        $scope.$apply(function() { $scope.edits.splice($scope.edits.indexOf($scope.edits[index]), 1); });
                });
                if (msg.vandal === "1")
                    $scope.$apply(function() { vandals.push(msg.nickname); });
                if (msg.vandal === "2")
                    $scope.$apply(function() { suspects.push(msg.nickname); });
            }
        };

        function talkSendInside(messageInside) {
            if (sc.readyState === 1)
                sc.send(JSON.stringify(messageInside));
        }
        connectTalk.talkSendInside = talkSendInside;

    }
    connectTalk();

    $scope.selectTalkUsers = function(selectedUser) {
        document.getElementById("phrase-send-talk").value = "@" + selectedUser + ", " + document.getElementById("phrase-send-talk").value;
        document.getElementById("phrase-send-talk").focus();
    };

    $scope.edits = [];
    if (typeof(EventSource) == "undefined") {
        alert("Sorry, your browser does not support server-sent events.");
        return;
    }

    var source = new EventSource("https://stream.wikimedia.org/v2/stream/recentchange");
    source.onmessage = function(event) {
        var stuff = JSON.parse(event.data);
        var namespacetemp = "";
        var swmt = false;
        var setusers = false;
        var n = "none";
        if (isGlobal == 'true' || isGlobalModeAccess == 'true') {
            if (document.getElementById('small-wikis-btn').style.paddingLeft == '22.5px') swmt = true;
            if (document.getElementById('lt-300-btn').style.paddingLeft == '22.5px') setusers = true;
        }
        else {
            swmt = false; setusers = false;
        }
        if (document.getElementById('new-pages-btn').style.paddingLeft == '22.5px') n = "new";
        if (stuff.namespace >= 0 && stuff.namespace <= 15) namespacetemp = ns[stuff.namespace];
        else namespacetemp = "<font color='brown'>Non-canon (" + stuff.namespace + ")</font>";

        // ((stuff.namespace !== 2 && stuff.type == n) || stuff.type == "edit") &&
        if (stuff.user !== userSelf && (stuff.type == n || stuff.type == "edit") && stuff.bot == false && (nsList2.indexOf(stuff.namespace.toString()) >= 0 || nsList2.length == 0 ) && stuff.patrolled != true && ((customlist.indexOf(stuff.wiki) >= 0) || (local_wikis.indexOf(stuff.wiki) >= 0 && isGlobal == 'false') || (wikis.indexOf(stuff.wiki) >= 0 && swmt == true && (isGlobal == 'true' || isGlobalModeAccess == 'true')) || (active_users.indexOf(stuff.wiki) >= 0 && setusers == true && (isGlobal == 'true' || isGlobalModeAccess == 'true')))) {
            if (typeof sandboxlist[stuff.wiki] !== "undefined")
                if (sandboxlist[stuff.wiki] == stuff.title)
                    return;
            if (global.indexOf(stuff.user) !== -1 || wlistu.indexOf(stuff.user) !== -1 || wlistp.indexOf(stuff.wiki) !== -1)
                return;

            // IP user
            if (/^\d*?\.\d*?\.\d*?\.\d*?$/.test(stuff.user) || stuff.user.indexOf(":") !== -1) {
                $scope.$apply(function() {
                    if (countqueue !== 0 && $scope.edits.length >= countqueue)
                        $scope.edits.pop();
                    var new_el = {"server_url": stuff.server_url, "server_name": stuff.server_name, "script_path": stuff.server_script_path, "server_uri": stuff.meta.uri, "wiki": stuff.wiki, "namespace": namespacetemp, "user": stuff.user, "title": stuff.title, "comment": stuff.comment, "old": stuff.revision.old, "new": stuff['revision']['new'], "isIp": "ip"};
                    $scope.edits.unshift(new_el);
                });
                return
            }
            if (document.getElementById('registered-btn').style.paddingLeft !== '22.5px')
                return

            // Registered user
            url = stuff.server_url + stuff.server_script_path + "/api.php?action=query&list=users&ususers=" + encodeURIComponent(stuff.user).replace(/'/g, '%27') + "&usprop=groups|registration|editcount&utf8&format=json";
            $.ajax({url: url, type: 'GET',
                beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / SSE parsing');},
                crossDomain: true, dataType: 'jsonp', success: function(datainfo) {
                    var groups = datainfo["query"]["users"][0]["groups"];
                    if (groups.includes("sysop") == false && groups.includes("editor") == false && groups.includes("autoreviewer") == false && groups.includes("confirmed") == false && groups.includes("extendedconfirmed") == false && groups.includes("filemover") == false && groups.includes("patroller") == false && groups.includes("templateeditor") == false && groups.includes("autopatrolled") == false && groups.includes("autoeditor") == false && groups.includes("closer") == false && groups.includes("rollbacker") == false && groups.includes("translator") == false && groups.includes("translationadmin") == false && groups.includes("engineer") == false && groups.includes("global-renamer") == false && groups.includes("oversight") == false && groups.includes("reviewer") == false && groups.includes("bureaucrat") == false) {
                        var d = new Date();
                        if (datainfo["query"]["users"][0]["registration"] == null)
                            return; // WMF have lost registration date of some very old accounts
                        var dateDiff = (Date.UTC(d.getUTCFullYear(), d.getUTCMonth(), d.getUTCDate(), d.getUTCHours(), d.getUTCMinutes(), d.getUTCSeconds(), d.getUTCMilliseconds()) - Date.parse(datainfo["query"]["users"][0]["registration"])) / 1000 / 60 / 60 / 24;
                        if (parseInt(datainfo["query"]["users"][0]["editcount"]) >= countedits && dateDiff >= regdays)
                            return;
                        var url = "https://cvn.wmflabs.org/api.php?users=" + encodeURIComponent(stuff.user).replace(/'/g, '%27');
                        $.ajax({url: url, type: 'GET',
                            beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / SSE parsing');},
                            crossDomain: true, dataType: 'jsonp', success: function(data) {
                                if (typeof data["users"][stuff.user] !== "undefined")
                                    if (data["users"][stuff.user]["type"] == "whitelist")
                                        return;
                                $scope.$apply(function() {
                                    if (countqueue !== 0 && $scope.edits.length >= countqueue)
                                        $scope.edits.pop();
                                    var new_el = {"server_url": stuff.server_url, "server_name": stuff.server_name, "script_path": stuff.server_script_path, "server_uri": stuff.meta.uri, "wiki": stuff.wiki, "namespace": namespacetemp, "user": stuff.user, "title": stuff.title, "comment": stuff.comment, "old": stuff.revision.old, "new": stuff['revision']['new'], "isIp": "registered"};
                                    $scope.edits.unshift(new_el);
                                });
                            }});
                    }
                }});
        }
    };

    $scope.removeLast = function() {
        $scope.$apply(function () {
            if ($scope.edits.length >= countqueue)
                while ($scope.edits.length > countqueue)
                    $scope.edits.pop();
        });
    };

    $scope.isCURRENT = function(tSERVER_URL, tSCRIPT_PATH, tTITLE, tDNEW, tOLD, CALLBACK) {
        url = tSERVER_URL + tSCRIPT_PATH + "/api.php?action=query&prop=revisions&titles=" + encodeURIComponent(tTITLE).replace(/'/g, '%27') + "&rvprop=ids|timestamp|user|comment&rvlimit=1&format=json&utf8=1";
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / isCurrent');},
            crossDomain: true,
            dataType: 'jsonp',
            success: function(data) {
                if (typeof data.error !== "undefined") {
                    document.getElementById('page').srcdoc = starterror + "Opening error; server error info: " + data.error.info + enderror;
                    CALLBACK(null);
                    return;
                }

                if (typeof data["query"]["pages"] !== "undefined") {
                    var pageId = "";
                    for (var k in data["query"]["pages"]) {
                        pageId = k;
                    }
                }

                if (typeof data["query"]["pages"][pageId]["revisions"][0]["revid"] == "undefined" || typeof data["query"]["pages"] == "undefined" || data["query"]["pages"] == "-1") {
                    if (typeof data.error !== "undefined")
                        document.getElementById('page').srcdoc = starterror + "Opening error. Maybe page was deleted.<br><small>Server error info: " + data.error.info + "</small>" + enderror;
                    else
                        document.getElementById('page').srcdoc = starterror + "Opening error. Maybe page was deleted.";
                    CALLBACK(null);
                    return;
                }

                if (tDNEW == data["query"]["pages"][pageId]["revisions"][0]["revid"]) {
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
                    beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / isCurrent');},
                    crossDomain: true,
                    dataType: 'jsonp',
                    success: function(data) {
                        if (typeof data.error !== "undefined") {
                            document.getElementById('page').srcdoc = starterror + "Opening error; Server error info: " + data.error.info + enderror;
                            CALLBACK(null);
                            return;
                        }
                        if (typeof data.compare['*'] == "undefined") {
                            document.getElementById('page').srcdoc = starterror + "Opening error. Maybe page has been deleted.<br><small>Server error info: " + data.error.info + "</small>" + enderror;
                            CALLBACK(null);
                        }
                        else {
                            url = tSERVER_URL + tSCRIPT_PATH + "/api.php?action=query&list=users&ususers=" + encodeURIComponent(user).replace(/'/g, '%27') + "&usprop=editcount&utf8&format=json";
                            $.ajax({
                                url: url,
                                type: 'GET',
                                beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / isCurrent');},
                                crossDomain: true,
                                dataType: 'jsonp',
                                success: function(data4) {
                                    if (typeof data4.error !== "undefined") {
                                        document.getElementById('page').srcdoc = starterror + "Opening error; server error info: " + data.error.info + enderror;
                                        CALLBACK(null);
                                        return;
                                    }

                                    if (typeof data4["query"]["users"][0]["editcount"] == "undefined") {
                                        userip = "ip";
                                        document.getElementById("userLinkSpec").style.color = "green";
                                        document.getElementById("userLinkSpec").textContent = user;
                                        if (i > 0)
                                            edits_history[i - 1]["userip"] = "ip";
                                    }
                                    else {
                                        userip = "registered";
                                        document.getElementById("userLinkSpec").style.color = "#3366BB";
                                        document.getElementById("userLinkSpec").textContent = user;
                                        if (i > 0)
                                            edits_history[i - 1]["userip"] = "registered";
                                    }
                                    document.getElementById('com').textContent = "Comment: " + summary;
                                    if (data.compare['*'] == "")
                                        document.getElementById('page').srcdoc = starterror + "This edit has been reverted already." + enderror;
                                    else
                                        document.getElementById('page').srcdoc = diffstart + data.compare['*'].replace(/\<a class\="mw\-diff\-movedpara\-left".*?\<\/a\>/g, '-').replace(/\<a class\="mw\-diff\-movedpara\-right".*?\<\/a\>/g, '+') + diffend;
                                    document.getElementById('page').scrollTop = 0;
                                    $scope.$apply(function() {
                                        $scope.edits.map(function (e, index) {
                                            if (e.wiki == wiki && e.title == title) {
                                                $scope.edits.splice($scope.edits.indexOf($scope.edits[index]), 1);
                                            }
                                        });
                                    });
                                    if (data.compare['*'] !== "") {
                                        document.getElementById('editForm').style.display = 'none';
                                        alert("Can't perform this action, this is not latest revision. Loaded new revision.");
                                    }
                                    CALLBACK(false);
                                }, error: function(error) { alert('Failed... dev code: 001; error code: ' + error.status + '.'); }
                            });
                        }
                    }, error: function(error) { alert('Failed... dev code: 002; error code: ' + error.status + '.'); }
                });
            }, error: function(error) { alert('Failed... dev code: 003; error code: ' + error.status + '.'); }
        });
    };

    $scope.reqEnd = function (tDATA) {
        user = tDATA["user"];
        old = tDATA["oldrevid"];
        dnew = tDATA["newrevid"];
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
            beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / reqEnd');},
            crossDomain: true,
            dataType: 'jsonp',
            success: function(data) {
                if (typeof data.error !== "undefined") {
                    document.getElementById('page').srcdoc = starterror + "Opening error; server error info: " + data.error.info + enderror;
                    uiEnable();
                    return;
                }
                document.getElementById("userLinkSpec").style.color = "#3366BB";
                document.getElementById("userLinkSpec").textContent = user;
                document.getElementById('com').textContent = "Comment: " + summary;
                document.getElementById('page').srcdoc = diffstart + data.compare['*'].replace(/\<a class\="mw\-diff\-movedpara\-left".*?\<\/a\>/g, '-').replace(/\<a class\="mw\-diff\-movedpara\-right".*?\<\/a\>/g, '+') + diffend;
                document.getElementById('page').scrollTop = 0;
                uiEnable();
            }, error: function(error) { alert('Failed... dev code: 009; error code: ' + error.status + '.'); uiEnable(); }
        });
    };

    $scope.nextDiff = function() {
        if ($scope.edits.length > 0) {
            $scope.select($scope.edits[0]);
        } else {
            document.getElementById('next-diff').classList.add('no-diff');
            setTimeout(function(){ document.getElementById('next-diff').classList.remove('no-diff'); }, 1000);
        }
    };

    $scope.openLink = function (tTYPE) {
        var urldiff = server_url + "/wiki/Special:Contributions/" + encodeURIComponent(user).replace(/'/g, '%27');
        if (tTYPE == "diff") {
            var diffWindow = window.open(urldiff, "_blank");
            diffWindow.location;
            diffWindow.focus();
        }
    };


    changeRollbacksDescription = function(wiki) {
        $scope.descriptions = config["wikis"][0]["others"][0]["rollback"]
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

    };

    document.getElementById("warn-box").onclick = function() {
        if (checkWarn == false && confirm("Turning this on will left a warning on the user talk page after clicking common summaries (green only).")) {
            checkWarn = true;
            $("#warn-box").removeClass( "warn-off").addClass("warn-on");
            $("#check-label").empty().append("Warn ON");
        }
        else {
            checkWarn = false;
            $("#warn-box").removeClass( "warn-on").addClass("warn-off");
            $("#check-label").empty().append("Warn OFF");
        }
        $scope.$apply(function(){$scope.descriptions;});
    };

    document.getElementById("warn-box-delete").onclick = function() {
        if (checkWarnDelete == false && confirm("Turning this on will left a notification on the user talk page after clicking templates (green only).")) {
            checkWarnDelete = true;
            $("#warn-box-delete").removeClass( "warn-off").addClass("warn-on");
            $("#check-label-delete").empty().append("Warn ON");
        }
        else {
            checkWarnDelete = false;
            $("#warn-box-delete").removeClass( "warn-on").addClass("warn-off");
            $("#check-label-delete").empty().append("Warn OFF");
        }
        $scope.$apply(function(){$scope.speedy;});
    };

    document.onkeydown = function(e) {
        if (!e)
            e = window.event;
        var keyCode = e.which || e.keyCode || e.key;
        if (keyCode == "13") {
            if (document.getElementById('customRevert').style.display == "block")
                document.getElementById('btn-cr-u-apply').click();
            if (document.getElementById('talkForm').style.display == "block")
                document.getElementById('btn-send-talk').click();
            if (document.getElementById('logs').style.display == "block")
                document.getElementById('btn-searchLogs').click();
        }
        if (keyCode == 82)
            if (isNotModal()) {
                document.getElementById('revert').click();
                return false;
            }
        if (keyCode == 89)
            if (isNotModal()) {
                document.getElementById('customRevertBtn').click();
                return false;
            }
        if (keyCode == 219)
            if (isNotModal()) {
                document.getElementById('back').click();
                return false;
            }
        if (keyCode == 69)
            if (isNotModal()) {
                document.getElementById('editBtn').click();
                return false;
            }
        if (keyCode == 79)
            if (isNotModal()) {
                document.getElementById('browser').click();
                return false;
            }
        if (keyCode == 84)
            if (isNotModal()) {
                document.getElementById('btn-talk').click();
                return false;
            }
        if (keyCode == 83)
            if (isNotModal()) {
                document.getElementById('btn-settings').click();
                return false;
            }
        if (keyCode == 85)
            if (isNotModal()) {
                document.getElementById('btn-unlogin').click();
                return false;
            }
        if (keyCode == 191)
            if (isNotModal()) {
                document.getElementById('luxo').click();
                return false;
            }
        if (keyCode == 65)
            if (isNotModal()) {
                if (document.getElementById('userLinkSpec').style.display !== "none") {
                    document.getElementById('userLinkSpec').click();
                    return false;
                }
            }
        if (keyCode == 32) {
            if (isNotModal()) {
                if ($scope.edits.length > 0) {
                    $scope.select($scope.edits[0]);
                    $scope.$digest();
                    return false;
                }
            }
        }
        if (keyCode == 27)
            if (document.getElementById('settings').style.display == "block" || document.getElementById('talkForm').style.display == "block" || document.getElementById('editForm').style.display == "block" || document.getElementById('logs').style.display == "block") {
                queueClick();
                return false;
            }
    };

    uiEnable = function() {
        document.getElementById("queue").classList.remove("disabled");
        document.getElementById("control").classList.remove("disabled");
        document.getElementById('next-diff').classList.remove('disabled');
       // if (isGlobal == 'false' && (isGlobalModeAccess == 'true' && local_wikis.indexOf(wiki) !== -1)) {
       //     document.getElementById("revert").classList.remove("disabled");
       //     document.getElementById("customRevertBtn").classList.remove("disabled");
      //  }
      //  if (isGlobal == 'false' && (isGlobalModeAccess == 'true' && local_wikis.indexOf(wiki) === -1)) {
       //     document.getElementById("revert").classList.add("disabled");
       //     document.getElementById("customRevertBtn").classList.add("disabled");
       // }
    };

});

function SHOW_DIFF(tSERVER_URL, tSERVER_NAME, tSCRIPT_PATH, tSERVER_URI, tWIKI, tNAMESPACE, tUSER, tOLD, tDNEW, tTITLE, tUSERIP, tSUMMARY) {
    uiDisableList();
    document.getElementById("us").style.display = "inline-block";
    if (tUSERIP == "registered")
        document.getElementById("userLinkSpec").style.color = "#3366BB";
    else
        document.getElementById("userLinkSpec").style.color = "green";
    document.getElementById("userLinkSpec").textContent = tUSER;
    document.getElementById('com').textContent = "Comment: " + tSUMMARY;
    document.getElementById('tit').textContent = "Title: " + tTITLE;
    if ((tWIKI == "testwiki") || (tWIKI == "test2wiki") || (tWIKI == "testwikidata") || (tWIKI == "testwikidatawiki"))
        document.getElementById('wiki').innerHTML = "Wiki: <font color='tomato'>" + tWIKI + "</font>";
    else
        document.getElementById('wiki').innerHTML = "Wiki: " + tWIKI;
    document.getElementById('ns').innerHTML = "Namespace: " + tNAMESPACE;
    if (tOLD == null) {
        uiDisableNew();
        var urlr = tSERVER_URL + tSCRIPT_PATH + "/api.php?action=query&prop=revisions&revids=" + tDNEW + "&rvprop=content&rvslots=main&format=json&utf8=1";
        $.ajax({
            url: urlr,
            type: 'GET',
            beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / diff');},
            crossDomain: true,
            dataType: 'jsonp',
            success: function(datar) {
                if (typeof datar.error !== "undefined") {
                    document.getElementById('page').srcdoc = starterror + "Opening error; server error info: " + data.error.info + enderror;
                    uiEnable();
                    return;
                }

                if (typeof datar["query"]["pages"] !== "undefined") {
                    var pageIdr = "";
                    for (var k in datar["query"]["pages"]) {
                        pageIdr = k;
                    }
                    var newPageDiff = "";
                    var newPage = datar["query"]["pages"][pageIdr]["revisions"][0]["slots"]["main"]["*"].replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').split("\n");
                    for(var j = 0; j<newPage.length; j++) {
                        newPageDiff += startstring + newPage[j] + endstring;
                    }
                    document.getElementById('page').srcdoc = newstart + newPageDiff + newend;
                } else
                    document.getElementById('page').srcdoc = starterror + "Opening error. Maybe page was deleted." + enderror;
                uiEnable();
            }
        });

    }
    else {
        var url = tSERVER_URL + tSCRIPT_PATH + "/api.php?action=compare&format=json&uselang=en&fromrev=" + tOLD + "&torev=" + tDNEW + "&utf8=1&prop=diff";
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function(xhr){xhr.setRequestHeader('Api-User-Agent', 'SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) Ajax / diff');},
            crossDomain: true,
            dataType: 'jsonp',
            success: function(data) {
                if (typeof data.error !== "undefined") {
                    if (data.error.code == "nosuchrevid")
                        document.getElementById('page').srcdoc = starterror + "Opening error. This page has been deleted." + enderror;
                    else
                        document.getElementById('page').srcdoc = starterror + "Opening error. Maybe page has been deleted.<br><small>Server error info: " + data.error.info + "</small>" + enderror;
                    uiEnable();
                    return;
                }

                if (data.compare['*'] == "")
                    document.getElementById('page').srcdoc = starterror + "The edit has already was reverted." + enderror;
                else
                    document.getElementById('page').srcdoc = diffstart + data.compare['*'].replace(/\<a class\="mw\-diff\-movedpara\-left".*?\<\/a\>/g, '-').replace(/\<a class\="mw\-diff\-movedpara\-right".*?\<\/a\>/g, '+') + diffend;
                uiEnable();
            },
            error: function(error) {
                alert('Failed... dev code: 010; error code: ' + error.status + '.'); uiEnable();
            }
        });
    }
    document.getElementById('page').scrollTop = 0;
    frameLoaded();
}
nextDiffStyle = function() {
    document.getElementById("page-welcome").style.display = "none";
    document.getElementById("description-container").style.display = "block";
    document.getElementById("page").style.display = "block";
    document.getElementById("browser").style.background = "";
    document.getElementById("editBtn").style.background = "";
    document.getElementById("customRevertBtn").style.background = "";
    document.getElementById("revert").style.background = "";
    document.getElementById("back").style.background = "";
};

uiDisable = function() {
    document.getElementById("queue").classList.add("disabled");
    document.getElementById("control").classList.add("disabled");
    document.getElementById('next-diff').classList.add('disabled');
};
uiDisableList = function() {
    document.getElementById("control").classList.add("disabled");
};
uiDisableNew = function() {
    document.getElementById("revert").classList.add("disabled");
    document.getElementById("customRevertBtn").classList.add("disabled");
};
uiEnableNew = function() {
    document.getElementById("queue").classList.remove("disabled");
   // if (isGlobalModeAccess !== 'true' || local_wikis.indexOf(wiki)) {
   //     document.getElementById("revert").classList.remove("disabled");
   //     document.getElementById("customRevertBtn").classList.remove("disabled");
   // }
};
isNotModal = function () {
    if (document.getElementById('customRevert').style.display !== "block" &&
        document.getElementById('editForm').style.display !== "block" &&
        document.getElementById('logs').style.display !== "block" &&
        document.getElementById('settings').style.display !== "block" &&
        document.getElementById('talkForm').style.display !== "block")
        return true;
    else
        return false;
};

function checkKey(k, wiki, targetArray) {
    var checkArrayKey = false;
    if (targetArray.hasOwnProperty(k))
        if (targetArray[k] !== null)
            if (targetArray[k] !== "")
                if (typeof targetArray[k] !== "undefined")
                    checkArrayKey = true;
    if (checkArrayKey === true)
        return true;
    else
        return false;
};

var isTouchDevice = 'ontouchstart' in document.documentElement;
function iframeBreakFocus() {
    if (!isTouchDevice) {
        window.focus();
    }
}
setInterval(iframeBreakFocus, 500);