const epilogue = require('epilogue');
const bodyParser = require('body-parser');
const express = require('express');
const mongoose = require('mongoose');
const session = require('express-session');
const app = express();
const path = require('path');
const http = require('http').Server(app);
const ejs = require('ejs');

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

mongoose.connect('mongodb://localhost:27017/mydatabase', { useNewUrlParser: true, useUnifiedTopology: true })
  .then(() => {
    console.log('Підключено до бази даних');
  })
  .catch(err => {
    console.error('Помилка підключення до бази даних', err);
  });

  app.post('/signup', (req, res) => {
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
  
      db.save('users', user);
  
      req.session.logged_user = user;
  
      res.redirect('/patregodp/');
    } else {
      res.render('signup', { errors });
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
  res.render(`${views}/signup`);
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