const { SerialPort } = require('serialport');
const { ReadlineParser } = require('@serialport/parser-readline');
const mysql = require('mysql2/promise');
const WebSocket = require('ws');

const portName = '/dev/cu.usbmodem0E230C491';
const dbConfig = {
  host: 'mysql-gusto.alwaysdata.net',
  user: 'gusto',
  password: 'RestoGustoG5',
  database: 'gusto_g5'
};

// Configuration for cleanup
const CLEANUP_CONFIG = {
  // Option 1: Keep records for X days (recommended)
  keepDays: 30,
  
  // Option 2: Keep only X most recent records
  maxRecords: 1000,
  
  // How often to run cleanup (every X new records)
  cleanupInterval: 100,
  
  // Which cleanup method to use: 'time' or 'count'
  method: 'count'
};

let recordCount = 0;

async function cleanupOldRecords(pool) {
  try {
    if (CLEANUP_CONFIG.method === 'time') {
      // Delete records older than specified days
      const sql = 'DELETE FROM `CapteurSon` WHERE temps < DATE_SUB(NOW(), INTERVAL ? DAY)';
      const [result] = await pool.execute(sql, [CLEANUP_CONFIG.keepDays]);
      console.log(`Cleaned up ${result.affectedRows} old records (older than ${CLEANUP_CONFIG.keepDays} days)`);
    } else if (CLEANUP_CONFIG.method === 'count') {
      // Keep only the most recent X records
      const sql = `
        DELETE FROM \`CapteurSon\` 
        WHERE id NOT IN (
          SELECT id FROM (
            SELECT id FROM \`CapteurSon\` 
            ORDER BY temps DESC 
            LIMIT ?
          ) AS recent_records
        )
      `;
      const [result] = await pool.execute(sql, [CLEANUP_CONFIG.maxRecords]);
      console.log(`Cleaned up ${result.affectedRows} old records (keeping ${CLEANUP_CONFIG.maxRecords} most recent)`);
    }
  } catch (err) {
    console.error('Cleanup error:', err);
  }
}

async function main() {
  const pool = mysql.createPool(dbConfig);

  // Start WebSocket server
  const wss = new WebSocket.Server({ port: 8080 });
  wss.on('connection', ws => {
    console.log('Client connected to WebSocket');
  });

  const port = new SerialPort({
    path: portName,
    baudRate: 9600
  });

  const parser = port.pipe(new ReadlineParser({ delimiter: '\n' }));

  // Filtering configuration
  const VALID_SENSOR_RANGE = {
    min: 10,    // minimum acceptable dB value
    max: 120    // optional, set according to your sensor limits
  };


  console.log(`Listening on port ${portName}...`);

  parser.on('data', async (line) => {
    console.log(`Received data: ${line}`);

    const sensorValue = parseFloat(line.trim());
    if (isNaN(sensorValue)) {
      console.warn('Received invalid data, ignoring...');
      return;
    }

    // Filter out outliers
    if (sensorValue < VALID_SENSOR_RANGE.min || sensorValue > VALID_SENSOR_RANGE.max) {
      console.warn(`Ignored outlier value: ${sensorValue} (outside range ${VALID_SENSOR_RANGE.min}-${VALID_SENSOR_RANGE.max})`);
      return;
    }

    try {
      // Insert into DB
      const sql = 'INSERT INTO `CapteurSon` (valeur) VALUES (?)';
      await pool.execute(sql, [sensorValue]);
      console.log(`Inserted sensor value ${sensorValue} into database.`);

      // Send live update to all WS clients
      const message = JSON.stringify({ valeur: sensorValue, temps: new Date() });
      wss.clients.forEach(client => {
        if (client.readyState === WebSocket.OPEN) {
          client.send(message);
        }
      });

      recordCount++;
      if (recordCount % CLEANUP_CONFIG.cleanupInterval === 0) {
        console.log('Running periodic cleanup...');
        await cleanupOldRecords(pool);
      }
    } catch (err) {
      console.error('Database error:', err);
    }
  });


  // Initial cleanup
  await cleanupOldRecords(pool);
}

main().catch(err => {
  console.error('Fatal error:', err);
});