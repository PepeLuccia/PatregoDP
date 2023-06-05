// beautiful error handler
//require(__DIR__."/../../whoops/index.php");
const mysql = require('mysql');

const connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'patrego'
});

module.exports = connection;