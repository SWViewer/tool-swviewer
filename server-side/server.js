const request = require('request');
const fs = require('fs');
const url = require('url');
const ejs = require('ejs');
const http = require('http');
const WebSocket = require('ws');
const log4js = require("log4js");
const moment = require('moment');
const NodeCache = require('node-cache');
EventSource = require('eventsource');
const ReconnectingEventSource = require('reconnecting-eventsource').default;

// Logging configure
log4js.configure({
  appenders: { everything: { type: 'file', filename: 'server.log' } },
  categories: { default: { appenders: [ 'everything' ], level: 'debug' } }
});
const logger = log4js.getLogger();

process.on('uncaughtException', (err, origin) => {
  logger.error(err);
  logger.error(origin);
});

console.log = function(cm) {
    logger.debug(cm);
};


const exp = require('./service/storage');

var content = fs.readFileSync('public/index.html', 'utf-8');
var compiled = ejs.compile(content);

const swmt = exp.swmt;
const lt300 = exp.lt300;
const groupFilt = exp.groupFilt;
const userAgent = exp.userAgent;
const namespaces = exp.namespaces;
const customSandBoxes = exp.customSandBoxes;

const cacheCVN = new NodeCache({ "stdTTL": 18000 }); // 5 h
const token = fs.readFileSync('service/token.txt', 'utf8');

var source;
var errors = 0;
var users = [];
var globals = [];
var storage = [];
var generalList = [];
var sandboxlist = [];
var ORESList = null;
var eventPerMin = "-";
var eventPerMinPrepare = 0;
var upTimeWS = moment().unix();
var upTimeSSE = moment().unix();
const port = +(process.env.PORT || 9030);

// The Talk / Websocket

const server = http.createServer((req, res) => {
    res.writeHead(200, { 'Content-Type': 'text/html' });
    var streamClients = getClients(); var garbage = getGarbage(); var cached = getCached();
    var memory = Math.round(process.memoryUsage().rss / 1024 / 1024 * 100) / 100;
    var upTimeWSSend = new Date((moment().unix() - upTimeWS) * 1000).toISOString().substr(11, 8);
    var upTimeSSESend = new Date((moment().unix() - upTimeSSE) * 1000).toISOString().substr(11, 8);
    res.end(compiled({clients: streamClients, garbage: garbage, cache: cached, memory: memory, errors: errors,
        upTimeWS: upTimeWSSend, upTimeSSE: upTimeSSESend, eventPerMin: eventPerMin, wikis: generalList.length,
        globals: globals.length}));
});
server.listen(port, () => 'Server up');

function getClients () {
    if (typeof wss !== "undefined")
        if (wss.hasOwnProperty("clients"))
            return wss.clients.size;
    return null;
}

function getCached() {
    return (typeof cacheCVN !== "undefined") ? cacheCVN.getStats().keys : null;
}

function getGarbage () {
    var i = 0;
    if (typeof storage !== "undefined") {
        Object.keys(storage).forEach(function(key) {
            var l = (!storage[key].hasOwnProperty(0)) ? storage[key] : storage[key][0];
            if (l.hasOwnProperty("time"))
                if (l.time <= (Date.now() - 1000 * 60))
                    i++;
        });
        return i;
    } else return null;
}

const wss = new WebSocket.Server({ noServer: true, verifyClient(info, done) {
        upTimeWS = moment().unix();
        const nickName = url.parse(info.req.url, true).query.name;
        const userToken = url.parse(info.req.url, true).query.token;
        try {
            request({
                method: "POST", uri: "https://swviewer.toolforge.org/php/authTalk.php", headers: {"User-Agent": userAgent},
                form: { serverToken: token, userToken: userToken, username: nickName }
            }, function (error, response, body) {
                if (!body || response.statusCode !== 200 || error) return done(false, 403, "Error of auth; bad request");
                var auth = JSON.parse(body);
                return (auth.auth !== "true") ? done(false, 403, "Token is not valid") : done(true);
            });
        }
        catch (e) {
            return done(false, 403, "Error of auth; Maybe no connect");
        }
    }
});

