const express = require('express'); 
const { Pool } = require('pg'); 
const path = require('path'); 
const bodyParser = require('body-parser');

//Env
require('dotenv').config();

const app = express(); 
const port = 3000;

// Middleware para parsear JSON
app.use(bodyParser.json());
app.use(express.static(path.join(__dirname)));

const DB_HOST = process.env.DB_HOST;
const DB_USER = process.env.DB_USER;
const DB_PASS = process.env.DB_PASS;
const DB_NAME = process.env.DB_NAME;
const DB_PORT = process.env.DB_PORT;

const pool = new Pool({ 
    user: DB_USER, 
    host: DB_HOST, 
    database: DB_NAME,
    password: DB_PASS,
    port: DB_PORT 

});   

app.get('/', (req, res) => { 
    res.sendFile(path.join(__dirname, 'index.html')); 
});

// Ruta de autenticación
app.post('/login', async (req, res) => {
    const { usuario, contrasena } = req.body;

    try {
        // Consulta para verificar credenciales
        const query = 'SELECT * FROM usuario WHERE usuario = $1 AND contrasena = $2';
        const result = await pool.query(query, [usuario, contrasena]);

        if (result.rows.length > 0) {
            res.json({ success: true, message: 'Inicio de sesión exitoso' });
        } else {
            res.json({ success: false, message: 'Credenciales incorrectas' });
        }
    } catch (error) {
        console.error('Error de autenticación:', error);
        res.status(500).json({ success: false, message: 'Error en el servidor' });
    }
});

app.listen(port, () => { 
    console.log(`Servidor iniciado en el puerto ${port}`); 
});








/*
// Ruta para obtener empleados
app.get('/empleados', async (req, res) => { 
    try {
        const query = 'SELECT * FROM empleado;'; 
        const result = await pool.query(query);
        const empleados = result.rows; 
        res.json(empleados); 
    } catch (error) {
        console.error('Error occurred:', error); 
        res.status(500).send('Ocurrió un error al recuperar los datos.'); 
    }
});
app.get('/numero', async (req, res) =>{
    try {
        const numero = req.query.numero;
        console.log(numero)
        if (parseInt(numero) >= 60) {
            res.send('1');  // Si aprueba (por ejemplo, >= 50)
        } else {
            //res.send('0');  // Si reprueba (menor a 50)
            res.send(numero)
        }
    } catch (error) {
        console.error('Error occurred:', error)
    }

});


app.listen(port, () => { 
    console.log(`Servidor iniciado en el puerto ${port}`); 
});

*/