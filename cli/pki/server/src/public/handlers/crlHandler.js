'use strict';

const superagent = require('superagent');

const privatePort = process.env.PRIVATE_SERVER_PORT;

exports.getCRL = async (req,res) => {
    const caTarget = req.params.caTarget;

    try {
        const crlFile = await superagent
                        .get(`http://127.0.0.1:${privatePort}/crl/${caTarget}`);
        return res.end(Buffer.from(crlFile.body,'binary'));
    } catch(err) {
        console.log(err.response.text);
        return res.status(500).send(err.response.text);
    }
}