server.on('upgrade', (request, socket, head) => {
    wss.handleUpgrade(request, socket, head, (ws) => {
        wss.emit('connection', ws, request);
    });
});

wss.on('connection', function(ws, req) {
    ws.nickName = url.parse('https://swviewer-service.toolforge.org:9030' + req.url, true).query.name;
    ws.preset = url.parse('https://swviewer-service.toolforge.org:9030' + req.url, true).query.preset;
    ws.isAlive = true;
    ws.pause = false;
    users.push(ws);
    getParams(ws).then(function(res) {
        if (res === false || res === undefined) { ws.terminate(); return; }
        ws.filt = res;
        getGeneralList();
        if (wss.clients.size === 1) SSEStart();
        wss.clients.forEach(function(client) {
            if (client.readyState === WebSocket.OPEN && client !== ws)
                client.send(JSON.stringify({"type": "connected", "nickname": ws.nickName}));
        });
        if (ws.readyState === WebSocket.OPEN)
            ws.send(JSON.stringify({"type": "hello", "clients": usersName(users)}));

        ws.on('pong', function() {
            ws.isAlive = true;
        });

        ws.on('message', function(message) {
            var msg = JSON.parse(message);

            if (msg.type === 'message') {
                wss.clients.forEach(function(client) {
                    if (client.readyState === WebSocket.OPEN)
                        client.send(JSON.stringify({"type": "message", "nickname": ws.nickName, "text": msg.text}));
                });
                request({
                    method: "POST", uri: "https://swviewer.toolforge.org/php/talkHistory.php", headers: {"User-Agent": userAgent},
                    form: { action: "save", serverToken: token, username: ws.nickName, text: msg.text }
                });
            }

            if (msg.type === 'synch') {
                wss.clients.forEach(function(client) {
                    if (client.readyState === WebSocket.OPEN && client !== ws)
                        client.send(JSON.stringify({"type": "synch", "wiki": msg.wiki, "nickname": msg.nickname, "vandal": msg.vandal, "page": msg.page}));
                });
            }

            if (msg.type === 'pause') {
                ws.pause = (!ws.pause);
                getGeneralList();
                ws.send(JSON.stringify({"type": "pause", "state": ws.pause}));
            }
        });

        ws.on('close', function() {
            for (var i in users) {
                if (users[i] === ws && users[i].nickName === ws.nickName)
                    users.splice(i, 1);
            }
            wss.clients.forEach(function(client) {
                if (client.readyState === WebSocket.OPEN)
                    client.send(JSON.stringify({"type": "disconnected", "clients": usersName(users), "client": ws.nickName}));
            });
            if (users.size === 0) source.close();
            else getGeneralList();
        });
    }).catch(function(e) { console.log(e); });
});

setInterval(function ping() {
    wss.clients.forEach(function(ws) {
        if (ws.isAlive === false)
            return ws.terminate();
        ws.isAlive = false;
        ws.ping(function() {});
    });
}, 2500000); // 41.6 min

function usersName(clientsArray) {
    var usersList = [];
    for (var i in clientsArray) {
        if (clientsArray.hasOwnProperty(i))
            usersList.push(clientsArray[i].nickName);
    }
    return usersList.join();
}

/*
SSE proxy
*/

request('https://ores.wikimedia.org/v3/scores', { json: true, headers: { "User-Agent": userAgent } }, (err, res) => {
    if (err) { return false; }
    ORESList = res.body;
});

getSandboxes();
getGlobals();

setInterval(function() {
    try {
        getSandboxes();
        getGlobals();
    } catch(err) {
        logger.debug("Updating lists error: " + err);
    }
}, 86400000); // 24 h

