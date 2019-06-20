'use strict';

const bluebird = require('bluebird');
const fs = bluebird.promisifyAll(require('fs'));

exports.getCRL = async (req, res) => {
    const caTarget = req.params.caTarget;
    const crlFile = `${process.env.PKI_WORK_DIR}/pki/${caTarget}/ca.crl`;

    try {
        await fs.accessAsync(crlFile,fs.F_OK);
    } catch(err) {
        console.log(err);
        return res.status(404).send(`CRL file for ${caTarget} not found`);
    }
    
    return res.download(crlFile);
}