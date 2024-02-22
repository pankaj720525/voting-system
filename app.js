const dotenv = require('dotenv');
const express = require('express');
const { createServer } = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

const { createClient } = require('redis');

dotenv.config();
const SOCKET_PORT = process.env.SOCKET_PORT;
const app = express();
const server = createServer(app);

const io = new Server(server, {
    cors: {
        origin: "*",
        methods: ["*"],
    },
});


server.listen(SOCKET_PORT, () => {
    console.log(`server running at http://localhost:${SOCKET_PORT}`);
});

io.on("connection", (socket) => {

    (async () => {

        const client = createClient({

        });

        const subscriber = client.duplicate();
        await subscriber.connect();

        await subscriber.subscribe(process.env.REDIS_PREFIX+'new:poll', (message) => {
            socket.emit('poll-added', message);
        });

        await subscriber.subscribe(process.env.REDIS_PREFIX+'poll:update', (message) => {
            socket.emit('poll-votes', message)
        });

        await subscriber.subscribe(process.env.REDIS_PREFIX+'poll:delete', (message) => {
            socket.emit('poll-delete', message)
        });
    })();

    socket.on('disconnect', async () => {
        console.log('Disconnect');
    });
});
