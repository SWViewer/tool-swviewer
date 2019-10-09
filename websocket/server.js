const fs = require('fs');
var token = fs.readFileSync('token.txt', 'utf8');
var request = require('request');
const WebSocket = require('ws');
const url = require('url');
var port = process.env.PORT || 9030;
var users = [];

const wss = new WebSocket.Server({ port: port, verifyClient: function(info, done){
            var nickName = url.parse(info.req.url, true).query.name;
            var userToken = url.parse(info.req.url, true).query.token;
		try {
			request({
				method: "POST",
				uri: "https://tools.wmflabs.org/swviewer/php/authTalk.php",
				headers: {"User-Agent": "SWViewer/1.3; swviewer@tools.wmflabs.org; auth of the Talk users"},
				form: { serverToken: token, userToken: userToken, username: nickName }
			}, function (error, response, body) {
				if (!body || response.statusCode !== 200 || error)
					return done(false, 403, "Error of auth; bad request");
				var auth = JSON.parse(body);
				if (auth["auth"] !== "true")
					return done(false, 403, "Token is not valid");
				else
					return done(true);
			});
		}
		catch (e) {
			return done(false, 403, "Error of auth; no connect");
		}
}
});

wss.on('connection', function(ws, req) {
    ws.nickName = url.parse('https://tools.wmflabs.org:9030' + req.url, true).query.name;
    ws.isAlive = true;
    users.push(ws);
    wss.clients.forEach(function(client) {
        if (client.readyState === WebSocket.OPEN && client !== ws) {
            client.send(JSON.stringify({"type": "connected", "nickname": ws.nickName}));
        }
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
	        if (client.readyState === WebSocket.OPEN) {
                    client.send(JSON.stringify({"type": "message", "nickname": ws.nickName, "text": msg.text}));
		}
	    });
        request({
		method: "POST",
		uri: "https://tools.wmflabs.org/swviewer/php/talkHistory.php",
		headers: {"User-Agent": "TheTalk talk history -- swviewer tool"},
		form: { action: "save", serverToken: token, username: ws.nickName, text: msg.text }
	});

        }

        if (msg.type === 'synch') {
	    wss.clients.forEach(function(client) {
	        if (client.readyState === WebSocket.OPEN && client !== ws) {
                    client.send(JSON.stringify({"type": "synch", "wiki": msg.wiki, "nickname": msg.nickname, "vandal": msg.vandal, "page": msg.page}));
		}
	    });
        }
    });
	
    ws.on('close', function() {
        for (var i in users) {
	        if (users[i] == ws && users[i]["nickName"] == ws.nickName)
	            users.splice(i, 1);
        }
	wss.clients.forEach(function(client) {
	    if (client.readyState === WebSocket.OPEN && client !== ws) {
	         client.send(JSON.stringify({"type": "disconnected", "clients": usersName(users), "client": ws.nickName}));
	    }
	});
    });
});

const interval = setInterval(function ping() {
  wss.clients.forEach(function(ws) {
    if (ws.isAlive === false)
        return ws.terminate();
    ws.isAlive = false;
    ws.ping(function() {});
  });
}, 2500000);

usersName = function(clientsArray) {
    var usersList = [];
    for (var i in clientsArray)
        usersList.push(clientsArray[i]["nickName"]);
    return usersList.join();
}