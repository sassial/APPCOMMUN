const { SerialPort } = require('serialport');
const { ReadlineParser } = require('@serialport/parser-readline');
const mysql = require('mysql2/promise');

const portName = '/dev/cu.usbmodem0E230C491';

const dbConfig = {
  host: 'mysql-gusto.alwaysdata.net',
  user: 'gusto',
  password: 'RestoGustoG5',
  database: 'gusto_g5'
};

async function main() {
  const pool = mysql.createPool(dbConfig);

  const port = new SerialPort({
    path: portName,
    baudRate: 9600
  });

  const parser = port.pipe(new ReadlineParser({ delimiter: '\n' }));

  console.log(`Listening on port ${portName}...`);

  parser.on('data', async (line) => {
    console.log(`Received data: ${line}`);
  
    const sensorValue = parseFloat(line.trim());
    if (isNaN(sensorValue)) {
      console.warn('Received invalid data, ignoring...');
      return;
    }
  
    try {
      const sql = 'INSERT INTO `Capteur_Son` (valeur) VALUES (?)';
      const [result] = await pool.execute(sql, [sensorValue]);
  
      console.log(`Inserted sensor value ${sensorValue} into database.`);
    } catch (err) {
      console.error('Database error:', err);
    }
  });  
}

main().catch(err => {
  console.error('Fatal error:', err);
});