const epilogue = require('epilogue');
const bodyParser = require('body-parser');
const express = require('express');
const mongoose = require('mongoose');
const session = require('express-session');
const app = express();
const path = require('path');
const http = require('http').Server(app);
const ejs = require('ejs');
const ejsLint = require('ejs-lint');
const { MongoClient } = require('mongodb');

app.set('view engine', 'ejs');
app.use(express.urlencoded({ extended: true }));
app.set('views', path.join(__dirname, 'views'));
app.locals.path = require('path');
const db = require('./db');

const port = 3000;
const views = path.join(__dirname, '/../src/views');

// Static Files
app.use(express.static(__dirname + '/../public'));

// Конфігурація сесії
app.use(session({
  secret: 'your-secret-key',
  cookie: {
    maxAge: 100 * 24 * 60 * 60 * 1000 // 100 days
  },
  resave: false,
  saveUninitialized: false
}));

MongoClient.connect('mongodb://localhost:27017/mydatabase', { useNewUrlParser: true, useUnifiedTopology: true })
  .then(() => {
    const db = client.db(dbName);
    console.log('Підключено до бази даних');
    client.close(); // Закриваємо підключення до MongoDB при завершенні
  })
  .catch(err => {
    console.error('Помилка підключення до бази даних', err);
  });

  app.post('/signup', (req, res) => {
    const { login, email, phonenum, pass, pass_2 } = req.body;
    const data = req.body;
  
    let errors = [];
  
    if (data.login.trim() === '') {
      errors.push('Введіть логін!');
    }
  
    if (data.email.trim() === '') {
      errors.push('Введіть e-mail!');
    }
  
    if (data.pass === '') {
      errors.push('Введіть пароль!');
    }
  
    if (data.pass_2 !== data.pass) {
      errors.push('Паролі не співпадають!');
    }
  
    const existingUserLogin = db.findOne('users', { login: data.login });
    const existingUserEmail = db.findOne('users', { email: data.email });
  
    if (existingUserLogin) {
      errors.push('Користувач з таким логіном вже існує!');
    }
  
    if (existingUserEmail) {
      errors.push('Користувач з такою поштою вже існує!');
    }
  
    if (errors.length === 0) {
      const user = {
        login: data.login,
        email: data.email,
        phonenum: data.phonenum,
        pass: data.pass,
        reg_date: Date.now(),
        status: 1
      };
      user.save()
        .then(() => {
          res.redirect('/success'); // Перенаправлення після успішної реєстрації
        })
        .catch(err => {
          console.error(err);
          res.redirect('/error'); // Перенаправлення у разі помилки
        });
  
      db.save('users', user);
  
      req.session.logged_user = user;
  
      res.redirect('/patregodp/');
      res.render('signup', { data: { login: '', email: '', phonenum: '', pass: '' , pass_2: '' }, errors: [] });
    } else {
      res.render('signup', { data, errors });
    }
  });

// === ROUTES === //
app.get('/', function (req, res) {
  res.render(`${views}/index`);
});

app.get('/about', (req, res) => {
  res.render(`${views}/about`);
});

app.get('/signup', (req, res) => {
  res.render(`${views}/signup`, { data: {} });
});

app.get('/login', (req, res) => {
  const errors = ['Помилка 1', 'Помилка 2'];
  res.render(`${views}/login`, { errors });
});

// Роут для виходу з системи та перенаправлення
app.get('/logout', (req, res) => {
  req.session.destroy(() => {
    res.redirect('/');
  });
});

// Обробка помилки 404
app.use((req, res, next) => {
  res.status(404).send('Сторінку не знайдено');
});

http.listen(port, () => console.log(`Сервер запущено на порті ${port}`));

process.on('uncaughtException', (err) => console.error('Caught exception: ' + err));