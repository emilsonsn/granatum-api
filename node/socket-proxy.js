import { io } from 'socket.io-client';
import axios from 'axios';

// Conectar ao WebSocket da Evolution API usando socket.io
const socket = io('https://api.andradeengenhariaeletrica.com.br/teste123'); 

// Quando conectado
socket.on('connect', () => {
    console.log('Conectado ao WebSocket da Evolution API');
});

// Ouvir todos os eventos
socket.onAny((eventName, data) => {
    console.log(`Evento recebido: ${eventName}`, data);
    
    axios.post('http://localhost:8000/api/evolution-data', { event: eventName, data: data })
        .then(response => {
            console.log('Dados enviados para o Laravel:', response.data);
        })
        .catch(error => {
            console.error('Erro ao enviar dados para o Laravel:', error.message);
        });
});

// Desconectar
socket.on('disconnect', () => {
    console.log('Desconectado do WebSocket da Evolution API');
});
