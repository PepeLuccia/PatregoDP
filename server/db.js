// beautiful error handler
//require(__DIR__."/../../whoops/index.php");
const { MongoClient } = require('mongodb');

const url = 'mongodb://localhost:27017';
const dbName = 'patrego';

const client = new MongoClient(url);

let db;

async function connectToMongoDB() {
  try {
    await client.connect();
    db = client.db(dbName);
    console.log('Підключено до бази даних');
  } catch (error) {
    console.error('Помилка підключення до бази даних:', error);
  }
}

connectToMongoDB();

module.exports = db;