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

//R.setup('mysql:host=localhost;dbname=patrego', 'root', '');

/*const session = require('express-session');
const express = require('express');
const app = express();

app.use(session({
    secret: 'your-secret-key',
    cookie: {
        maxAge: 100 * 24 * 60 * 60 * 1000 // 100 days
    }
}));

app.use((req, res, next) => {
    res.setHeader('Content-Type', 'text/html; charset=utf-8');
    next();
});

// Check if the user is not banned
if (req.session.logged_user) {
    // Check if the user is not banned
    const userCount = R.count('users', 'id = ? and status = 0', [req.session.logged_user.id]);
    if (userCount) {
        // Destroy session
        req.session.destroy();
        // Redirect to the main page
        res.redirect('/patregodp/?banned');
    }
} else if (!req.LOGIN) {
    res.redirect('/patregodp/');
} */