'use strict';

const dotenv = require('dotenv').config();
const express = require('express');
const fileUpload = require('express-fileupload');
const csr = require('./handlers/csrHandler');
const crl = require('./handlers/crlHandler');
const auth = require('./middlewares/authMiddleware');
const app = express();
const csrRoute = express.Router();
const crlRoute = express.Router();

app.use(fileUpload());

csrRoute.use(auth.authByToken());
csrRoute.post('/',csr.uploadCSR);
csrRoute.get('/:serviceName/status',csr.getSigningStatus);

crlRoute.get('/:caTarget',crl.getCRL);

app.use('/csr',csrRoute);
app.use('/crl',crlRoute);

app.listen(process.env.PUBLIC_SERVER_PORT,"0.0.0.0", () => console.log("Public CA server is running"));