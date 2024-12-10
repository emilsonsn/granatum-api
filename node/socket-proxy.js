import { io } from 'socket.io-client';
import axios from 'axios';

const instances = ['escritorio', 'teste123', 'mel'];

const baseUrl = "https://app.andradeengenhariaeletrica.com.br:3001/api/evolution-data";
// const baseUrl = "http://localhost:8000/api/evolution-data";

instances.forEach((instance) => {
    const socket = io(`https://api.andradeengenhariaeletrica.com.br/${instance}`); // Conecta com a instância

    socket.on('connect', () => {
        console.log(`Conectado ao WebSocket da instância: ${instance}`);
    });

    socket.onAny((eventName, data) => {
        console.log(`Evento recebido (${instance}): ${eventName}`, data);

        axios.post(baseUrl, { event: eventName, data, instance })
            .then(response => {
                console.log(`Resposta recebida (${instance}):`, response.data);
            })
            .catch(error => {
                console.error(`Erro ao enviar dados (${instance}):`, error.message);
            });
    });

    socket.on('disconnect', () => {
        console.log(`Desconectado do WebSocket da instância: ${instance}`);
    });
});