function getSandboxes() {
    sandboxlist = [];
    request('https://www.wikidata.org/w/api.php?action=wbgetentities&ids=Q3938&props=sitelinks/urls&format=json&utf8=1', { json: true, headers: { "User-Agent": userAgent } }, (err, res) => {
        if (err) return;
        var sandbox = res.body;
        for(var sb in sandbox.entities.Q3938.sitelinks) {
            if (sandbox.entities.Q3938.sitelinks.hasOwnProperty(sb))
                sandboxlist[sandbox.entities.Q3938.sitelinks[sb].site] = sandbox.entities.Q3938.sitelinks[sb].title;
        }
        Object.keys(customSandBoxes).forEach(element => {
            customSandBoxes[element].forEach(element2 => {
                sandboxlist[element] = (sandboxlist.hasOwnProperty(element)) ? sandboxlist[element] + "," + element2 : element2;
            });
        });
    });
}

function getGlobals() {
    request('https://swviewer.toolforge.org/php/getGlobals.php?token_proxy=' + token, { json: false, headers: { "User-Agent": userAgent } }, (err, res) => {
        if (err) { return false; }
        globals = res.body.slice(0, -1).split(",");
    });
}

function SSEStart() {
    source = new ReconnectingEventSource('https://stream.wikimedia.org/v2/stream/recentchange,revision-create');
    source.onmessage = function(e) {
        try {
            if (wss.clients.size === 0) { source.close(); return; }
            if (e.type !== "message") return;
            e = JSON.parse(e.data);
            if (!streamFilter(e)) return;
            e.time = Date.now();
            var uniqWiki = (e.hasOwnProperty("wiki")) ? e.wiki : e.database;
            var uniqRev = (e.hasOwnProperty("rev_id")) ? e.rev_id : e.revision.new;
            var uniqID = String(e.meta.request_id) + String(uniqWiki) + String(uniqRev);
            if (!storage[uniqID]) { storage[uniqID] = [e]; return; }
            if (checkStreamExist(storage[uniqID], e.meta.stream)) return;
            storage[uniqID].push(e);
            var result;
            result = mergeList(normalizeArray(storage[uniqID][0]), normalizeArray(storage[uniqID][1]));
            if (!result.performer.hasOwnProperty("user_text")) { errors++; return; }
            checkCVN(result.performer.user_text, result.performer.user_is_anon).then(function(res) {
                if (res === undefined) res = true;
                if (res === false) { delete storage[uniqID]; return false; }
                getWikidataTitle(result).then(function(res) {
                    if (res === undefined) res = null;
                    result.wikidata_title = res;
                    eventPerMinPrepare++;
                    getORES(result.wiki, result.new_id, getModel(ORESList, result.wiki)).then(function(res) {
                        if (res === undefined || res === false) res = null;
                        result.ORES = res;
                        wss.clients.forEach(function (ws) {
                            if (ws.readyState === WebSocket.OPEN && ws.pause !== true)
                                if (customFilter(result, ws.filt, ws.nickName))
                                    ws.send(JSON.stringify({"type": "edit", "data": result}));
                        });
                    });
                    delete storage[uniqID];
                });
            });
        } catch(err) {
            logger.debug("global error: " + err);
        }
    };
    source.onopen = function () {
        upTimeSSE = moment().unix();
    };
}

function getModel(olist, wiki){
    if (olist === null) return false;
    if (Object.keys(olist).length === 0) return false;
    if (Object.keys(olist).find(oresWiki => oresWiki === wiki) === undefined) return false;
    if (olist[wiki].models.damaging !== undefined) return 'damaging';
    else if (olist[wiki].models.reverted !== undefined) return 'reverted';
    else { return false; }
}

