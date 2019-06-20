'use strict';

const bluebird = require('bluebird');
const fs = bluebird.promisifyAll(require('fs'));
const moment = require('moment');
const exec = bluebird.promisify(require('child_process').exec);

const signCSR = async (serviceName) => {
  await exec(`cd ${process.env.PKI_WORK_DIR} && ./pki-sign-csr service ${serviceName} intermediate-ca ${process.env.KEYSTORE_PASS}`);
}

const isCertificateExisted = async (serviceName) => {
  try {
    await fs.accessAsync(`${process.env.PKI_CSR_DIR}/${serviceName}/${serviceName}.crt`,fs.F_OK);
    return true;
  } catch(err) {
    console.log(err);
    return false;
  }
}

const getExpiryDate = (content) => {
  const expiryString = "Not After : ";
  const expiryStringIndex = content.indexOf(expiryString);
  const startExpiryStringIndex = parseInt(expiryStringIndex) + parseInt(expiryString.length);
  const limitExpiryStringIndex = content.indexOf('\n',startExpiryStringIndex+1);
    
  return content.slice(startExpiryStringIndex,limitExpiryStringIndex);
}

const isCertificateNeedRenewal = async (serviceName) => {
  const content = await fs.readFileAsync(`${process.env.PKI_CSR_DIR}/${serviceName}/${serviceName}.crt`);
  const expiryDateString = getExpiryDate(content.toString());
  const expiryDate = moment(expiryDateString, "MMM DD HH:mm:ss YYYY GMT");
    
  if (moment().isBefore(expiryDate.subtract(1, 'days'))) {
      return false;
  }
  return true;
}

exports.handleCSR = async (req,res) => {
  const serviceName = req.body.service_name;
  const serviceCSRDir = `${process.env.PKI_CSR_DIR}/${serviceName}`;
  const csrFile = `${serviceCSRDir}/${serviceName}.csr`;
  const sourceCertFile = `${process.env.PKI_CSR_DIR}/${serviceName}/${serviceName}.tar.gz`;

  let currentTime = moment().toISOString();
  console.log(`[${currentTime}] Received signing request for ${serviceName}`);

  try {
    await fs.mkdirAsync(serviceCSRDir,{recursive: true});
  } catch(err) {
    console.log(err);
    return res.status(500).send("Failed creating CSR directory");
  }

  try {
    await fs.writeFileAsync(`${csrFile}`,req.files.csr.data);
  } catch(err) {
    console.log(err);
    return res.status(500).send("Failed writing CSR");
  }

  try {
    await signCSR(serviceName);
    currentTime = moment().toISOString();
    console.log(`[${currentTime}] Success signing CSR for ${serviceName}`);
    res.download(sourceCertFile);
  } catch(err) {
    currentTime = moment().toISOString();
    console.log(`[${currentTime}] Failed signing CSR for ${serviceName}`);
    console.log(err);
    return res.status(500).send("Failed signing CSR");
  }
}

exports.getSigningStatus = async (req,res) => {
  const serviceName = req.params.serviceName;
   
  if(!await isCertificateExisted(serviceName)) {
    return res.status(404).end();
  }

  try {
    const isRenewalNeeded = await isCertificateNeedRenewal(serviceName);
    if(isRenewalNeeded) {
      return res.status(200).send("Certificate renewal needed");
    }
    return res.status(200).send("Certificate has been signed");
  } catch(err) {
    return res.status(500).send(err);
  }
}