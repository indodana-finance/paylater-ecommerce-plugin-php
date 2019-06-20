'use strict';

exports.authByToken = () => {
  return (req,res,next) => {
    const clientAuthToken = req.header('X-AUTH-TOKEN');
    if(clientAuthToken !== process.env.AUTH_TOKEN) {
      return res.status(401).send("Invalid token");
    }
    return next();
  }
}