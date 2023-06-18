var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

require('dotenv').config();
var redisPort = process.env.REDIS_PORT;
var redisHost = process.env.REDIS_HOST;

var Redis = require('ioredis');
var redis = new Redis(redisPort, redisHost);
redis.subscribe('private-chat', function(err, count) {
    console.log(err)
    console.log(count)
});
redis.on('message', function(channel, message) {
    console.log('Message Recieved: ' + message);
    console.log('Message Recieved: ' + channel);
    message = JSON.parse(message);
    io.emit(channel + ':' + message.data.message.user_id, message.data);
});
http.listen(3000, function(){
    console.log('Listening on Port 3000');
});