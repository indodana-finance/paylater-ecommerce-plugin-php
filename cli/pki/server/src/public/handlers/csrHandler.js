'use strict';

const superagent = require('superagent');

const privatePort = process.env.PRIVATE_SERVER_PORT;

exports.uploadCSR = async (req,res) => {
  const serviceName = req.body.service_name;
  const csrContent = Buffer.from(req.files.csr.data,'binary');
  let signingResult = null 
  
  try {
    signingResult = await superagent
          .post(`http://127.0.0.1:${privatePort}/csr`)
          .field('service_name', serviceName)
          .attach("csr",csrContent,`${serviceName}.csr`);
    return res.end(Buffer.from(signingResult.body,'binary'));
  } catch(err) {
    console.log(err.response.text);
    return res.status(500).send(err.response.text);
  }
}

exports.getSigningStatus = async (req,res) => {
  const serviceName = req.params.serviceName;
  let certificateStatus = null;
  
  try {
    certificateStatus = await superagent.get(`http://127.0.0.1:${privatePort}/csr/${serviceName}/status`);
    return res.status(certificateStatus.status).send(certificateStatus.text);
  } catch(err) {
    if(err.response.status === 404) {
      return res.status(404).send("Certificate did not exist");
    } else {
      return res.status(500).send(err.response.text);
    }
  }
}