async function getORES(wiki, new_id, model) {
    return new Promise(resolve => {
        if (model === false) resolve(false);
        request("https://ores.wikimedia.org/v3/scores/" + String(wiki) + "/" + String(new_id) + "/" + String(model), {
            json: true,
            headers: {"User-Agent": userAgent}
        }, (err, res) => {
            if (err) { resolve(false); return; }
            if (res.body.hasOwnProperty("error")) { resolve(false); return; }
            if (res.body[wiki] === undefined) { resolve(false); return; }
            if (!res.body[wiki].hasOwnProperty("scores")) { resolve(false); return; }
            if (res.body[wiki].scores[new_id] === undefined) { resolve(false); return; }
            if (res.body[wiki].scores[new_id][model] === undefined) { resolve(false); return; }
            if (res.body[wiki].scores[new_id][model].error !== undefined) { resolve(false); return; }
            if (res.body[wiki].scores[new_id][model].score === undefined) { resolve(false); return; }
            const damage = res.body[wiki].scores[new_id][model].score.probability.true;
            const damagePer = parseInt(damage * 100);
            resolve({score: damagePer, color: `hsl(0, ${damagePer}%, 56%)`});
        });
    }).catch(function(err) {
        logger.debug("getORES promise error: " + err);
    });
}

function checkStreamExist(exist, streamName) {
    var check = false;
    exist.forEach(function (k) {
        if (k.meta.stream === streamName) check = true;
    });
    return check;
}

function mergeList(arr1, arr2, arr3 = null) {
    for(var key in arr2) {
        if (arr2.hasOwnProperty(key))
            if ((arr2[key] !== "") && (arr2[key] !== null) && (typeof arr2[key] !== "object" || Object.keys(arr2[key]).length > 0))
                arr1[key] = arr2[key];
    }
    if (arr3 !== null) {
        for(var key2 in arr3) {
            if (arr3.hasOwnProperty(key2))
                if ((arr3[key2] !== "") && (arr3[key2] !== null) && (typeof arr3[key2] !== "object" || Object.keys(arr3[key2]).length > 0))
                    arr1[key2] = arr3[key2];
        }
    }
    return arr1;
}

function streamFilter(e) {
    if ((e.hasOwnProperty("wiki") && !generalList.includes(e.wiki)) || (e.hasOwnProperty("database") && !generalList.includes(e.database))) return false; // general wiki list
    if (e.meta.stream === "mediawiki.revision-create" && (e.page_namespace === 6 && !e.hasOwnProperty("rev_parent_id"))) return false; // upload files
    if (e.meta.stream === "mediawiki.revision-create" && e.database === "wikidatawiki" && e.is_redirect === true) return false; // redirects on wikidata
    if (e.meta.stream === "mediawiki.recentchange" && (e.type !== "edit" && e.type !== "new")) return false; // cats, uploads, logs
    if ((e.hasOwnProperty("title") && sandboxlist.includes(e.title)) || (e.hasOwnProperty("page_title") && sandboxlist.includes(e.page_title))) return false; // sandboxes
    if ((e.hasOwnProperty("user") && globals.includes(e.user)) || (e.hasOwnProperty("performer") && e.performer.hasOwnProperty("user_text") && globals.includes(e.performer.user_text))) return false; // global users
    if (e.meta.stream === "mediawiki.revision-create" && (e.hasOwnProperty("performer") && e.performer.hasOwnProperty("user_is_bot") && e.performer.user_is_bot === true)) return false; // bot
    if (e.meta.stream === "mediawiki.recentchange" && (e.hasOwnProperty("bot") && e.bot === true)) return false; // mark as bot edit
    if (e.meta.stream === "mediawiki.recentchange" && e.patrolled === true) return false; // patrolled
    if (e.meta.stream === "mediawiki.revision-create" && (e.rev_is_revert === true && e.rev_revert_details.rev_revert_method === "rollback")) return false; // rollback
    if (e.meta.stream === "mediawiki.revision-create" && (Object.values(e.performer.user_groups).some(v => groupFilt.indexOf(v) !== -1) === true)) return false; // groups
    return true;
}

