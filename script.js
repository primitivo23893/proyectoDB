const express = require('express'); 
const { Pool } = require('pg'); 
const path = require('path'); 
const bodyParser = require('body-parser');

const app = express(); 
const port = 3000;

// Middleware para parsear JSON
app.use(bodyParser.json());
app.use(express.static(path.join(__dirname)));

const pool = new Pool({ 
    user: 'admin', 
    host: 'localhost', 
    database: 'empleado', 
    password: '1234', 
    port: 5433
});   

app.get('/', (req, res) => { 
    res.sendFile(path.join(__dirname, 'index.html')); 
});

// Ruta de autenticación
app.post('/login', async (req, res) => {
    const { email, password } = req.body;

    try {
        // Consulta para verificar credenciales
        const query = 'SELECT * FROM usuarios WHERE email = $1 AND password = $2';
        const result = await pool.query(query, [email, password]);

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