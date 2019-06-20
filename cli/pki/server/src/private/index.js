'use strict';

const dotenv = require('dotenv').config();
const express = require('express');
const fileUpload = require('express-fileupload')
const app = express();
const csr = require('./handlers/csrHandler');
const crl = require('./handlers/crlHandler');

app.use(fileUpload());
app.use(express.json());

app.post('/csr',csr.handleCSR);
app.get('/csr/:serviceName/status',csr.getSigningStatus);
app.get('/crl/:caTarget',crl.getCRL);

app.listen(process.env.PRIVATE_SERVER_PORT, "127.0.0.1", () => console.log("Private CA server is running"));