function customFilter(e, filt, nick) {
    if (e.performer.user_text === nick) return false;
    if (filt.anons === 0 && e.performer.user_is_anon === true) return false;
    if (filt.registered === 0 && e.performer.user_is_anon === false) return false;
    if (filt.new === 0 && e.is_new === true) return false;
    if (filt.onlynew === 1 && e.is_new === false) return false;
    if (e.performer.user_is_anon === false && e.performer.user_registration_dt === null && filt.edits <= e.performer.user_edit_count) return false;
    if (!filt.namespaces.split(',').includes(e.namespace.toString()) && filt.namespaces.length !== 0) return false;
    if (filt.wikiwhitelist.split(',').includes(e.wiki)) return false;
    if (filt.userwhitelist.split(',').includes(e.performer.user_text)) return false;
    if (e.performer.user_is_anon === false && e.performer.user_registration_dt !== null) {
        const d = new Date();
        var dateDiff = (Date.UTC(d.getUTCFullYear(), d.getUTCMonth(), d.getUTCDate(), d.getUTCHours(), d.getUTCMinutes(), d.getUTCSeconds(), d.getUTCMilliseconds()) - Date.parse(e.performer.user_registration_dt)) / 1000 / 60 / 60 / 24;
        if (filt.edits <= e.performer.user_edit_count && dateDiff >= filt.days) return false;
    }
    if (typeof sandboxlist[e.wiki] !== "undefined" && sandboxlist[e.wiki].split(',').includes(e.title)) return false;
    if (e.ORES !== null && filt.ores !== 0 && filt.ores > e.ORES.score) return false;
    return (filt.wikis.split(',').includes(e.wiki)) ||
        (filt.local_wikis.includes(e.wiki) && filt.isGlobal === false) ||
        (swmt.includes(e.wiki) && filt.swmt === 1 && (filt.isGlobal === true || filt.isGlobalModeAccess === true)) ||
        (lt300.includes(e.wiki) && filt.lt300 === 1 && (filt.isGlobal === true || filt.isGlobalModeAccess === true));
}

async function checkCVN(username, is_anon) {
    return new Promise(resolve => {
        if (is_anon === true) resolve(true);
        else if (typeof cacheCVN.get(String(username)) !== "undefined") { resolve(cacheCVN.get(String(username))); }
        else
            request("https://cvn.wmflabs.org/api.php?users=" + encodeURIComponent(username).replace(/'/g, '%27'), { json: true, headers: { "User-Agent": userAgent } }, (err, res) => {
                if (err) resolve(true); else {
                    var cvn = res.body;
                    (cvn.hasOwnProperty("users") && cvn.users.hasOwnProperty(username) && cvn.users[username].hasOwnProperty("type") && cvn.users[username].type === "whitelist") ? resolve(false) : resolve(true);
                    (cvn.hasOwnProperty("users") && cvn.users.hasOwnProperty(username) && cvn.users[username].hasOwnProperty("type") && cvn.users[username].type === "whitelist") ? cacheCVN.set(String(username), false) : cacheCVN.set(String(username), true);
                }
            });
    }).catch(function(err) {
        logger.debug("checkCVN promise error: " + err);
    });
}

// Get labels for Wikidata elements. Instead "Q2735363" we will see title of article
function getWikidataTitle (e) {
    return new Promise(resolve => {
        if (e.wiki === "wikidatawiki" && (e.namespace === 120 || e.namespace === 0) && (e.title.search(/^P\d*?$/gm) !== -1 || e.title.search(/^Q\d*?$/gm) !== -1)) {
            var urlWD = "https://www.wikidata.org/w/api.php?action=wbgetentities&ids=" + encodeURIComponent(e.title) + "&props=labels&languages=en&format=json&utf8=1";
            request(urlWD, { json: true, headers: { "User-Agent": userAgent } }, (err, res) => {
                if (err) resolve(null); else {
                    var wikidatatitle = res.body;
                    if ((wikidatatitle.hasOwnProperty("entities")) && (wikidatatitle.entities.hasOwnProperty(e.title)) &&
                        (wikidatatitle.entities[e.title].hasOwnProperty("labels")) &&
                        (wikidatatitle.entities[e.title].labels.hasOwnProperty("en")) &&
                        (wikidatatitle.entities[e.title].labels.en.hasOwnProperty("value")) &&
                        (wikidatatitle.entities[e.title].labels.en.value !== null || wikidatatitle.entities[e.title].labels.en.value !== ""))
                        resolve(wikidatatitle.entities[e.title].labels.en.value);
                    else resolve(null);
                }
            });
        } else resolve(null);
    }).catch(function(err) {
        logger.debug("getWikidataTitle promise error: " + err);
    });
}

function normalizeArray(e) {
    var normArray = {
        "wiki": "", "domain": "", "uri": "", "title": "", "wikidata_title": null, "id": "", "namespace": "",
        "namespace_name": null, "new_id": "",  "old_id": null, "new_len": "", "old_len": null, "is_new": false,
        "is_minor": "",  "is_redirect": "", "is_revert": false, "comment": "", "performer": {}, "ORES": null,
        "timestamp": "" };
    normArray.wiki = (e.hasOwnProperty("database")) ? e.database : e.wiki;
    normArray.domain = e.meta.domain;
    normArray.uri = e.meta.uri;
    normArray.id = (e.hasOwnProperty("page_id")) ? e.page_id : e.id;
    normArray.title = (e.hasOwnProperty("page_title")) ? e.page_title : e.title;
    normArray.namespace = (e.hasOwnProperty("page_namespace")) ? e.page_namespace : e.namespace;
    normArray.new_id = (e.hasOwnProperty("rev_id")) ? e.rev_id : e.revision.new;
    normArray.timestamp = (e.hasOwnProperty("rev_timestamp")) ? e.rev_timestamp : e.timestamp;
    normArray.new_len = (e.hasOwnProperty("rev_len")) ? e.rev_len : e.length.new;
    normArray.is_redirect = (e.hasOwnProperty("page_is_redirect")) ? e.page_is_redirect : normArray.is_redirect;
    normArray.comment = e.comment;
    normArray.is_revert = (e.hasOwnProperty("rev_is_revert")) ? e.rev_is_revert : normArray.is_revert;
    normArray.performer = (e.hasOwnProperty("performer")) ? e.performer : normArray.performer;
    normArray.is_minor = (e.hasOwnProperty("rev_minor_edit")) ? e.rev_minor_edit : e.minor;
    if (e.hasOwnProperty("performer"))
        e.performer.user_is_anon = (Object.values(e.performer.user_groups).indexOf("user") === -1);
    if ((!e.hasOwnProperty("revision") && !e.hasOwnProperty("rev_parent_id")) || (e.hasOwnProperty("revision") && !e.revision.hasOwnProperty("old")))
        normArray.is_new = true;
    else normArray.old_id = (e.hasOwnProperty("rev_parent_id")) ? e.rev_parent_id : e.revision.old;
    if (e.meta.stream === "mediawiki.recentchange")
        normArray.old_len = (e.length.hasOwnProperty("old")) ? e.length.old : normArray.old_len;
    if (e.hasOwnProperty("page_namespace")) {
        normArray.namespace_name = (e.page_namespace >= 0 && e.page_namespace <= 15) ? namespaces[e.page_namespace]
            : "<font color='brown'>Non-canon (" + e.page_namespace + ")</font>";
        if (e.database === "wikidatawiki") {
            if (normArray.namespace_name === "<font color='brown'>Non-canon (146)</font>") normArray.namespace_name = "Lexeme";
            if (normArray.namespace_name === "<font color='brown'>Non-canon (122)</font>") normArray.namespace_name = "Query";
            if (normArray.namespace_name === "<font color='brown'>Non-canon (120)</font>")  if (normArray.itle.search(/^P\d*?$/gm) !== -1) normArray.namespace_name = "Property";
        }
        if (e.database === "enwiki") {
            if (normArray.namespace_name === "<font color='brown'>Non-canon (118)</font>") normArray.namespace_name = "Draft";
            if (normArray.namespace_name === "<font color='brown'>Non-canon (119)</font>") normArray.namespace_name = "Draft talk";
        }
    }

    return normArray;
}

function getParams(w) {
    return new Promise(resolve => {
        request('https://swviewer.toolforge.org/php/getFilt.php?preset_name=' + encodeURIComponent(w.preset).replace(/'/g, '%27') +
            '&token_proxy=' + token + '&username=' + encodeURIComponent(w.nickName).replace(/'/g, '%27'), { json: true, headers: { "User-Agent": userAgent } }, (err, res) => {
            if (err) { resolve(false); return; }
            if (res.body.hasOwnProperty("error")) { resolve(false); return; }

            var filt = [];
            filt.swmt =  (parseInt(res.body.swmt) === 1 || parseInt(res.body.swmt) === 2) ? 1 : 0;
            filt.lt300 = (parseInt(res.body.users) === 1 || parseInt(res.body.users) === 2) ? 1 : 0;
            filt.edits = parseInt(res.body.editcount);
            filt.days = parseInt(res.body.regdays);
            filt.registered = parseInt(res.body.registered);
            filt.anons = parseInt(res.body.onlyanons);
            filt.new = parseInt(res.body.new);
            filt.onlynew = parseInt(res.body.onlynew);
            filt.ores = parseInt(res.body.oresFilter);
            filt.namespaces = (res.body.namespaces !== null) ? res.body.namespaces : "";
            filt.wikiwhitelist = (res.body.wlprojects !== null) ? res.body.wlprojects : "";
            filt.userwhitelist = (res.body.wlusers !== null) ? res.body.wlusers : "";
            filt.wikis = (res.body.blprojects !== null) ? res.body.blprojects : "";
            filt.local_wikis = (res.body.local_wikis !== null) ? res.body.local_wikis : "";
            filt.isGlobalModeAccess = (parseInt(res.body.isGlobalModeAccess) === 1);
            filt.isGlobal = (parseInt(res.body.isGlobal) === 1);

            resolve(filt);
        });
    }).catch(function(err) {
        logger.debug("getParams promise error: " + err);
    });
}

function getGeneralList() {
    var generalListPrepare = [];
    let swmtCheck = false; let lt300Check = false;

    wss.clients.forEach(function(ws) {
        if (ws.pause !== true) {
            if (ws.filt.swmt === 1 && (ws.filt.isGlobal === true || ws.filt.isGlobalModeAccess === true))
                swmtCheck = true;
            if (ws.filt.lt300 === 1 && (ws.filt.isGlobal === true || ws.filt.isGlobalModeAccess === true))
                lt300Check = true;

            ws.filt.wikis.split(',').forEach(function (el) {
                if (!generalListPrepare.includes(el) && !ws.filt.wikiwhitelist.split(',').includes(el)) generalListPrepare.push(el);
            });

            if (ws.filt.isGlobal === false)
                ws.filt.local_wikis.split(',').forEach(function (el) {
                    if (!generalListPrepare.includes(el) && !ws.filt.wikiwhitelist.split(',').includes(el)) generalListPrepare.push(el);
                });
        }
    });
    if (swmtCheck === true)
        swmt.forEach(function (el) {
            if (!generalListPrepare.includes(el)) generalListPrepare.push(el);
        });
    if (lt300Check === true)
        lt300.forEach(function (el) {
            if (!generalListPrepare.includes(el)) generalListPrepare.push(el);
        });
    generalList = generalListPrepare;
}

setInterval(function() {
    Object.keys(storage).forEach(function(key) {
        var l = (!storage[key].hasOwnProperty(0)) ? storage[key] : storage[key][0];
        if (l.hasOwnProperty("time"))
            if (l.time <= (Date.now() - 1000 * 60 * 3))
                delete storage[key];
    });
}, 5000);

setInterval(function() {
    eventPerMin = eventPerMinPrepare;
    eventPerMinPrepare = 0;
}, 60000);